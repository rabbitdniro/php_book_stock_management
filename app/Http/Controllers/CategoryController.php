<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    // Display a listing of the categories
    public function index()
    {
        $categories = DB::table('categories')->get();
        return view('pages.categories', ['categories' => $categories]);
        // For now, we will return a simple view for categories
        // return view('pages.categories');
    }

    public function showCreateForm()
    {
        // Show form to create a new category
        return view('pages.categories-create');
    }

    public function store(Request $request)
    {
        // Handle the form submission to create a new category
        // For now, we will just redirect back to the categories list
        //dd($request->all());

        if (!Auth::user()) {
            return redirect()->route('login');
        }

        $validatedCategory = $request->validate([
            'category_name' => 'required|string|max:255',
            'category_description' => 'nullable|string',
            'category_status' => 'required|in:active,inactive',
        ]);

        $category_name = $validatedCategory['category_name'];
        $category_description = $validatedCategory['category_description'] ?? '';
        $category_status = $validatedCategory['category_status'];

        DB::table('categories')->insert([
            'name' => $category_name,
            'description' => $category_description,
            'status' => $category_status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('categories.index');
    }
}
