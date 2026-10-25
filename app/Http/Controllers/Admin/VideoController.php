<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExerciseVideo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VideoController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Videos', [
            'videos' => ExerciseVideo::orderBy('category')->orderBy('title')->get(),
        ]);
    }

    public function store(Request $request)
    {
        ExerciseVideo::create($this->validated($request));

        return back()->with('success', 'Video added.');
    }

    public function update(Request $request, ExerciseVideo $video)
    {
        $video->update($this->validated($request));

        return back()->with('success', 'Video updated.');
    }

    public function destroy(ExerciseVideo $video)
    {
        $video->delete();

        return back()->with('success', 'Video removed.');
    }

    private function validated(Request $request): array
    {
        // A full http(s) link, or a file hosted on this site (e.g. /videos/exercises/squat.mp4).
        $sitePath = str_starts_with((string) $request->input('url'), '/');

        return $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:60',
            'url' => ['required', 'string', 'max:500', $sitePath ? 'regex:#^/[^/\\\\\s]\S*$#' : 'url:http,https'],
            'description' => 'nullable|string|max:2000',
        ]);
    }
}
