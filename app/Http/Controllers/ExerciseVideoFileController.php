<?php

namespace App\Http\Controllers;

use App\Models\ExerciseVideo;
use Illuminate\Http\Request;

class ExerciseVideoFileController extends Controller
{
    /**
     * Streams a hosted library clip to the coach or a client who has it unlocked.
     * BinaryFileResponse handles range requests, so seeking and iOS playback work.
     */
    public function __invoke(Request $request, string $file)
    {
        $video = ExerciseVideo::where('url', ExerciseVideo::HOSTED.$file)->firstOrFail();

        abort_unless($request->user()->canWatch($video), 403);

        $path = ExerciseVideo::hostedPath($file);
        abort_unless(is_file($path), 404);

        return response()->file($path, ['Cache-Control' => 'private, max-age=86400']);
    }
}
