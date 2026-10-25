<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExerciseVideo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ClientController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Clients', [
            'clients' => User::clients()
                ->with('program:id,title')
                ->withCount('progressEntries')
                ->withMax('progressEntries', 'logged_on')
                ->latest()
                ->get(['id', 'name', 'email', 'phone', 'program_id', 'created_at']),
        ]);
    }

    public function show(User $client)
    {
        abort_if($client->is_admin, 404);

        return Inertia::render('Admin/ClientShow', [
            'client' => $client->only('id', 'name', 'email', 'phone', 'created_at') + [
                'program' => $client->program?->title,
            ],
            'entries' => $client->progressEntries()->orderByDesc('logged_on')->limit(365)
                ->get(['id', 'logged_on', 'weight_kg', 'body_fat_pct', 'waist_cm', 'note']),
            'sessions' => $client->timeSlots()->orderByDesc('starts_at')->limit(20)
                ->get(['id', 'starts_at', 'ends_at', 'client_note']),
            'tickets' => $client->tickets()->latest('last_reply_at')->limit(20)
                ->get(['id', 'subject', 'status', 'last_reply_at']),
            // Body parts in the video library with their video counts, e.g. { Chest: 9, Back: 11 }.
            'libraryCategories' => ExerciseVideo::orderBy('category')->groupBy('category')->selectRaw('category, count(*) as total')
                ->pluck('total', 'category'),
            'unlockedCategories' => $client->unlocked_video_categories ?? [],
        ]);
    }

    public function updateLibrary(Request $request, User $client)
    {
        abort_if($client->is_admin, 404);

        $validated = $request->validate([
            'categories' => 'present|array',
            'categories.*' => ['string', 'distinct', Rule::in(ExerciseVideo::distinct()->pluck('category'))],
        ]);

        $client->update(['unlocked_video_categories' => array_values($validated['categories'])]);

        return back()->with('success', 'Video library access updated.');
    }
}
