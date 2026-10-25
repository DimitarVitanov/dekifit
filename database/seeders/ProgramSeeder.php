<?php

namespace Database\Seeders;

use App\Models\Program;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'title' => 'Basic',
                'slug' => 'start',
                'excerpt' => 'Basic plan, step by step.',
                'content' => 'The essentials to get going: a structured training program, a basic nutrition plan and a weekly check-in to keep you on track.',
                'price' => 50,
                'duration' => null,
                'features' => [
                    'Training program',
                    'Exercise video library',
                    'Basic nutrition plan',
                    'Calories & macros',
                    'Progress tracking',
                    'Sunday check-in',
                    'Email / message support',
                ],
                'is_featured' => false,
                'is_published' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Pro',
                'slug' => 'pro',
                'excerpt' => 'Coaching via video calls.',
                'content' => 'The main package and the best value: everything in Basic plus a fully personalized nutrition plan, weekly reviews and a monthly video call.',
                'price' => 100,
                'duration' => null,
                'features' => [
                    'Everything in Basic, plus:',
                    'Fully personalized nutrition plan',
                    'Weekly check-in',
                    'Weekly progress review',
                    'Training adjustments based on progress',
                    'Nutrition adjustments',
                    'Direct Instagram support',
                    '1 video call per month',
                    'Goal-specific programming: fat loss / muscle gain / recomposition',
                    'Bonus exercise tips and form checks',
                ],
                'is_featured' => true,
                'is_published' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'Elite',
                'slug' => 'elite',
                'excerpt' => '1-on-1 premium online video call coaching.',
                'content' => 'For people who want their coach heavily involved in their transformation.',
                'price' => 140,
                'duration' => null,
                'features' => [
                    'Everything in Pro, plus:',
                    '2 video calls per month',
                    'Priority response',
                    'More frequent check-ins',
                    'Faster training / nutrition adjustments',
                    'Detailed weekly progress analysis',
                    'Personalized cardio / step recommendations',
                    'Exercise technique / form feedback from videos',
                    'Higher level of accountability',
                    'Priority scheduling / support',
                ],
                'is_featured' => false,
                'is_published' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($packages as $package) {
            Program::updateOrCreate(['slug' => $package['slug']], $package);
        }

        // Retire the old tiers without deleting them, so past orders keep their link.
        Program::whereNotIn('slug', array_column($packages, 'slug'))->update(['is_published' => false]);
    }
}
