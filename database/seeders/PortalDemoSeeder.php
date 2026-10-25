<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\ExerciseVideo;
use App\Models\NutritionPlan;
use App\Models\Order;
use App\Models\Program;
use App\Models\Ticket;
use App\Models\TimeSlot;
use App\Models\TrainingPlan;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Demo data for previewing the client portal and coach admin locally.
 * Run on demand:  php artisan db:seed --class=PortalDemoSeeder
 */
class PortalDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command?->error('PortalDemoSeeder is for local previews only.');

            return;
        }

        $coach = User::firstOrNew(['email' => 'coach@dekifit.test']);
        $coach->forceFill(['name' => 'Dejan Badzukov', 'password' => 'password', 'is_admin' => true])->save();

        $client = User::firstOrNew(['email' => 'client@dekifit.test']);
        $client->forceFill([
            'name' => 'Marko Petrov',
            'password' => 'password',
            'program_id' => Program::where('slug', 'pro')->value('id'),
            'created_at' => $client->created_at ?? now()->subDays(41),
        ])->save();

        // Six weeks of check-ins trending down.
        $weight = 88.6;
        foreach (range(41, 0, 3) as $i => $daysAgo) {
            $weight -= [0.5, 0.2, 0.6, -0.2, 0.4][$i % 5];
            $client->progressEntries()->updateOrCreate(
                ['logged_on' => now()->subDays($daysAgo)->toDateString()],
                ['weight_kg' => round($weight, 1), 'waist_cm' => round(94 - $i * 0.3, 1), 'note' => $i % 4 === 0 ? 'Good week, hit all sessions.' : null],
            );
        }

        if (! $client->notes()->exists()) {
            $client->notes()->createMany([
                ['title' => 'Squat cues', 'body' => "Brace before unracking\nKnees out, chest up\nPause 1s at the bottom on warm-up sets"],
                ['title' => 'PRs', 'body' => "Bench 95 kg x 5\nDeadlift 160 kg x 3"],
            ]);
        }

        if (! Announcement::exists()) {
            Announcement::create(['title' => 'Welcome to your DekiFit dashboard', 'body' => "This is your home base. Log your check-ins, keep notes, ask me anything and book your calls from here.\n\nIt's a marathon, not a sprint.", 'is_pinned' => true, 'published_at' => now()->subDays(5)]);
            Announcement::create(['title' => "This week's focus: sleep", 'body' => 'Aim for 7–8 hours. Recovery is where the muscle is built — treat it like a training session.', 'published_at' => now()->subHours(6)]);
        }

        if (! $client->tickets()->exists()) {
            $ticket = $client->tickets()->create(['subject' => 'Shoulder discomfort on bench press']);
            $ticket->addMessage($client, 'I feel a pinch in my right shoulder on the last reps. Should I lower the weight?');
            $ticket->addMessage($coach, 'Drop 10% this week and tuck your elbows a bit more. Send me a video of your next set and I will check your form.');
        }

        if (! TimeSlot::upcoming()->exists()) {
            foreach ([1, 2, 4] as $day) {
                foreach ([17, 18] as $hour) {
                    foreach ([0, 30] as $minute) {
                        $start = now()->addDays($day)->setTime($hour, $minute);
                        TimeSlot::create(['starts_at' => $start, 'ends_at' => $start->copy()->addMinutes(30)]);
                    }
                }
            }
            TimeSlot::upcoming()->skip(2)->first()->update(['user_id' => $client->id, 'booked_at' => now(), 'client_note' => 'Review my bench form']);
        }

        if (! Order::where('status', 'pending')->exists()) {
            Order::create(['program_id' => Program::where('slug', 'elite')->value('id'), 'package_name' => 'Elite', 'price' => 140, 'name' => 'Ana Stojanova', 'email' => 'ana@dekifit.test', 'phone' => '+389 70 000 000', 'notes' => 'Goal: fat loss, training 4x per week.']);
        }

        if (! ExerciseVideo::exists()) {
            // Placeholder clip so the library has something to play; swap for real exercise videos.
            foreach ([['Barbell bench press', 'Chest'], ['Incline dumbbell press', 'Chest'], ['Barbell row', 'Back'], ['Lat pulldown', 'Back'], ['Back squat', 'Legs'], ['Romanian deadlift', 'Legs'], ['Overhead press', 'Shoulders']] as [$title, $category]) {
                ExerciseVideo::create(['title' => $title, 'category' => $category, 'url' => '/videos/dekifit-coach.mp4', 'description' => 'Demo placeholder clip.']);
            }
        }

        if (! TrainingPlan::exists()) {
            $video = fn (string $title) => ExerciseVideo::where('title', $title)->value('id');

            TrainingPlan::create([
                'title' => 'Upper / Lower — 4 days',
                'description' => "Train 4 days per week. Leave 1–2 reps in the tank on every set and add weight when you hit the top of the rep range.\n10–15k steps daily.",
                'user_id' => $client->id,
                'days' => [
                    ['name' => 'Day 1 — Upper', 'exercises' => [
                        ['name' => 'Barbell bench press', 'sets' => '4', 'reps' => '6–8', 'rest' => '2–3 min', 'note' => 'Tuck elbows, pause on the chest.', 'video_id' => $video('Barbell bench press')],
                        ['name' => 'Barbell row', 'sets' => '4', 'reps' => '8–10', 'rest' => '2 min', 'note' => null, 'video_id' => $video('Barbell row')],
                        ['name' => 'Overhead press', 'sets' => '3', 'reps' => '8–10', 'rest' => '2 min', 'note' => null, 'video_id' => $video('Overhead press')],
                        ['name' => 'Lat pulldown', 'sets' => '3', 'reps' => '10–12', 'rest' => '90 s', 'note' => null, 'video_id' => $video('Lat pulldown')],
                    ]],
                    ['name' => 'Day 2 — Lower', 'exercises' => [
                        ['name' => 'Back squat', 'sets' => '4', 'reps' => '6–8', 'rest' => '3 min', 'note' => 'Brace before you unrack.', 'video_id' => $video('Back squat')],
                        ['name' => 'Romanian deadlift', 'sets' => '3', 'reps' => '8–10', 'rest' => '2 min', 'note' => null, 'video_id' => $video('Romanian deadlift')],
                        ['name' => 'Walking lunges', 'sets' => '3', 'reps' => '12 / leg', 'rest' => '90 s', 'note' => null, 'video_id' => null],
                    ]],
                    ['name' => 'Day 3 — Upper', 'exercises' => [
                        ['name' => 'Incline dumbbell press', 'sets' => '4', 'reps' => '8–10', 'rest' => '2 min', 'note' => null, 'video_id' => $video('Incline dumbbell press')],
                        ['name' => 'Lat pulldown', 'sets' => '4', 'reps' => '10–12', 'rest' => '90 s', 'note' => null, 'video_id' => $video('Lat pulldown')],
                    ]],
                ],
            ]);
        }

        if (! NutritionPlan::exists()) {
            NutritionPlan::create([
                'title' => 'Lean bulk — 2,600 kcal',
                'description' => "Hit your protein every day; carbs and fats can flex a little.\nDrink 3 L of water. One free meal per week — enjoy it, then back on plan.",
                'user_id' => $client->id,
                'calories' => 2600,
                'protein_g' => 180,
                'carbs_g' => 300,
                'fat_g' => 75,
                'meals' => [
                    ['name' => 'Breakfast', 'time' => '08:00', 'note' => null, 'items' => [['food' => 'Oats', 'amount' => '80 g'], ['food' => 'Whey protein', 'amount' => '1 scoop'], ['food' => 'Banana', 'amount' => '1'], ['food' => 'Peanut butter', 'amount' => '15 g']]],
                    ['name' => 'Lunch', 'time' => '13:00', 'note' => 'Any lean meat works here.', 'items' => [['food' => 'Chicken breast', 'amount' => '200 g'], ['food' => 'Rice (cooked)', 'amount' => '250 g'], ['food' => 'Mixed vegetables', 'amount' => '150 g'], ['food' => 'Olive oil', 'amount' => '1 tbsp']]],
                    ['name' => 'Pre-workout snack', 'time' => '16:30', 'note' => null, 'items' => [['food' => 'Greek yogurt', 'amount' => '250 g'], ['food' => 'Berries', 'amount' => '100 g'], ['food' => 'Honey', 'amount' => '1 tsp']]],
                    ['name' => 'Dinner', 'time' => '20:00', 'note' => null, 'items' => [['food' => 'Salmon or beef', 'amount' => '200 g'], ['food' => 'Potatoes', 'amount' => '300 g'], ['food' => 'Green salad', 'amount' => 'as much as you like']]],
                ],
            ]);
        }

        $this->command?->info('Demo logins — coach@dekifit.test / client@dekifit.test, password: password');
    }
}
