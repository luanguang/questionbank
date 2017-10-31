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

        return $categories->tojson();
    }

    public function show($category_id)
    {
        $category = Category::findOrFail($category_id);

        return $category->tojson();
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

        return $category->toJson();
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

        return $category->tojson();
    }

    public function destroy($category_id)
    {
        $category = Category::findOrFail($category_id);
        $category->delete();

        return;
    }
}
