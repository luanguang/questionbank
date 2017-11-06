<?php

namespace App\Http\Controllers\Web;

use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AnswerController extends Controller
{
    public function show($question_id)
    {
        $question       = Question::findOrFail($question_id);
        $answers        = [];
        $all_answers    = Answer::where('question_id', $question_id)->get();
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
        return $answers;
    }

    public function store(Request $request, $question_id)
    {
        $this->validate($request, [
            'choice'        =>  'required|string',
            'detail'        =>  'string',
        ]);

        $answer = Answer::create($request, [
            'question_id'   =>  $question_id,
            'choice'        =>  $request->input('choice'),
            'is_right'      =>  $request->input('is_right'),

        ]);
    }

    public function destroy($answer_id)
    {
        $answer = Answer::findOrFail($answer_id);
        $answer->delete();

        return;
    }


}
