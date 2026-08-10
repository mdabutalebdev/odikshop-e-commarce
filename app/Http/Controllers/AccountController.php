<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Guests see the login screen.
        if (! $user) {
            return redirect()->route('login');
        }

        $orders = $user->orders()->latest()->take(20)->get();

        return view('account-dashboard', compact('user', 'orders'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
        ]);

        $request->user()->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }
}
