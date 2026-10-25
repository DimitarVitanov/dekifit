<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Portal/Tickets', [
            'tickets' => $request->user()->tickets()
                ->withCount('messages')
                ->latest('last_reply_at')
                ->get(['id', 'subject', 'status', 'last_reply_at', 'created_at']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string|max:5000',
        ]);

        $ticket = DB::transaction(function () use ($request, $validated) {
            $ticket = $request->user()->tickets()->create(['subject' => $validated['subject']]);
            $ticket->addMessage($request->user(), $validated['body']);

            return $ticket;
        });

        return redirect()->route('portal.tickets.show', $ticket)->with('success', 'Your question was sent to your coach.');
    }

    public function show(Request $request, Ticket $ticket)
    {
        abort_unless($ticket->user_id === $request->user()->id, 403);

        return Inertia::render('Portal/TicketShow', [
            'ticket' => $ticket->only('id', 'subject', 'status', 'created_at'),
            'messages' => $ticket->messages()->with('user:id,name,is_admin')->get(['id', 'user_id', 'body', 'created_at']),
        ]);
    }

    public function reply(Request $request, Ticket $ticket)
    {
        abort_unless($ticket->user_id === $request->user()->id, 403);

        $validated = $request->validate(['body' => 'required|string|max:5000']);
        $ticket->addMessage($request->user(), $validated['body']);

        return back();
    }
}
