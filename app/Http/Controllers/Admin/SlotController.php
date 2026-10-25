<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TimeSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class SlotController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Slots', [
            'slots' => TimeSlot::upcoming()->with('user:id,name,email')->limit(500)
                ->get(['id', 'starts_at', 'ends_at', 'user_id', 'client_note', 'meeting_url']),
        ]);
    }

    /**
     * The browser turns the coach's local date/time into UTC instants, so the
     * slots land correctly whatever timezone the coach or the client is in.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'slots' => 'required|array|min:1|max:200',
            'slots.*.starts_at' => 'required|date|after:now',
            'slots.*.ends_at' => 'required|date',
            'meeting_url' => 'nullable|url|max:255',
        ]);

        $created = 0;

        foreach ($validated['slots'] as $slot) {
            $startsAt = Carbon::parse($slot['starts_at'])->utc();
            $endsAt = Carbon::parse($slot['ends_at'])->utc();

            if ($endsAt->lessThanOrEqualTo($startsAt)) {
                continue;
            }

            $overlaps = TimeSlot::where('starts_at', '<', $endsAt)->where('ends_at', '>', $startsAt)->exists();

            if (! $overlaps) {
                TimeSlot::create([
                    'starts_at' => $startsAt,
                    'ends_at' => $endsAt,
                    'meeting_url' => $validated['meeting_url'] ?? null,
                ]);
                $created++;
            }
        }

        $skipped = count($validated['slots']) - $created;

        return back()->with('success', "{$created} slot(s) added".($skipped ? ", {$skipped} skipped (overlapping existing slots)." : '.'));
    }

    public function destroy(TimeSlot $slot)
    {
        $slot->delete();

        return back()->with('success', 'Slot removed.');
    }
}
