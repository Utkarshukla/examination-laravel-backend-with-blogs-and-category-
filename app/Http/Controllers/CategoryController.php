<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
class CategoryController extends Controller
{
    // public function show()
    // {
    //     $categories = Category::all();
    //     return response()->json($categories);
    // }
    public function store(Request $request)
    {
        echo 'hi1';
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            
            'parent_id' => 'nullable|exists:categories,id', // Ensure parent category exists
        ]);
        echo 'hi';
        // Create the category
        $category = Category::create($validatedData);

        return response()->json($category, 201);
    }

    public function index()
    {
        $categories = Category::with('children')->where('parent_id', 0)->get();
        $categories->each(function ($category) {
            $this->fetchChildren($category);
        });

        return response()->json($categories);
    }
    
    private function fetchChildren($category)
    {
        $category->load('children');
        foreach ($category->children as $child) {
            $this->fetchChildren($child);
        }
    }
    
}
