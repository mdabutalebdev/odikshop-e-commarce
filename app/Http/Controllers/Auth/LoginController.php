<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        // Allow login by email OR phone.
        $field = filter_var($credentials['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $ok = Auth::attempt(
            [$field => $credentials['email'], 'password' => $credentials['password']],
            $request->boolean('remember')
        );

        if (! $ok) {
            return back()
                ->withErrors(['email' => 'Invalid email/phone or password.'])
                ->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        return $request->user()->is_admin
            ? redirect()->intended(route('admin.dashboard'))
            : redirect()->intended(route('account'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
