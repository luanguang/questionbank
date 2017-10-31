<?php

namespace App\Http\Controllers\Admin;

use App\Models\Question;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $this->validate($request, [
            'content'       =>  'string',
            'category_id'   =>  'integer|min:0',
            'is_pic'        =>  'boolean',
            'is_plural'     =>  'boolean',
            'choice_num'    =>  'integer|min:0',
            'user_id'       =>  'integer|min:0'
        ]);

        $question = new Question;
        $search   = array_filter($request->only('content', 'category_id', 'is_pic', 'is_plural', 'choice_num', 'user_id'), function ($var) {
            return !empty($var);
        });

        foreach ($search as $key => $value) {
            if (in_array($key, ['category_id', 'is_pic', 'is_plural', 'choice_num', 'user_id'])) {
                $question = $question->where($key, $value);
            } elseif (in_array($key, ['content'])) {
                $question = $question->where($key, 'LIKE', '%'.$value.'%');
            }
        }
        $question = $question->orderBy('id', 'DESC')->paginate(20);

        return $question->tojson();
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'content'           =>  'required|string',
            'is_pic'            =>  'required|boolean',
            'choice_num'        =>  'required|integer|min:0',
            'category_id'       =>  'required|integer|min:0'
        ]);

        $question = Question::create($request, [
            'content'           =>  $request->input('content'),
            'is_pic'            =>  $request->input('is_pic'),
            'choice_num'        =>  $request->input('choice_num'),
            'category_id'       =>  $request->input('category_id')
        ]);

        return $question->tojson();
    }

    public function destroy($question_id)
    {
        $question = Question::findOrFail($question_id);
        $question->delete();

        return;
    }
}