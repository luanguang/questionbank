<?php

namespace App\Http\Controllers\Web;

use App\Models\History;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $this->validate($request, [
            'category_id'   => 'required|integer|min:0'
        ]);
        $history = History::with('user', 'question', 'category')->where(['user_id' => Auth::user()->id, 'category_id' => $request->input('category_id')])->orderBy('id', 'DESC')->paginate(20);

        return response()->json(['history' => $history]);
    }
}
