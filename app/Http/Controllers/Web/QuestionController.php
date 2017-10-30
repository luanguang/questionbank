<?php

namespace App\Http\Controllers\Web;

use App\Models\Question;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $this->validate($request, [
            'category_id'   =>  'integer|min:0',
            'content'       =>  'string',
            'is_pic'        =>  'boolean',
            'user_id'       =>  'integer|min:0'
        ]);

        $search = array_filter($request->only('content', 'category_id', 'is_pic', 'user_id'), function ($var) {
            return !empty($var);
        });

        $questions = new Question;
        foreach ($search as $key => $value) {
            if (in_array($key, ['category_id', 'is_pic', 'user_id'])) {
                $questions = $questions->where($key, $value);
            } elseif (in_array($key, ['content'])) {
                $questions = $questions->where($key, 'LIKE', '%' . $value . '%');
            }
        }
        $questions = $questions->orderBy('id', 'DESC')->paginate(30);

        return $questions->toJson();
    }
}
