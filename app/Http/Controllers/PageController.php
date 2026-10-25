<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Program;
use Inertia\Inertia;

class PageController extends Controller
{
    public function home()
    {
        $page = Page::where('slug', 'home')->with('visibleSections')->first();

        if (!$page) {
            $page = (object) [
                'meta_title' => 'DekiFit — Train. Focus. Transform.',
                'meta_description' => 'Personalized bodybuilding programs and nutrition plans by coach Dejan Badzukov. Build muscle, lose fat, and transform your physique.',
            ];
        }

        return Inertia::render('Home', [
            'page' => $page,
            'programs' => Program::published()->get(),
            'sections' => $page->visibleSections ?? collect(),
        ]);
    }

    public function programs()
    {
        $page = Page::where('slug', 'programs')->with('visibleSections')->first();
        $programs = Program::published()->get();

        return Inertia::render('Programs', [
            'page' => $page,
            'sections' => $page?->visibleSections?->keyBy('key') ?? collect(),
            'programs' => $programs,
        ]);
    }

    public function nutrition()
    {
        $page = Page::where('slug', 'nutrition')->with('visibleSections')->first();

        return Inertia::render('Nutrition', [
            'page' => $page,
            'sections' => $page?->visibleSections?->keyBy('key') ?? collect(),
            'programs' => Program::published()->get(),
        ]);
    }

    public function about()
    {
        $page = Page::where('slug', 'about')->with('visibleSections')->first();

        return Inertia::render('About', [
            'page' => $page,
            'sections' => $page?->visibleSections?->keyBy('key') ?? collect(),
        ]);
    }

    public function contact()
    {
        $page = Page::where('slug', 'contact')->with('visibleSections')->first();

        return Inertia::render('Contact', [
            'page' => $page,
            'sections' => $page?->visibleSections?->keyBy('key') ?? collect(),
        ]);
    }

    public function privacyPolicy()
    {
        return Inertia::render('PrivacyPolicy');
    }

    public function termsOfService()
    {
        return Inertia::render('TermsOfService');
    }
}
