<?php

use App\Http\Controllers\AuthorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;


// Public routes for the book stock application
Route::get('/', [UserController::class, 'index'])->name('home');
Route::get('/registration', [UserController::class, 'showRegistrationForm'])->name('register.form');
Route::post('/register', [UserController::class, 'register'])->name('register');
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

// Private routes for managing books, accessible only to authenticated users
Route::middleware(['auth'])->group(function () {
    Route::resource('books', BookController::class);

    // Additional routes for book management can be added here
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'showCreateForm'])->name('categories.create');
    Route::post('/categories/create', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/edit/{id}', [CategoryController::class, 'show'])->name('categories.show');
    Route::put('/categories/update/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/delete/{id}', [CategoryController::class, 'destroy'])->name('categories.delete');

    Route::get('/authors', [AuthorController::class, 'index'])->name('authors.index');
    Route::get('/authors/create', [AuthorController::class, 'showCreateForm'])->name('authors.create');
    Route::post('/authors/create', [AuthorController::class, 'store'])->name('authors.store');
});
