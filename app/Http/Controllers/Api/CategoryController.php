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
            'data' => $categories,
        ]);
    }

    public function show(Category $category)
    {
        $category->load('children');

        return response()->json([
            'status' => true,
            'data' => $category,
        ]);
    }
}
