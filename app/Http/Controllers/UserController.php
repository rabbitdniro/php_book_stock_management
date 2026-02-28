<?php

namespace App\Http\Controllers;

use Illuminate\support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Display the home page for the book stock application
    public function index()
    {
        return view('pages.login');
    }

    // Show the login form to the user
    public function showRegistrationForm()
    {
        return view('pages.signup');
    }

    // Display the dashboard for authenticated users
    public function dashboard()
    {
        return view('pages.dashboard');
    }

    // Handle user registration
    public function register(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create a new user in the database
        $user = DB::table('users')->insert([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        // Redirect to login page after successful registration
        return redirect()->route('home');
    }

    // Handle user login
    public function login(Request $request)
    {
        // Validate the incoming request data
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        // Attempt to authenticate the user        
        if (Auth::attempt($credentials)) {

            $request->session()->regenerate();
            // Authentication passed, redirect to the dashboard
            return redirect()->route('dashboard');
        } else {
            // Authentication failed, redirect back to the login form with an error message
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

    }

    // Handle user logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

}