<?php

namespace App\Http\Controllers\Web;

use App\Models\Question;
use App\Models\Answer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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

        return $question->tojson();
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'content'           =>  'required|string',
            'is_pic'            =>  'required|boolean',
            'choice_num'        =>  'required|integer',
            'right_num'         =>  'required_if:'
        ]);

        $question = Question::create([
            'content'           =>  $request->input('content'),
            'is_pic'            =>  $request->input('is_pic'),
            'choice_num'        =>  $request->input('choice_num')
        ]);

        return $question->tojson();
    }

    public function update(Request $request, $question_id)
    {
        $this->validate($request, [
            'content'           =>  'required|string',
            'is_plural'         =>  'required|boolean',
            'choice_num'        =>  'required|integer'
        ]);

        $question = Question::findOrFail($question_id);
        $question->update([
            'content'           =>  $request->input('content'),
            'is_plural'         =>  $request->input('is_plural'),
            'choice_num'        =>  $request->input('choice_num')
        ]);

        return $question->tojson();
    }

    public function destroy($question_id)
    {
        $question = Question::findOrFail($question_id);
        $answers  = Answer::where('question_id', $question_id);
        $question->delete();
        $answers->delete();

        return;
    }
}
