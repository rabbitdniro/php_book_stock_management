<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    // Display a listing of the authors
    public function index()
    {
        $authors = DB::table('authors')->get();
        return view('pages.authors', ['authors' => $authors]);
        // For now, we will return a simple view for authors
        // return view('pages.authors');
    }

    public function showCreateForm()
    {
        // Show form to create a new author
        return view('pages.authors-create');
    }

    public function store(Request $request)
    {
        // dd($request->all());
        // Handle the form submission to create a new author
        if (!Auth::user()) {
            return redirect()->route('login');
        }

        $validatedAuthor = $request->validate([
            'author_name' => 'required|string|max:255',
            'author_email' => 'required|email|unique:authors,email',
            'author_bio' => 'nullable|string',
            'author_status' => 'required|in:active,inactive',
        ]);

        $author_name = $validatedAuthor['author_name'];
        $author_email = $validatedAuthor['author_email'];
        $author_bio = $validatedAuthor['author_bio'] ?? '';
        $author_status = $validatedAuthor['author_status'];

        DB::table('authors')->insert([
            'name' => $author_name,
            'email' => $author_email,
            'bio' => $author_bio,
            'status' => $author_status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        // For now, we will just redirect back to the authors list
        return redirect()->route('authors.index');
    }
}
