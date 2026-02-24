<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('status', 'active')
            ->with('children')
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $categories->map(fn (Category $category) => $this->transformCategory($category)),
        ]);
    }

    public function show(Category $category)
    {
        $category->load('children');

        return response()->json([
            'status' => true,
            'data' => $this->transformCategory($category),
        ]);
    }

    private function transformCategory(Category $category): array
    {
        return [
            'id' => $category->id,
            'parent_id' => $category->parent_id,
            'name' => $category->name,
            'slug' => $category->slug,
            'image' => $category->image,
            'image_url' => $category->image ? asset('storage/'.$category->image) : null,
            'status' => $category->status,
            'children' => $category->children->map(fn (Category $child) => $this->transformCategory($child))->values(),
        ];
    }
}
