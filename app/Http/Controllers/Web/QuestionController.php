<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\RenderException;
use App\Models\History;
use App\Models\Question;
use App\Models\Choice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $this->validate($request, [
            'category_id'   =>  'integer|min:0',
            'title'         =>  'string',
            'is_pic'        =>  'boolean',
            'user_id'       =>  'integer|min:0',
            'choice_num'    =>  'integer|min:0',
        ]);

        $search = array_filter($request->only('title', 'category_id', 'is_pic', 'user_id', 'choice_num'), function ($var) {
            return !empty($var);
        });

        $questions = new Question;
        foreach ($search as $key => $value) {
            if (in_array($key, ['category_id', 'is_pic', 'user_id', 'choice_num'])) {
                $questions = $questions->where($key, $value);
            } elseif (in_array($key, ['title'])) {
                $questions = $questions->where($key, 'LIKE', '%'.$value.'%');
            }
        }
        $questions = $questions->orderBy('id', 'DESC')->paginate(30);

        return $questions->toJson();
    }

    public function show($question_id)
    {
        $question       = Question::findOrFail($question_id);
        $answers        = [];
        $all_answers    = Choice::where('question_id', $question_id)->get();
        $right_answer   = $all_answers->where('is_right', 1)->random(1);
        $num            = count($right_answer);
        $mistake_answer = $all_answers->where('is_right', 0)->random($question->choice_num - $num);

        foreach ($right_answer as $value) {
            $answers[] = $value->only(['id', 'question_id', 'choice']);
        }
        foreach ($mistake_answer as $val) {
            $answers[] = $val->only(['id', 'question_id', 'choice']);
        }
        shuffle($answers);

        return response()->json(['question' => $question, 'answers' => $answers]);
    }

    public function answer(Request $request, $question_id)
    {
        $this->validate($request, [
            'choice.*'    =>  'required|integer|min:0'
        ]);

        $question       = Question::with('answers')->findOrFail($question_id);
        $right_answer   = $question->answers->where('is_right', 1)->pluck('id')->toArray();
        $choices        = $request->input('choice');
        $result =   array_diff($choices, $right_answer);
        History::create([
            'user_id'       =>  Auth::user()->id,
            'question_id'   =>  $question->id,
            'category_id'   =>  $question->category_id,
            'created_at'    =>  Carbon::now()
        ]);
        if (!empty($result)) {
            return response()->json(['result' => '错误']);
        } else {
            return response()->json(['result' => '正确']);
        }
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title'         =>  'required|string',
            'is_pic'        =>  'boolean',
            'score'         =>  'integer|min:0',
            'difficult'     =>  'required|in:1,2,3,4,5',
            'category_id'   =>  'required|integer|min:0|exists:categories,id',
            'choice.*'      =>  'required|string',
            'is_right'      =>  'required|integer|min:0'
        ]);

        if (count($request->input('choice')) < 2 || Auth::user()->is_admin != 1 || Auth::user()->profession != 'teacher') {
            throw new RenderException();
        }

        $question = Question::create([
            'title'         =>  $request->input('title'),
            'is_pic'        =>  $request->input('is_pic'),
            'score'         =>  $request->input('score'),
            'difficult'     =>  $request->input('difficult'),
            'category_id'   =>  $request->input('category_id')
        ]);

        $choice  = $request->input('choice');
        $right   = $request->input('is_right');
        $choices = [];
        for ($i=0; $i<count($choice); $i++) {
            $choices[$i]['question_id'] = $question->id;
            $choices[$i]['content']     = $choice[$i];
            $choices[$i]['created_at']  = Carbon::now();
            $choices[$i]['is_right']    = 0;
        }
        foreach ($right as $value) {
            $choices[$value]['is_right']    = 1;
        }

        $choice = Choice::insert($choices);

        return response()->json(['question' => $question, 'choice' => $choice]);
    }

    public function getTest(Request $request)
    {
        $this->validate($request, [
            'category_id'   =>  'integer|min:0,exists:categories,id',
            'number'        =>  'required|integer|max:100',
            'difficult'     =>  'integer|in:1,2,3,4,5'
        ]);

        $test = Question::with(['answers' => function ($var) {return $var->select('id', '$question_id', 'content');}])
                        ->where(['category_id' => $request->input('category_id'), 'difficult' => $request->input('difficult')])
                        ->inRandomOrder()->limit($request->input('number'))->get();

        return response()->json(['test' => $test]);
    }

    public function getResult(Request $request)
    {
        $this->validate($request, [
            'choice.*'  => 'required|integer'
        ]);

        $choices = Choice::with('question')->whereIn('id', $request->input('choice'))->get();

        return response()->json(['choices' => $choices]);
    }
}
