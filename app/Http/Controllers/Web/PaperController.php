<?php

namespace App\Http\Controllers\Web;

use App\Models\Paper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PaperController extends Controller
{
    public function index()
    {
        $papers = Paper::where('is_online', 1)->select('title', 'teacher_name', 'category_id', 'question_num', 'total_score', 'test_hours')->get();

        return response()->json(['papers' => $papers])->setStatusCode(201);
    }
}
