<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\RenderException;
use App\Http\Requests\AnswerRequest;
use App\Models\Choice;
use App\Models\Paper;
use App\Models\Question;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Exception\NotReadableException;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $this->validate($request, [
            'title'         =>  'string',
            'is_pic'        =>  'boolean',
            'difficult'     =>  'integer|in:1,2,3,4,5',
            'category_id'   =>  'integer|min:0',
        ]);

        $question = new Question;
        $search   = array_filter($request->only('title', 'is_pic', 'difficult', 'category_id'), function ($var) {
            return !empty($var);
        });

        foreach ($search as $key => $value) {
            if (in_array($key, ['category_id', 'is_pic', 'difficult'])) {
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
        $paper = Paper::findOrFail($paper_id);
        $question = Question::create([
            'title'         =>  $request->input('title'),
            'is_pic'        =>  $request->input('is_pic'),
            'category_id'   =>  $request->input('category_id'),
            'score'         =>  $request->input('score'),
            'user_id'       =>  Auth::user()->id,
            'paper_id'      =>  $paper->id
        ]);

        $choice  =   $request->input('choice');
        $right   =   $request->input('is_right');
        $choices = [];
        for ($i = 0; $i<count($choice); $i++) {
            $choices[$i]['content']         = $choice[$i];
            $choices[$i]['question_id']     = $question->id;
            if ($i == $right[0]) {
                $choices[$i]['is_right']    = 1;
            } else {
                $choices[$i]['is_right']    = 0;
            }
            $choices[$i]['created_at']      = Carbon::now();
        }
        $choices = Choice::insert($choices);
        if ($choices) {
            return response()->json(['choices' => $choices, 'question' => $question]);
        } else {
            $question->delete();
            throw new RenderException();
        }
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
            $ans = Choice::findOrFail($answers[$i]['id']);
            if ($right[0] == $i) {
                $ans->update([
                    'choice'        =>  $choices[$i],
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

    public function create(AnswerRequest $request)
    {
        $question = Question::create([
            'title'         =>  $request->input('title'),
            'is_pic'        =>  $request->input('is_pic'),
            'score'         =>  $request->input('score'),
            'difficult'     =>  $request->input('difficult'),
            'category_id'   =>  $request->input('category_id'),
            'user_id'       =>  Auth::user()->id
        ]);

        $choice = $request->input('choice');
        $right  = $request->input('is_right');
        $choices = [];
        for ($i=0; $i<count($choice); $i++) {
            $choices[$i]['content']  = $choice[$i];
            $choices[$i]['question_id'] = $question->id;
            if ($right[0] == $i) {
                $choices[$i]['is_right'] = 1;
            } else {
                $choices[$i]['is_right'] = 0;
            }
            $choices[$i]['created_at'] = Carbon::now();
        }
        $choices = Choice::insert($choices);
        if ($choices) {
            return response()->json(['choices' => $choices, 'question' => $question]);
        } else {
            $question->delete();
            throw new NotReadableException();
        }

    }

    public function destroy($question_id)
    {
        $question = Question::findOrFail($question_id);
        $question->delete();
        Choice::where('question_id', $question_id)->delete();

        return response()->json(['code' => 204]);
    }
}