<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use \App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function registerCreate()
    {
        return view('auth.register');
    }
    public function registerStore(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users',
            'password' => ['required',Password::min(6)],
        ]);

        $user = User::create([
            'username' => $request->username,
            'password' => $request->password,
        ]);

        Auth::login($user);

        return redirect('/games');
    }
    public function loginCreate()
    {
        return view('auth.login');
    }
    public function loginStore(Request $request)
    {
        $attributes = request()->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($attributes)) {
            throw ValidationException::withMessages([
                'username' => 'Sorry, those credentials do not match.',
            ]);
        }

        request()->session()->regenerate();

        return redirect('/games');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/');
    }
}