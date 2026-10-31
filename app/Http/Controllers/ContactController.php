<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        // The view composer in AppServiceProvider only shares $settings with the
        // chrome partials, so this page has to load them itself for the info panel.
        return view('contact', ['settings' => SiteSetting::getAll()]);
    }

    public function submit(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string|max:2000',
        ]);

        ContactMessage::create($data);

        return back()->with('success', 'Your message has been sent. We will contact you soon.');
    }
}
