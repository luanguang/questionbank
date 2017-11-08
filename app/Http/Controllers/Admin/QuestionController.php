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
        $search   = array_filter($request->only('title', 'category_id', 'is_pic', 'is_plural', 'choice_num', 'user_id'), function ($var) {
            return !empty($var);
        });

        foreach ($search as $key => $value) {
            if (in_array($key, ['category_id', 'is_pic', 'is_plural', 'choice_num', 'user_id'])) {
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

        $choice =   $request->input('choice');
        foreach ($choice as &$value) {
            $value['question_id']   =  $question->id;
            $value['created_at']    =  Carbon::now()->addHours(8);
        }
        $choice = Answer::insert($choice);


        return response()->json(['question' => $question, 'choice' => $choice]);
    }

    public function update(Request $request, $question_id)
    {
        $this->validate($request, [
            'title'         =>  'string',
            'choice_num'    =>  'integer',
            'category_id'   =>  'integer',
            'score'         =>  'integer',
        ]);

        $question = Question::findOrFail($question_id);
        $question->update([
            'title'         =>  $request->input('title'),
            'choice_num'    =>  $request->input('choice_num'),
            'category_id'   =>  $request->input('category_id'),
            'score'         =>  $request->input('score')
        ]);

        $this->validate($request, [
            'answer_id'     =>  'integer'
        ]);

        $answer = Answer::findOrFail($request->input('answer_id'));
        $answer->update([
            'choice'        =>  $request->input('choice'),
            'detail'        =>  $request->input('detail')
        ]);

        return response()->json(['question' => $question, 'answer' => $answer]);
    }

    public function destroy($question_id)
    {
        $question = Question::findOrFail($question_id);
        $question->delete();
        Answer::where('question_id', $question_id)->delete();

        return response()->json(['success' => "删除成功", 'code' => 204]);
    }
}