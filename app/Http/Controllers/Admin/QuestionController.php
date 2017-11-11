<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AnswerRequest;
use App\Models\Answer;
use App\Models\Question;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $this->validate($request, [
            'title'         =>  'string',
            'category_id'   =>  'integer|min:0',
            'is_pic'        =>  'boolean',
            'is_plural'     =>  'boolean',
            'choice_num'    =>  'integer|min:0',
            'user_id'       =>  'integer|min:0'
        ]);

        $question = new Question;
        $search   = array_filter($request->only('title', 'category_id', 'is_pic', 'user_id'), function ($var) {
            return !empty($var);
        });

        foreach ($search as $key => $value) {
            if (in_array($key, ['category_id', 'is_pic', 'user_id'])) {
                $question = $question->where($key, $value);
            } elseif (in_array($key, ['title'])) {
                $question = $question->where($key, 'LIKE', '%'.$value.'%');
            }
        }
        $question = $question->orderBy('id', 'DESC')->paginate(20);

        return response()->json(['question' => $question]);
    }

    public function store(AnswerRequest $request, $paper_id)
    {
        $question = Question::create([
            'title'         =>  $request->input('title'),
            'is_pic'        =>  $request->input('is_pic'),
            'category_id'   =>  $request->input('category_id'),
            'score'         =>  $request->input('score'),
            'user_id'       =>  Auth::user()->id,
            'paper_id'      =>  $paper_id
        ]);

        $choice  =   $request->input('choice');
        $right   =   $request->input('is_right');
        $choices = [];
        for ($i = 0; $i<count($choice); $i++) {
            $choices[$i]['choice']          = $choice[$i];
            $choices[$i]['question_id']     = $question->id;
            if ($i == $right[0]) {
                $choices[$i]['is_right']    = 1;
            } else {
                $choices[$i]['is_right']    = 0;
            }
            $choices[$i]['created_at']      = Carbon::now();
        }
        $choices = Answer::insert($choices);

        return response()->json(['choices' => $choices, 'question' => $question]);
    }

    public function update(Request $request, $question_id)
    {
        $this->validate($request, [
            'title'         =>  'string',
            'category_id'   =>  'integer',
            'score'         =>  'integer',
        ]);

        $question = Question::findOrFail($question_id);
        $question->update([
            'title'         =>  $request->input('title'),
            'category_id'   =>  $request->input('category_id'),
            'score'         =>  $request->input('score')
        ]);
        $answers = $question->answers->toArray();
        $choices = $request->input('choice');
        $right   = $request->input('is_right');
        for ($i = 0; $i<count($answers); $i++) {
            $ans = Answer::findOrFail($answers[$i]['id']);
            if ($right[0] == $i) {
                $ans->update([
                    'choice'        =>   $choices[$i],
                    'is_right'      =>  1
                ]);
            } else {
                $ans->update([
                    'choice'        =>  $choices[$i],
                    'is_right'      =>  0
                ]);
            }
        }

        return response()->json(['question' => $question]);
    }

    public function destroy($question_id)
    {
        $question = Question::findOrFail($question_id);
        $question->delete();
        Answer::where('question_id', $question_id)->delete();

        return response()->json(['success' => "删除成功", 'code' => 204]);
    }
}