<?php

namespace App\Http\Controllers\Web;

use App\Models\Choice;
use App\Models\Paper;
use App\Models\Question;
use App\Models\Transcript;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PaperController extends Controller
{
    public function index()
    {
        $papers = Paper::with('category')->where('is_online', 1)->select('id', 'title', 'teacher_name', 'category_id', 'question_num', 'total_score', 'test_hours')->get();

        return response()->json(['papers' => $papers])->setStatusCode(200);
    }

    public function show($paper_id)
    {
        $paper          = Paper::with('category')->findOrFail($paper_id);
        $questions      = Question::with(['answers'=>function($var) {$var->select('id', 'question_id', 'choice');}])->where('paper_id', $paper_id)->get();


        return response()->json(['paper' => $paper, 'questions' => $questions]);
    }

    public function getScore(Request $request, $paper_id)
    {
        $choices        = $request->input('choices');
        $all_choice     = Choice::with('question')->whereIn('id', $choices)->get();
        $score          = 0;
        foreach ($all_choice as $temp) {
            if ($temp->is_right == 1) {
                $score  += $temp->question->score;
            }
        }
        $transcript = Transcript::create([
            'paper_id'      =>  $paper_id,
            'user_id'       =>  Auth::user()->id,
            'score'         =>  $score
        ]);

        return response()->json(['transcript' => $transcript]);
    }
}
