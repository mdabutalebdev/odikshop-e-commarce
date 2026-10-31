<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::latest()->paginate(20);
        return view('admin.contact_messages.index', compact('messages'));
    }

    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();
        return back()->with('success', 'Message deleted successfully.');
    }
}
