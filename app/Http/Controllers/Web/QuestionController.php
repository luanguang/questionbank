<?php

namespace App\Http\Controllers\Web;

use App\Models\History;
use App\Models\Question;
use App\Models\Choice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    public function __construct()
    {

    }

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

}
