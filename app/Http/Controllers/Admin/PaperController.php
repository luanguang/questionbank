<?php

namespace App\Http\Controllers\Admin;

use App\Models\Paper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PaperController extends Controller
{
    public function index()
    {
        $papers = Paper::where('teacher_name', Auth::user()->name)->orderBy('id', 'DESC')->paginate(10);

        return response()->json(['papers' => $papers]);
    }

    public function show($paper_id)
    {

    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title'         =>  'required|string',
            'category_id'   =>  'required|integer|min:0',
            'question_num'  =>  'required|integer|min:0',
            'total_score'   =>  'required|integer|min:0',
            'score'         =>  'required|integer|min:0',
            'is_online'     =>  'required|boolean',
            'test_hours'    =>  'required|integer'
        ]);

        $paper = Paper::create([
            'title'         =>  $request->input('title'),
            'category_id'   =>  $request->input('category_id'),
            'teacher_name'  =>  Auth::user()->name,
            'question_num'  =>  $request->input('question_num'),
            'total_score'   =>  $request->input('total_score'),
            'score'         =>  $request->input('score'),
            'is_online'     =>  $request->input('is_online'),
            'test_hours'    =>  $request->input('test_hours')
        ]);

        return response()->json(['paper'    =>  $paper]);
    }
}
