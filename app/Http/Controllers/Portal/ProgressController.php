<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\ProgressEntry;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProgressController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Portal/Progress', [
            'entries' => $request->user()->progressEntries()
                ->orderByDesc('logged_on')
                ->limit(365)
                ->get(['id', 'logged_on', 'weight_kg', 'body_fat_pct', 'waist_cm', 'note']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'logged_on' => 'required|date|before_or_equal:tomorrow',
            'weight_kg' => 'required|numeric|between:25,400',
            'body_fat_pct' => 'nullable|numeric|between:2,70',
            'waist_cm' => 'nullable|numeric|between:30,250',
            'note' => 'nullable|string|max:500',
        ]);

        // One entry per day: logging the same date again updates it.
        $request->user()->progressEntries()->updateOrCreate(
            ['logged_on' => $validated['logged_on']],
            $validated,
        );

        return back()->with('success', 'Progress saved.');
    }

    public function destroy(Request $request, ProgressEntry $entry)
    {
        abort_unless($entry->user_id === $request->user()->id, 403);
        $entry->delete();

        return back()->with('success', 'Entry removed.');
    }
}
