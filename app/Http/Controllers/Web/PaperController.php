<?php

namespace App\Http\Controllers\Web;

use App\Models\Answer;
use App\Models\Paper;
use App\Models\Transcript;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PaperController extends Controller
{
    public function index()
    {
        $papers = Paper::with('category')->where('is_online', 1)->select('id', 'title', 'teacher_name', 'category_id', 'question_num', 'total_score', 'test_hours')->get();

        return response()->json(['papers' => $papers])->setStatusCode(200);
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

    public function getScore(Request $request, $paper_id)
    {
        $choices        = $request->input('choices');
        $choice_id      = [];
        foreach ($choices as $choice) {
            $choice_id[] = $choice->only(['id']);
        }
        $all_choice     = Answer::whereIn('id', $choice_id)->get();
        $right_choice   = count($all_choice->where('is_right', 1));
        $paper          = Paper::findOrFail($paper_id);
        $transcript     = Transcript::create([
            'paper_id'  =>  $paper_id,
            'user_id'   =>  Auth::user()->id,
            'score'     =>  $paper->score * $right_choice
        ]);

        return response()->json(['transcript' => $transcript]);
    }
}
