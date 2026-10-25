<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\TimeSlot;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Portal/Schedule', [
            'available' => TimeSlot::upcoming()->available()->limit(200)->get(['id', 'starts_at', 'ends_at']),
            'booked' => $request->user()->timeSlots()->upcoming()->get(['id', 'starts_at', 'ends_at', 'client_note', 'meeting_url']),
        ]);
    }

    public function book(Request $request, TimeSlot $slot)
    {
        $validated = $request->validate(['client_note' => 'nullable|string|max:500']);

        // Atomic claim: only succeeds if the slot is still free and in the future.
        $claimed = TimeSlot::whereKey($slot->id)
            ->whereNull('user_id')
            ->where('starts_at', '>', now())
            ->update([
                'user_id' => $request->user()->id,
                'booked_at' => now(),
                'client_note' => $validated['client_note'] ?? null,
            ]);

        if (! $claimed) {
            return back()->with('error', 'That time was just taken. Please pick another slot.');
        }

        return back()->with('success', 'Your session is booked.');
    }

    public function cancel(Request $request, TimeSlot $slot)
    {
        abort_unless($slot->user_id === $request->user()->id, 403);

        if ($slot->starts_at->isPast()) {
            return back()->with('error', 'This session has already started.');
        }

        $slot->update(['user_id' => null, 'booked_at' => null, 'client_note' => null]);

        return back()->with('success', 'Your booking was cancelled.');
    }
}
