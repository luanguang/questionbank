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
        if ($question->is_plural == 0) {
            $answers        = Answer::where('question_id', $question_id)->select('id', 'question_id', 'choice')->get();
            $right_answer   = $answers->where('is_right', 1)->toArray();
        } else {
            $right_answer   = Answer::where(['question_id' => $question_id, 'is_right' => 1])->select('id', 'question_id', 'choice')->get()->toArray();
            $num            = count($right_answer);
            $mistake_answer = Answer::where(['question_id' => $question_id, 'is_right' => 0])->select('id', 'question_id', 'choice')->inRandomOrder()->limit($question->choice_num - $num)->get()->toArray();
            $answers        = array_merge($right_answer, $mistake_answer);
        }

        return shuffle($answers)->tojson();
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
