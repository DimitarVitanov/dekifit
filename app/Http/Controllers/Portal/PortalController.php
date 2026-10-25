<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\ExerciseVideo;
use App\Models\NutritionPlan;
use App\Models\Ticket;
use App\Models\TrainingPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class PortalController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $entries = $user->progressEntries()->orderBy('logged_on')->get(['logged_on', 'weight_kg']);
        $first = $entries->first();
        $latest = $entries->last();

        return Inertia::render('Portal/Dashboard', [
            'program' => $user->program?->only('title', 'excerpt'),
            'stats' => [
                'current_weight' => $latest?->weight_kg,
                'start_weight' => $first?->weight_kg,
                'weight_change' => $entries->count() > 1 ? round($latest->weight_kg - $first->weight_kg, 1) : null,
                'check_ins' => $entries->count(),
                'last_logged_on' => $latest?->logged_on?->toDateString(),
                'member_since' => $user->created_at,
            ],
            'weightSeries' => $entries->slice(-60)->values()->map(fn ($e) => [
                'date' => $e->logged_on->toDateString(),
                'value' => $e->weight_kg,
            ]),
            'nextSession' => $user->timeSlots()->upcoming()->first(['id', 'starts_at', 'ends_at', 'meeting_url']),
            'announcements' => Announcement::published()->limit(3)->get(['id', 'title', 'body', 'is_pinned', 'published_at']),
            'tickets' => $user->tickets()->where('status', '!=', Ticket::CLOSED)->latest('last_reply_at')->limit(3)->get(['id', 'subject', 'status', 'last_reply_at']),
            'plan' => TrainingPlan::visibleTo($user)->first()?->only('title', 'days', 'created_at'),
            'videoCount' => ExerciseVideo::count(),
            'unlockedVideoCount' => ExerciseVideo::whereIn('category', $user->unlocked_video_categories ?? [])
                ->orWhereIn('id', $user->programVideoIds())->count(),
            'nutrition' => NutritionPlan::visibleTo($user)->first()?->only('title', 'calories', 'meals'),
            'recentNotes' => $user->notes()->latest('updated_at')->limit(3)->get(['id', 'title', 'updated_at']),
        ]);
    }

    public function announcements()
    {
        return Inertia::render('Portal/Announcements', [
            'announcements' => Announcement::published()->limit(50)->get(['id', 'title', 'body', 'is_pinned', 'published_at']),
        ]);
    }

    public function account(Request $request)
    {
        return Inertia::render('Portal/Account', [
            'program' => $request->user()->program?->only('title', 'excerpt', 'features'),
        ]);
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $request->user()->forceFill([
            'password' => Hash::make($validated['password']),
            'must_change_password' => false,
        ])->save();

        return back()->with('success', 'Your password has been updated.');
    }
}
