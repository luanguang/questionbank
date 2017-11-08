<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('id', 'DESC')->paginate(20);

        return response()->json(['categories'   =>  $categories]);
    }

    public function show($category_id)
    {
        $category = Category::findOrFail($category_id);

        return response()->json(['category' =>  $category]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'subject'       =>  'required|string',
            'parent_id'     =>  'integer'
        ]);

        $category = Category::create([
            'subject'       => $request->input('subject'),
            'parent_id'     => $request->input('parent_id')
        ]);

        return response()->json(['category' =>  $category, 'code'   =>  201]);
    }

    public function update(Request $request, $category_id)
    {
        $this->validate($request, [
            'subject'       =>  'string',
            'parent_id'     =>  'integer'
        ]);

        $category = Category::findOrFail($category_id);
        $category->update([
            'subject'       =>  $request->input('subject'),
            'parent_id'     =>  $request->input('parent_id')
        ]);

        return response()->json(['category' =>  $category]);
    }

    public function destroy($category_id)
    {
        $category = Category::findOrFail($category_id);
        $category->delete();

        return response()->json(['code' => 204, 'success' => "删除成功"]);
    }
}
