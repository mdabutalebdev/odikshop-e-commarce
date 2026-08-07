<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'text' => 'required|string|max:1000',
        ]);

        Testimonial::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'rating' => $validated['rating'],
            'text' => $validated['text'],
            'is_active' => false, // Hidden by default until admin approves
            'sort_order' => 0,
        ]);

        return back()->with('status', 'Thank you for your review! It has been submitted and is pending approval.');
    }
}
