<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NutritionPlan;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NutritionPlanController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Nutrition', [
            'plans' => NutritionPlan::with('user:id,name', 'program:id,title')->latest('updated_at')->get(),
            'clients' => User::clients()->orderBy('name')->get(['id', 'name']),
            'packages' => Program::published()->get(['id', 'title']),
        ]);
    }

    public function store(Request $request)
    {
        NutritionPlan::create($this->validated($request));

        return back()->with('success', 'Nutrition plan saved.');
    }

    public function update(Request $request, NutritionPlan $plan)
    {
        $plan->update($this->validated($request));

        return back()->with('success', 'Nutrition plan updated.');
    }

    public function destroy(NutritionPlan $plan)
    {
        $plan->delete();

        return back()->with('success', 'Nutrition plan deleted.');
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'user_id' => 'nullable|exists:users,id',
            'program_id' => 'nullable|exists:programs,id',
            'calories' => 'nullable|integer|between:500,10000',
            'protein_g' => 'nullable|integer|between:0,1000',
            'carbs_g' => 'nullable|integer|between:0,2000',
            'fat_g' => 'nullable|integer|between:0,1000',
            'meals' => 'required|array|min:1|max:30',
            'meals.*.name' => 'required|string|max:100',
            'meals.*.time' => 'nullable|string|max:30',
            'meals.*.note' => 'nullable|string|max:500',
            'meals.*.items' => 'present|array|max:30',
            'meals.*.items.*.food' => 'required|string|max:150',
            'meals.*.items.*.amount' => 'nullable|string|max:60',
        ]);

        // A plan for one client is not also tied to a package.
        if ($validated['user_id'] ?? null) {
            $validated['program_id'] = null;
        }

        return $validated;
    }
}
