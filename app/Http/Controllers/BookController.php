<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all books from the database
        $books = DB::table('books')
            ->join('authors', 'books.author_id', '=', 'authors.id')
            ->join('categories', 'books.category_id', '=', 'categories.id')
            ->select('books.*', 'authors.name as author_name', 'categories.name as category_name')
            ->get();
        return view('pages.books', ['books' => $books]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $authors_name = DB::table('authors')->pluck('name', 'id');
        $categories_name = DB::table('categories')->pluck('name', 'id');
        return view('pages.books-create', ['authors' => $authors_name, 'categories' => $categories_name]);
        // Show the form to create a new book
        // return view('pages.books-create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::user()) {
            return redirect()->route('login');
        }

        // dd($request->all());
        // Handle the form submission to create a new book
        $validatedBook = $request->validate([
            'book_cover_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'book_title' => 'required|string|max:255',
            'book_isbn' => 'required|string|max:20|unique:books,isbn',
            'book_author_id' => 'required|exists:authors,id',
            'book_category_id' => 'required|exists:categories,id',
            'book_publication_year' => 'required|integer|min:1000|max:' . date('Y'),
            'book_stock_quantity' => 'required|integer|min:0',
            'book_description' => 'nullable|string',
            'book_status' => 'required|in:available,borrowed,out_of_stock',
        ]);

        // Creating a unique file name for the uploaded book cover image
        // dd($request->all());
        $book_cover_image_path = null;
        if ($request->hasFile('book_cover_image')) {
            $file_name = $request->file('book_cover_image')->getClientOriginalName();
            $file_new_name = Auth::user()->id . '_' . date("j_F_Y_h:i:s_A") . '_' . implode('_', explode(' ', $file_name));
            $book_cover_image_path = $request->file('book_cover_image')->storeAs('book_covers', $file_new_name, 'public');
        }
        // dd($book_cover_image_path);

        // Extracting validated data for the new book
        $book_title = $validatedBook['book_title'];
        $book_isbn = $validatedBook['book_isbn'];
        $book_author_id = $validatedBook['book_author_id'];
        $book_category_id = $validatedBook['book_category_id'];
        $book_publication_year = $validatedBook['book_publication_year'];
        $book_stock_quantity = $validatedBook['book_stock_quantity'];
        $book_description = $validatedBook['book_description'] ?? '';
        $book_status = $validatedBook['book_status'];

        DB::table('books')->insert([
            'title' => $book_title,
            'isbn' => $book_isbn,
            'cover_image' => $book_cover_image_path,
            'description' => $book_description,
            'publication_date' => $book_publication_year,
            'stock_quantity' => $book_stock_quantity,
            'status' => $book_status,
            'author_id' => $book_author_id,
            'category_id' => $book_category_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // For now, we will just redirect back to the books list
        return redirect()->route('books.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        if (!Auth::user()) {
            return redirect()->route('login');
        }

        $book = DB::table('books')->where('id', $id)->first();

        if (!$book) {
            return redirect()->route('books.index');
        }

        // Delete the book cover image from storage if it exists
        if ($book->cover_image) {
            $cover_image_path = public_path('storage/' . $book->cover_image);
            if (file_exists($cover_image_path)) {
                unlink($cover_image_path);
            }
        }

        // Delete the book record from the database
        DB::table('books')->where('id', $id)->delete();

        return redirect()->route('books.index');
    }
}
