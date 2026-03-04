<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
 
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
             $request->session()->regenerate();
             return redirect()->route('home');
        }

        return back()
            ->withErrors(['email' => 'Neplatné údaje'])
            ->onlyInput('email');
    }

    public function register(Request $request)
    {
        $credentials = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        //If user with specified email already exists, redirect back.
        if (User::where('email', $credentials['email'])->exists())
        { 
            return back();
        }

        $user = User::create([
            'name' => $credentials['name'],
            'email'=> $credentials['email'],
            'password' => Hash::make($credentials['password']),
        ]);
        
        Auth::login($user);

        return redirect()->route('home');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        // Invalidate session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
