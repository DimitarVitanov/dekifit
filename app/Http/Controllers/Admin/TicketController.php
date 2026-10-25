<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TicketController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Tickets', [
            'tickets' => Ticket::with('user:id,name')
                ->orderByRaw("case status when 'open' then 0 when 'answered' then 1 else 2 end")
                ->latest('last_reply_at')
                ->limit(300)
                ->get(['id', 'user_id', 'subject', 'status', 'last_reply_at']),
        ]);
    }

    public function show(Ticket $ticket)
    {
        return Inertia::render('Admin/TicketShow', [
            'ticket' => $ticket->only('id', 'subject', 'status', 'created_at') + [
                'client' => $ticket->user->only('id', 'name', 'email'),
            ],
            'messages' => $ticket->messages()->with('user:id,name,is_admin')->get(['id', 'user_id', 'body', 'created_at']),
        ]);
    }

    public function reply(Request $request, Ticket $ticket)
    {
        $validated = $request->validate(['body' => 'required|string|max:5000']);
        $ticket->addMessage($request->user(), $validated['body']);

        return back();
    }

    public function close(Ticket $ticket)
    {
        $ticket->update(['status' => Ticket::CLOSED]);

        return back()->with('success', 'Ticket closed.');
    }
}
