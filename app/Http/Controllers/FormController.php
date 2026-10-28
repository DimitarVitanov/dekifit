<?php

namespace App\Http\Controllers;

use App\Mail\ContactReceived;
use App\Mail\NewContactMessage;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use function Illuminate\Support\defer;

class FormController extends Controller
{
    public function submitContact(Request $request)
    {
        $thanks = 'Thank you for your message. We\'ll get back to you soon!';

        // Hidden field that only bots fill in: act as if it worked, but store and send nothing.
        if (filled($request->input('contact_website'))) {
            return back()->with('success', $thanks);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $contact = Contact::create($validated);

        // Sent after the response, so the visitor isn't kept waiting on the mail server.
        defer(function () use ($contact) {
            rescue(fn () => Mail::to(config('dekifit.admin_email'))->send(new NewContactMessage($contact)));
            rescue(fn () => Mail::to($contact->email, $contact->name)->send(new ContactReceived($contact)));
        });

        return back()->with('success', $thanks);
    }
}
