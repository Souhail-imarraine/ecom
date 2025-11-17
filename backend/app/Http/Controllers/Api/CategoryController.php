<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('children')->whereNull('parent_id')->where('is_active', true)->get();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'parent_id' => $request->parent_id,
        ]);

        return response()->json($category, 201);
    }

    public function show(Category $category)
    {
        return response()->json($category->load('children', 'products'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($request->has('name')) {
            $category->slug = Str::slug($request->name);
        }

        $category->update($request->all());

        return response()->json($category);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(['message' => 'Catégorie supprimée']);
    }
}
