<?php

namespace App\Http\Controllers\Admin;

use App\Models\Answer;
use App\Models\Paper;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PaperController extends Controller
{
    public function index()
    {
        $papers = Paper::with('category')->where('teacher_name', Auth::user()->name)->orderBy('id', 'DESC')->paginate(10);

        return response()->json(['papers' => $papers]);
    }

    public function show($paper_id)
    {
        $paper          = Paper::with('questions', 'category')->findOrFail($paper_id);
        $questions      = $paper->questions;
        $question_id    = [];
        foreach ($questions as $value) {
            $question_id[] = $value->id;
        }
        $answers        = Answer::whereIn('question_id', $question_id)->select('id', 'question_id', 'choice')->get();

        return response()->json(['paper' => $paper, 'answers' => $answers]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title'         =>  'required|string',
            'category_id'   =>  'required|integer|min:0',
            'question_num'  =>  'required|integer|min:0',
            'total_score'   =>  'required|integer|min:0',
            'is_online'     =>  'required|boolean',
            'test_hours'    =>  'required|integer|min:0'
        ]);

        $paper = Paper::create([
            'title'         =>  $request->input('title'),
            'category_id'   =>  $request->input('category_id'),
            'teacher_name'  =>  Auth::user()->name,
            'question_num'  =>  $request->input('question_num'),
            'total_score'   =>  $request->input('total_score'),
            'is_online'     =>  $request->input('is_online'),
            'test_hours'    =>  $request->input('test_hours')
        ]);

        return response()->json(['paper'    =>  $paper])->setStatusCode(201);
    }

    public function update(Request $request, $paper_id)
    {
        $this->validate($request, [
            'title'         =>  'string',
            'category_id'   =>  'integer|min:0',
            'question_num'  =>  'integer|min:0',
            'total_score'   =>  'integer|min:0',
            'score'         =>  'integer|min:0',
            'is_online'     =>  'boolean',
            'test_hours'    =>  'integer|min:0'
        ]);

        $paper = Paper::findOrFail($paper_id);
        $paper->update([
            'title'         =>  $request->input('title'),
            'category_id'   =>  $request->input('category_id'),
            'question_num'  =>  $request->input('question_num'),
            'total_score'   =>  $request->input('total_score'),
            'score'         =>  $request->input('score'),
            'is_online'     =>  $request->input('is_online'),
            'test_hours'    =>  $request->input('test_hours')
        ]);

        return response()->json(['paper' => Paper::findOrFail($paper_id)])->setStatusCode(201);
    }

    public function destroy($paper_id)
    {
        $paper = Paper::findOrFail($paper_id);
        $paper->delete();

        return response()->json(['success' => true]);
    }
}
