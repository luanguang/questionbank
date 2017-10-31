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
            'category_id'  =>   'integer|min:0',
            'content'      =>   'string'
        ]);

        $histories = History::where('user_id', Auth::user()->id);
        $search = array_filter($request->only('category_id', 'content', 'question_id'), function ($var) {
            return !empty($var);
        });
        foreach ($search as $key => $value) {
            if (in_array($key, ['category_id', 'question_id'])) {
                $histories = $histories->where($key, $value);
            } elseif (in_array($key, ['content'])) {
                $histories = $histories->where($key, 'LIKE', '%'.$value.'%');
            }
        }
        $histories = $histories->orderBy('id', 'DESC')->paginate(20);

        return $histories->tojson();
    }
}
