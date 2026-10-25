<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\ExerciseVideo;
use App\Models\NutritionPlan;
use App\Models\TrainingPlan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LibraryController extends Controller
{
    public function program(Request $request)
    {
        $user = $request->user();

        return Inertia::render('Portal/Program', [
            'plans' => TrainingPlan::visibleTo($user)->get(['id', 'title', 'description', 'user_id', 'days', 'created_at', 'updated_at']),
            // Videos in a client's program always play, even from a locked body part.
            'videos' => ExerciseVideo::whereIn('id', $user->programVideoIds())->get(['id', 'title', 'url'])->keyBy('id'),
            'memberSince' => $user->created_at,
        ]);
    }

    public function nutrition(Request $request)
    {
        return Inertia::render('Portal/Nutrition', [
            'plans' => NutritionPlan::visibleTo($request->user())
                ->get(['id', 'title', 'description', 'user_id', 'calories', 'protein_g', 'carbs_g', 'fat_g', 'meals', 'updated_at']),
        ]);
    }

    public function videos(Request $request)
    {
        $user = $request->user();
        $unlocked = $user->unlocked_video_categories ?? [];
        $programVideoIds = $user->programVideoIds();

        $videos = ExerciseVideo::orderBy('category')->orderBy('title')->get(['id', 'title', 'category', 'url', 'description'])
            ->map(function (ExerciseVideo $video) use ($unlocked, $programVideoIds) {
                $locked = ! in_array($video->category, $unlocked, true) && ! $programVideoIds->contains($video->id);

                // A locked video keeps its title and poster as a preview, but never its link.
                return $locked
                    ? $video->only('id', 'title', 'category', 'poster') + ['locked' => true]
                    : $video->only('id', 'title', 'category', 'url', 'description', 'poster') + ['locked' => false];
            });

        return Inertia::render('Portal/Videos', ['videos' => $videos]);
    }
}
