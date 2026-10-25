<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AnnouncementController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Announcements', [
            'announcements' => Announcement::orderByDesc('is_pinned')->latest()->limit(200)->get(),
        ]);
    }

    public function store(Request $request)
    {
        Announcement::create($this->validated($request) + ['published_at' => now()]);

        return back()->with('success', 'Announcement published.');
    }

    public function update(Request $request, Announcement $announcement)
    {
        $announcement->update($this->validated($request));

        return back()->with('success', 'Announcement updated.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return back()->with('success', 'Announcement deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:10000',
            'is_pinned' => 'boolean',
        ]);
    }
}
