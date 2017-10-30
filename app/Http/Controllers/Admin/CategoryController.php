<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        return $categories;
    }

    public function show($category_id)
    {
        $category = Category::findOrFail($category_id);

        return $category;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'subject'   =>  'required|string',
            'content'   =>  'required|string'
        ]);

        $category = Category::create([
            'subject'   => $request->input('subject'),
            'content'   => $request->input('content'),
        ]);

        return $category;
    }

    public function update(Request $request, $category_id)
    {
        $this->validate($request, [
            'subject'   =>  'string',
            'content'   =>  'content'
        ]);

        $category = Category::findOrFail($category_id);
        $category->update([
            'subject'   =>  $request->input('subject'),
            'content'   =>  $request->input('content')
        ]);

        return $category;
    }

    public function destroy($category_id)
    {
        $category = Category::findOrFail($category_id);
        $category->delete();

        return;
    }
}
