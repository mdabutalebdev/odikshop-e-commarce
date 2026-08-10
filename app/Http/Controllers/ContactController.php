<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return view('contact');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string|max:2000',
        ]);

        // In a real deployment this would notify the store owner.
        return back()->with('success', 'Your message has been sent. We will contact you soon.');
    }
}
