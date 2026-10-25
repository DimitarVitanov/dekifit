<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExerciseVideo;
use App\Models\Program;
use App\Models\TrainingPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TrainingPlanController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Programs', [
            'plans' => TrainingPlan::with('user:id,name', 'program:id,title')->latest('updated_at')->get(),
            'clients' => User::clients()->orderBy('name')->get(['id', 'name']),
            'packages' => Program::published()->get(['id', 'title']),
            'videos' => ExerciseVideo::orderBy('category')->orderBy('title')->get(['id', 'title', 'category']),
        ]);
    }

    public function store(Request $request)
    {
        TrainingPlan::create($this->validated($request));

        return back()->with('success', 'Training program saved.');
    }

    public function update(Request $request, TrainingPlan $plan)
    {
        $plan->update($this->validated($request));

        return back()->with('success', 'Training program updated.');
    }

    public function destroy(TrainingPlan $plan)
    {
        $plan->delete();

        return back()->with('success', 'Training program deleted.');
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'user_id' => 'nullable|exists:users,id',
            'program_id' => 'nullable|exists:programs,id',
            'days' => 'required|array|min:1|max:100',
            'days.*.name' => 'required|string|max:100',
            'days.*.week' => 'nullable|integer|between:1,52',
            'days.*.exercises' => 'present|array|max:40',
            'days.*.exercises.*.name' => 'required|string|max:150',
            'days.*.exercises.*.sets' => 'nullable|string|max:20',
            'days.*.exercises.*.reps' => 'nullable|string|max:30',
            'days.*.exercises.*.rest' => 'nullable|string|max:30',
            'days.*.exercises.*.note' => 'nullable|string|max:300',
            'days.*.exercises.*.video_id' => 'nullable|integer|exists:exercise_videos,id',
        ]);

        // A plan for one client is not also tied to a package.
        if ($validated['user_id'] ?? null) {
            $validated['program_id'] = null;
        }

        return $validated;
    }
}
