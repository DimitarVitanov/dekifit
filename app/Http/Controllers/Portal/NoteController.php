<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Portal/Notes', [
            'notes' => $request->user()->notes()->latest('updated_at')->get(['id', 'title', 'body', 'updated_at']),
        ]);
    }

    public function store(Request $request)
    {
        $request->user()->notes()->create($this->validated($request));

        return back()->with('success', 'Note saved.');
    }

    public function update(Request $request, Note $note)
    {
        abort_unless($note->user_id === $request->user()->id, 403);
        $note->update($this->validated($request));

        return back()->with('success', 'Note updated.');
    }

    public function destroy(Request $request, Note $note)
    {
        abort_unless($note->user_id === $request->user()->id, 403);
        $note->delete();

        return back()->with('success', 'Note deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'nullable|string|max:10000',
        ]);
    }
}
