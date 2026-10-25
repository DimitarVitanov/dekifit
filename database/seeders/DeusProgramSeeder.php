<?php

namespace Database\Seeders;

use App\Models\ExerciseVideo;
use App\Models\TrainingPlan;
use Illuminate\Database\Seeder;
use RuntimeException;

/**
 * The default 12-week (3-month) "Deus" training program from "deus plan_1.docx",
 * shared with every client, with exercises linked to the exercise video library.
 * Skipped if it already exists, so the coach's edits stay.
 *   php artisan db:seed --class=DeusProgramSeeder --force
 */
class DeusProgramSeeder extends Seeder
{
    public const TITLE = 'Deus plan — 3 months';

    private const SS = 'Superset with the next exercise.';

    private const SS2 = 'Superset: straight after the previous exercise.';

    private const DROP = 'Drop set on the last set.';

    public function run(): void
    {
        $this->call(ExerciseLibrarySeeder::class);

        if (TrainingPlan::where('title', self::TITLE)->whereNull('user_id')->whereNull('program_id')->exists()) {
            $this->command?->info('Deus plan already exists.');

            return;
        }

        $videos = ExerciseVideo::pluck('id', 'url');
        $days = [];

        foreach ($this->weeks() as $week => $weekDays) {
            foreach ($weekDays as [$name, $exercises]) {
                $days[] = [
                    'name' => $name,
                    'week' => $week,
                    'exercises' => array_map(function (array $exercise) use ($videos) {
                        [$name, $sets, $reps, $note, $video, $rest] = $exercise + [null, null, null, null, null, null];

                        if ($video && ! isset($videos[ExerciseVideo::HOSTED."{$video}.mp4"])) {
                            throw new RuntimeException("Unknown exercise video: {$video}");
                        }

                        return [
                            'name' => $name, 'sets' => $sets, 'reps' => $reps, 'rest' => $rest, 'note' => $note,
                            'video_id' => $video ? $videos[ExerciseVideo::HOSTED."{$video}.mp4"] : null,
                        ];
                    }, $exercises),
                ];
            }
        }

        TrainingPlan::create(['title' => self::TITLE, 'description' => $this->description(), 'days' => $days]);

        $this->command?->info('Deus plan added.');
    }

    private function description(): string
    {
        return <<<'TEXT'
        Your 12-week training program. The calendar opens on today's workout; tap any day to see it. Tap Watch on an exercise to see how it's done.

        • Rest 60–90 seconds between sets
        • Focus on good form
        • Increase the weight every week
        • Warm up before every workout
        • On rest days, aim for 10–15k steps

        RM (rep max): use a weight that's hard to finish the reps with, but you still can. 6–8 RM means a weight you can lift for 6–8 good reps.
        Failure: keep going until you can't do one more good rep.
        Drop set: after the last set, lower the weight and keep going to failure.
        Superset: two exercises back to back, no rest in between.

        Week 12 is the final test: max out every exercise.
        TEXT;
    }

    /**
     * Week number => days. Each day is [name, exercises]; each exercise is
     * [name, sets, reps, note, video slug, rest] with the trailing ones optional.
     */
    private function weeks(): array
    {
        $ss = self::SS;
        $ss2 = self::SS2;
        $drop = self::DROP;

        return [
            1 => [
                ['Day 1 — Upper', [
                    ['Lat pulldown', '3', '8–12', '80% of your rep max. 2 warm-up sets of 5–6 reps with a lighter weight.', 'lat-pulldown'],
                    ['Incline dumbbell chest press', '3', '6–8 RM', "A weight that's hard to do 6 reps with, but you still can.", 'incline-dumbbell-press'],
                    ['Cable flys', '2', '8–12', $ss, 'cable-crossover-fly'],
                    ['Push-ups', '2', '8–10', $ss2, 'push-ups'],
                    ['V-bar seated row', '2', '10–12 RM', null, 'seated-cable-row-v-bar'],
                    ['Overhead press', '2', '6–8 RM', null, 'barbell-overhead-press'],
                    ['Rope pushdown', '3', '8–12 RM', "{$ss} Last set: drop set to failure.", 'rope-triceps-pushdown'],
                    ['Cable biceps curl', '3', '8–12 RM', "{$ss2} Last set: drop set to failure.", 'single-arm-cable-curl'],
                ]],
                ['Day 2 — Lower', [
                    ['Squat', '3', '5 RM', '3 light warm-up sets first.', 'barbell-back-squat'],
                    ['Leg press', '2', 'To failure', $ss, 'leg-press'],
                    ['Seated calf raise', '2', 'To failure', $ss2, 'seated-calf-raise'],
                    ['Leg curl machine (hamstrings)', '3', '8–10 RM'],
                    ['Leg extension', '2', 'To failure', $drop, 'leg-extension'],
                    ['Abs — Lying leg raises', '3', '30 sec', null, 'lying-leg-raises'],
                    ['Abs — Russian twist', '3', '20 sec', 'With a 10 kg dumbbell.', 'russian-twist'],
                ]],
                ['Day 3 — Rest · 10–15k steps', []],
                ['Day 4 — Pull (back, biceps, rear delts)', [
                    ['Chin pulldown', '3', '8–12 RM', '2 lighter warm-up sets first.', 'reverse-grip-lat-pulldown'],
                    ['Standing barbell row', '3', '6–8 RM', 'Pull the bar to just under your chest, elbows slightly out.', 'barbell-row'],
                    ['Back extensions', '2', 'To failure', 'Holding 15 kg.', 'back-extensions'],
                    ['Face pulls', '3', '8–12 RM', null, 'cable-face-pull'],
                    ['Strict biceps curl', '3', '6–8 RM', null, 'wall-barbell-curl'],
                    ['Seated one-arm cable pulldown', '1', 'To failure', 'Each arm.', 'single-arm-cable-pulldown'],
                ]],
                ['Day 5 — Push (chest, triceps, shoulders)', [
                    ['Incline Smith machine bench press', '3', '8–10 RM', 'Always 2 lighter warm-up sets first.', 'incline-smith-machine-press'],
                    ['Dips (bodyweight)', '3', 'To failure', null, 'dips'],
                    ['Machine chest press', '3', '6–8 RM', 'Go heavier, then a lighter drop set to failure.', 'machine-chest-press'],
                    ['Lateral raises', '3', '10–15 RM', null, 'dumbbell-lateral-raise'],
                    ['Dumbbell skull crushers', '3', '8–12 RM', null, 'dumbbell-skull-crushers'],
                    ['Abs — Crunches', '3', '25 sec', null, 'crunches'],
                    ['Abs — Mountain climbers', '3', '25 sec', null, 'mountain-climbers'],
                ]],
                ['Day 6 — Legs', [
                    ['Bulgarian split squat', '3', '8–12 RM each leg', 'With dumbbells. 1 warm-up set without weight.'],
                    ['Goblet squat', '2', '10–12 RM', 'With a dumbbell.', 'goblet-squat'],
                    ['Calf raises', '2', 'To failure', $drop, 'standing-calf-raise'],
                    ['Leg curl', '3', '10–15 RM', 'Last set: drop set to failure.'],
                    ['Leg extension', '2', 'To failure', "Until you can't lift one more.", 'leg-extension'],
                ]],
                ['Day 7 — Active rest · 10–15k steps', []],
            ],
            2 => [
                ['Day 1 — Push', [
                    ['Incline dumbbell chest press', '3', '8–12 RM', '2 light warm-up sets of 5–6 reps.', 'incline-dumbbell-press'],
                    ['Dips (bodyweight or assisted)', '3', 'To failure', null, 'dips'],
                    ['Machine chest press', '2', '8–12', $ss, 'machine-chest-press'],
                    ['Push-ups', '2', '5–8', $ss2, 'push-ups'],
                    ['Rope triceps pushdown', '3', '8–12 RM', null, 'rope-triceps-pushdown'],
                    ['Overhead press', null, '5–8 RM', null, 'barbell-overhead-press'],
                    ['Abs — Mountain climbers', '3', '25 sec', null, 'mountain-climbers'],
                    ['Abs — Lying leg raises', '3', '20 sec', null, 'lying-leg-raises'],
                ]],
                ['Day 2 — Pull', [
                    ['Pull-ups (assisted or bodyweight)', '2', 'To failure', '2 warm-up sets of 3–4 reps.', 'pull-ups'],
                    ['Standing barbell row', '3', '5–8 RM', 'Keep your lower back straight.', 'barbell-row'],
                    ['Face pulls', '3', '8–12 RM', $ss, 'cable-face-pull'],
                    ['Rope pullover', '3', '8–12 RM', $ss2, 'cable-straight-arm-pulldown'],
                    ['Back extensions', '2', 'To failure', null, 'back-extensions'],
                    ['Strict barbell curl', '3', '5–8 RM', null, 'wall-barbell-curl'],
                ]],
                ['Day 3 — Legs', [
                    ['Squat', '3', '3', '90% of your PR. 3 warm-up sets first.', 'barbell-back-squat'],
                    ['Bulgarian split squat', '3', '8–12 each leg'],
                    ['Leg curl', '2', 'To failure', $drop],
                    ['Leg extensions', '2', 'To failure', $drop, 'leg-extension'],
                    ['Calf raises', '2', '10–15', null, 'standing-calf-raise'],
                    ['Abs — Russian twist', '3', '30 sec', $ss, 'russian-twist'],
                    ['Abs — Crunches', '3', '30 sec', $ss2, 'crunches'],
                ]],
                ['Day 4 — Rest · 15k+ steps', []],
                ['Day 5 — Upper', [
                    ['Chin pulldown', '3', '8–12 RM', '2 warm-up sets first.', 'reverse-grip-lat-pulldown'],
                    ['Incline bench press', '3', '5–8 RM', null, 'incline-barbell-bench-press'],
                    ['Dips (bodyweight or assisted)', '2', 'To failure', null, 'dips'],
                    ['One-arm pulldown', '3', '8–12 RM each side', null, 'single-arm-cable-pulldown'],
                    ['Skull crushers', '3', '8–10 RM', $ss, 'dumbbell-skull-crushers'],
                    ['Dumbbell biceps curl', '3', '8–10 RM', $ss2, 'dumbbell-curl'],
                    ['Lateral raises', '2', 'To failure', $drop, 'dumbbell-lateral-raise'],
                ]],
                ['Day 6 — Lower', [
                    ['Goblet squat', '3', '8–12 RM', '2 warm-up sets first.', 'goblet-squat'],
                    ['Lunges in place', '3', '8–10 each leg', null, 'dumbbell-lunges'],
                    ['Romanian deadlift', '2', 'To failure', null, 'romanian-deadlift'],
                    ['Back extensions (hamstring focus)', '3', 'To failure', null, 'back-extensions'],
                    ['Calf raises', '2', 'To failure', $drop, 'standing-calf-raise'],
                    ['Abs — Weighted crunches', '2', '12', null, 'kneeling-cable-crunch'],
                    ['Abs — Weighted Russian twist', '2', '12', 'Both sides count as one rep.', 'russian-twist'],
                ]],
                ['Day 7 — Rest & recover · 15k+ steps', []],
            ],
            3 => [
                ['Day 1 — Chest & back', [
                    ['Incline dumbbell chest press', '3', '8–12', '2 light warm-up sets, then add 5–10 kg.', 'incline-dumbbell-press'],
                    ['Lat pulldown', '3', '8–12 RM', 'Add 10 kg.', 'lat-pulldown'],
                    ['Cable flys', '2', 'To failure', $drop, 'cable-crossover-fly'],
                    ['V-bar seated row', '2', '8–12 RM', null, 'seated-cable-row-v-bar'],
                    ['Machine chest press', '2', 'To failure', $drop, 'machine-chest-press'],
                    ['Back extensions', '2', 'To failure', 'Holding 15 kg.', 'back-extensions'],
                ]],
                ['Day 2 — Legs', [
                    ['Squat', '5', '5 RM', 'Add 10 kg. 3 warm-up sets before the working sets.', 'barbell-back-squat'],
                    ['Leg extensions', '2', 'To failure', $drop, 'leg-extension'],
                    ['Romanian deadlift', '3', '8–12 RM', null, 'romanian-deadlift'],
                    ['Calf raises', null, null, null, 'standing-calf-raise'],
                    ['Abs — Lying leg raises', '2', '30 sec', null, 'lying-leg-raises'],
                    ['Abs — Weighted crunches', '2', '8–12', 'With 10 kg.', 'kneeling-cable-crunch'],
                ]],
                ['Day 3 — Arms & shoulders', [
                    ['Close-grip bench press', '3', '6–8 RM', '2 warm-up sets first.', 'close-grip-bench-press'],
                    ['Rope triceps pushdown', '2', '8–12', $drop, 'rope-triceps-pushdown'],
                    ['Dumbbell hammer curls', '3', '8–12', 'Keep your elbows and arms close to your body.', 'hammer-curl'],
                    ['Strict barbell curl', '3', '6–8 RM', null, 'wall-barbell-curl'],
                    ['Seated dumbbell overhead press', '3', '6–8 RM', null, 'seated-dumbbell-shoulder-press'],
                ]],
                ['Day 4 — Rest & recover · 15k steps', []],
                ['Day 5 — Upper', [
                    ['Pull-ups (bodyweight or assisted)', '3', '8–12', '2 warm-up sets of 3 reps only.', 'pull-ups'],
                    ['Dips (bodyweight or assisted)', '3', 'To failure', null, 'dips'],
                    ['Standing barbell row', '3', '6–8 RM', null, 'barbell-row'],
                    ['Incline machine chest press', '3', '8–12 RM', $drop, 'incline-machine-chest-press'],
                    ['Rope triceps pushdown', '3', '8–12 RM', $ss, 'rope-triceps-pushdown'],
                    ['Cable biceps curl', '3', '8–12 RM', $ss2, 'single-arm-cable-curl'],
                    ['Lateral raises', '3', 'To failure', $drop, 'dumbbell-lateral-raise'],
                ]],
                ['Day 6 — Lower', [
                    ['Bulgarian split squat', '3', '8–10 each leg', '1 warm-up set with bodyweight.'],
                    ['Goblet squat', '2', 'To failure', null, 'goblet-squat'],
                    ['Leg curl (hamstrings)', '3', 'To failure'],
                    ['Leg press', '2', 'To failure', $ss, 'leg-press'],
                    ['Calf raises', '2', 'To failure', $ss2, 'standing-calf-raise'],
                    ['Abs — Weighted crunches', '2', '8–12', 'With 10 kg.', 'kneeling-cable-crunch'],
                    ['Abs — Side-to-side plank crunches', '2', '20 sec', null, 'plank-hip-twists'],
                ]],
                ['Day 7 — Rest', []],
            ],
            4 => [
                ['Day 1 — Chest & shoulders', [
                    ['Incline dumbbell chest press', '3', '8–12 RM', '2 light warm-up sets.', 'incline-dumbbell-press'],
                    ['Dips (bodyweight)', '3', 'To failure', 'Finish the last set with slow negatives.', 'dips'],
                    ['Cable flys', '2', 'To failure', $drop, 'cable-crossover-fly'],
                    ['Machine chest press', '2', '8–12 RM', null, 'machine-chest-press'],
                    ['Overhead press', '2', '6–8', 'Heavy.', 'barbell-overhead-press'],
                    ['Abs — Mountain climbers', '2', '25 sec', null, 'mountain-climbers'],
                    ['Abs — Weighted crunches', '2', '12', 'With 15 kg.', 'kneeling-cable-crunch'],
                ]],
                ['Day 2 — Back & rear delts', [
                    ['Lat pulldown', '3', '8–12 RM', '2 warm-up sets first.', 'lat-pulldown'],
                    ['Seated V-bar row', '2', 'To failure', $drop, 'seated-cable-row-v-bar'],
                    ['Back extensions', '3', 'To failure', 'Holding 10 kg.', 'back-extensions'],
                    ['Machine row', '2', 'To failure', $drop, 'seated-machine-row'],
                    ['Face pulls', '2', '10–12', $drop, 'cable-face-pull'],
                ]],
                ['Day 3 — Legs', [
                    ['Bulgarian split squat', '3', '8–12 RM', '2 warm-up sets without weight.'],
                    ['Goblet squat', '2', 'To failure', 'Stop once your lower back can no longer stay straight.', 'goblet-squat'],
                    ['Leg press', '2', '8–12', $ss, 'leg-press'],
                    ['Calf raises', '2', '8–12', $ss2, 'standing-calf-raise'],
                    ['Leg curl (hamstrings)', '2', 'To failure', $drop],
                    ['Abs — Lying leg raises', '4', '30 sec', null, 'lying-leg-raises'],
                ]],
                ['Day 4 — Arms', [
                    ['Skull crushers', '3', '8–12', '1 warm-up set first.', 'dumbbell-skull-crushers'],
                    ['Dumbbell biceps curl', '3', '8–12', null, 'dumbbell-curl'],
                    ['Rope pushdown', '2', 'To failure', $drop, 'rope-triceps-pushdown'],
                    ['Hammer curls', '3', '8–12', null, 'hammer-curl'],
                    ['Straight-bar triceps pushdown', '2', 'To failure', "{$drop} Same movement as the rope pushdown, with a straight bar.", 'rope-triceps-pushdown'],
                ]],
                ['Day 5 — Rest · 15k steps', []],
                ['Day 6 — Legs (light)', [
                    ['Leg extensions', '3', 'To failure', '3 warm-up sets first.', 'leg-extension'],
                    ['Romanian deadlift (hamstrings)', '3', '8–12', null, 'romanian-deadlift'],
                    ['Leg press', '2', '10–15', null, 'leg-press'],
                    ['Calf raises', null, null, null, 'standing-calf-raise'],
                    ['Abs — Weighted Russian twist', '3', '8–12', null, 'russian-twist'],
                    ['Abs — Frog crunches', '2', '30 sec'],
                ]],
                ['Day 7 — Upper', [
                    ['Incline bench press', '3', '6–8', '1 warm-up set first.', 'incline-barbell-bench-press'],
                    ['Weighted dips', '3', 'To failure', 'With 5–10 kg.', 'dips'],
                    ['Chin pulldown', '3', '8–12', null, 'reverse-grip-lat-pulldown'],
                    ['Standing barbell row', '2', 'To failure', null, 'barbell-row'],
                    ['Cable biceps curl', '3', '10–12', $ss, 'single-arm-cable-curl'],
                    ['Rope triceps pushdown', '3', '10–12', $ss2, 'rope-triceps-pushdown'],
                    ['Lateral raises', '2', 'To failure', $drop, 'dumbbell-lateral-raise'],
                ]],
            ],
            5 => [
                ['Day 1 — Rest', []],
                ['Day 2 — Push', [
                    ['Incline bench press', '4', '6–8 RM', '2 light warm-up sets.', 'incline-barbell-bench-press'],
                    ['Weighted dips', '4', '5–8 RM', null, 'dips'],
                    ['Cable flys', '3', '8–12', $ss, 'cable-crossover-fly'],
                    ['Push-ups', '3', '6–8', $ss2, 'push-ups'],
                    ['Overhead press', '3', '6–8 RM', null, 'barbell-overhead-press'],
                    ['Rope pushdown', '3', 'To failure', $ss, 'rope-triceps-pushdown'],
                    ['Rope overhead triceps extension', '3', 'To failure', "{$ss2} Same rope.", 'cable-overhead-triceps-extension'],
                    ['Abs — Mountain climbers', '3', '25 sec', null, 'mountain-climbers'],
                    ['Abs — Plank shoulder taps', '3', '25 sec', null, 'plank-shoulder-taps'],
                ]],
                ['Day 3 — Pull', [
                    ['Pull-ups', '3', 'To failure', '1 warm-up set of 3 reps only.', 'pull-ups'],
                    ['Standing barbell row', '4', '6–8 RM', null, 'barbell-row'],
                    ['Back extensions', '4', 'To failure', 'Holding 15 kg.', 'back-extensions'],
                    ['Face pulls', '4', '10–12 RM', null, 'cable-face-pull'],
                    ['Barbell biceps curl', '4', '6–8 RM', null, 'barbell-curl'],
                ]],
                ['Day 4 — Legs', [
                    ['Squat', '4', '6 RM', '2 warm-up sets of 8 reps.', 'barbell-back-squat'],
                    ['Bulgarian split squat', '4', '8–12 RM'],
                    ['Goblet squat', '2', 'To failure', null, 'goblet-squat'],
                    ['Leg extensions', '2', 'To failure', $drop, 'leg-extension'],
                    ['Calf raises', null, null, null, 'standing-calf-raise'],
                    ['Abs — Lying leg raises', '4', '30 sec', null, 'lying-leg-raises'],
                    ['Abs — Crunches', '2', '12', null, 'crunches'],
                ]],
                ['Day 5 — Rest', []],
                ['Day 6 — Upper', [
                    ['Lat pulldown', '4', '8–12 RM', "{$ss} 1 warm-up set of 10 reps.", 'lat-pulldown'],
                    ['Chin pulldown', '4', '8–12 RM', $ss2, 'reverse-grip-lat-pulldown'],
                    ['Seated V-bar row', '3', '8–12 RM', $drop, 'seated-cable-row-v-bar'],
                    ['Dips (bodyweight)', '3', 'To failure', null, 'dips'],
                    ['Cable flys', '2', 'To failure', $drop, 'cable-crossover-fly'],
                    ['Rope triceps pushdown', '3', '10–12 RM', $ss, 'rope-triceps-pushdown'],
                    ['Cable biceps curl', '3', '10–12 RM', $ss2, 'single-arm-cable-curl'],
                    ['Lateral raises', null, null, null, 'dumbbell-lateral-raise'],
                ]],
                ['Day 7 — Lower', [
                    ['Goblet squat', '3', '8–12 RM', '2 warm-up sets first.', 'goblet-squat'],
                    ['Leg press', '4', '8–12 RM', null, 'leg-press'],
                    ['Romanian deadlift', '3', '8–12 RM', null, 'romanian-deadlift'],
                    ['Calf raises', null, null, null, 'standing-calf-raise'],
                    ['Abs — Russian twist', '3', '8–12', 'With 10 kg.', 'russian-twist'],
                    ['Abs — Lying leg raises', '3', '25 sec', null, 'lying-leg-raises'],
                ]],
            ],
            6 => [
                ['Day 1 — Rest · 15k+ steps', []],
                ['Day 2 — Chest & back', [
                    ['Incline dumbbell press', '4', '8–12 RM', 'Increase the weight. 2 easy warm-up sets.', 'incline-dumbbell-press'],
                    ['Lat pulldown', '4', '8–12 RM', $drop, 'lat-pulldown'],
                    ['Back extensions', '2', 'To failure', 'Holding 20 kg.', 'back-extensions'],
                    ['Weighted dips', '3', '8–12 RM', null, 'dips'],
                    ['Standing barbell row', '2', '6–10 RM', null, 'barbell-row'],
                    ['Machine chest press', '3', 'To failure', null, 'machine-chest-press'],
                ]],
                ['Day 3 — Legs', [
                    ['Bulgarian split squat', '3', '8–12 RM', "{$ss} Warm up with bodyweight squats."],
                    ['Goblet squat', '3', '8–12 RM', $ss2, 'goblet-squat'],
                    ['Leg press', '3', '8–12 RM', null, 'leg-press'],
                    ['Leg curl (hamstrings)', '4', '8–12 RM'],
                    ['Leg extensions', '2', 'To failure', null, 'leg-extension'],
                    ['Calf raises', null, null, null, 'standing-calf-raise'],
                    ['Abs — Side-to-side leg raises with toe touch', '3', '30 sec'],
                    ['Abs — Weighted crunches', '3', '8–10', 'With 15 kg.', 'kneeling-cable-crunch'],
                ]],
                ['Day 4 — Arms & shoulders', [
                    ['Strict biceps curl', '3', '8–12 RM', '1 warm-up set first.', 'wall-barbell-curl'],
                    ['Hammer curls', '3', '8–12 RM', null, 'hammer-curl'],
                    ['Skull crushers', '3', '8–12 RM', null, 'dumbbell-skull-crushers'],
                    ['Rope pushdown', '4', 'To failure', $drop, 'rope-triceps-pushdown'],
                    ['Seated dumbbell overhead press', '3', '8–12 RM', null, 'seated-dumbbell-shoulder-press'],
                ]],
                ['Day 5 — Rest · 15k+ steps', []],
                ['Day 6 — Lower', [
                    ['Squat', '5', '5 RM', 'Increase the weight. 3 warm-up sets first.', 'barbell-back-squat'],
                    ['Leg extensions', '4', 'To failure', $drop, 'leg-extension'],
                    ['Leg curl (hamstrings)', '2', 'To failure', $drop],
                    ['Romanian deadlift', '3', '8–12', null, 'romanian-deadlift'],
                    ['Calf raises', null, null, null, 'standing-calf-raise'],
                    ['Abs — Lying leg raises', '3', '30 sec', null, 'lying-leg-raises'],
                    ['Abs — Russian twist', '3', 'To failure', 'With 15 kg.', 'russian-twist'],
                ]],
                ['Day 7 — Upper', [
                    ['Incline Smith machine bench press', '3', '8–12 RM', '2 warm-up sets first.', 'incline-smith-machine-press'],
                    ['Dips', '3', 'To failure', $ss, 'dips'],
                    ['Push-ups', '3', 'To failure', $ss2, 'push-ups'],
                    ['Pull-ups (bodyweight)', '3', 'To failure', 'Finish the last set with slow negatives.', 'pull-ups'],
                    ['Standing barbell row', '4', '8–12 RM', null, 'barbell-row'],
                    ['Lateral raises', '2', 'To failure', $drop, 'dumbbell-lateral-raise'],
                    ['Skull crushers', '3', '8–10 RM', $ss, 'dumbbell-skull-crushers'],
                    ['EZ-bar curl', '3', '8–10 RM', $ss2, 'barbell-curl'],
                ]],
            ],
            7 => [
                ['Day 1 — Rest', []],
                ['Day 2 — Chest & shoulders', [
                    ['Incline dumbbell bench press', '3', '8–10 RM', '2 warm-up sets first.', 'incline-dumbbell-press'],
                    ['Dips (bodyweight)', '4', 'To failure', null, 'dips'],
                    ['Cable flys', '4', '8–12 RM', $ss, 'cable-crossover-fly'],
                    ['Push-ups', '4', '8–12', $ss2, 'push-ups'],
                    ['Overhead press', '2', '6–8 RM', null, 'barbell-overhead-press'],
                    ['Lateral raises', '3', 'To failure', $drop, 'dumbbell-lateral-raise'],
                    ['Abs — Mountain climbers', '3', '25 sec', null, 'mountain-climbers'],
                    ['Abs — Russian twist', '3', '25 sec', null, 'russian-twist'],
                ]],
                ['Day 3 — Back & rear delts', [
                    ['Pull-ups (bodyweight)', '4', 'To failure', '1 warm-up set first.', 'pull-ups'],
                    ['Standing barbell row', '3', '6–8 RM', null, 'barbell-row'],
                    ['Back extensions', '4', 'To failure', 'Holding 20 kg.', 'back-extensions'],
                    ['Face pulls', '3', '8–12 RM', $ss, 'cable-face-pull'],
                    ['Rope pullover', '3', '8–12 RM', $ss2, 'cable-straight-arm-pulldown'],
                    ['Barbell biceps curl', '3', '6–10 RM', null, 'barbell-curl'],
                    ['Hammer curls', '2', 'To failure', null, 'hammer-curl'],
                ]],
                ['Day 4 — Legs', [
                    ['Goblet squat', '3', '8–12 RM', '2 warm-up sets first.', 'goblet-squat'],
                    ['Leg press', '4', '8–12 RM', $ss, 'leg-press'],
                    ['Calf press', '4', '8–12 RM', $ss2, 'standing-calf-raise'],
                    ['Leg curl (hamstrings)', '4', 'To failure', $drop],
                    ['Leg extensions', '4', 'To failure', 'Two drop sets on the last set.', 'leg-extension'],
                    ['Abs — Lying leg raises', '3', '12', null, 'lying-leg-raises'],
                    ['Abs — Crunches', '3', '30 sec', null, 'crunches'],
                ]],
                ['Day 5 — Arms & shoulders', [
                    ['Skull crushers', '3', '8–12 RM', '1 warm-up set first.', 'dumbbell-skull-crushers'],
                    ['Rope pushdown', '3', '8–12 RM', $ss, 'rope-triceps-pushdown'],
                    ['Rope overhead triceps extension', '3', '8–12 RM', $ss2, 'cable-overhead-triceps-extension'],
                    ['Hammer curls', '4', '8–12', null, 'hammer-curl'],
                    ['Cable biceps curl', '4', 'To failure', $drop, 'single-arm-cable-curl'],
                    ['Lateral raises', '4', 'To failure', $drop, 'dumbbell-lateral-raise'],
                ]],
                ['Day 6 — Rest', []],
                ['Day 7 — Rest · 15k+ steps', []],
            ],
            8 => [
                ['Day 1 — Push', [
                    ['Incline bench press', '4', '8–10 RM', '2 warm-up sets first.', 'incline-barbell-bench-press'],
                    ['Cable flys', '3', '8–12 RM', $ss, 'cable-crossover-fly'],
                    ['Push-ups', '3', '6–10', $ss2, 'push-ups'],
                    ['Incline machine chest press', '2', 'To failure', $drop, 'incline-machine-chest-press'],
                    ['Weighted dips', '2', '5–6 RM', null, 'dips'],
                    ['Overhead press', '3', '6–8 RM', null, 'barbell-overhead-press'],
                    ['Skull crushers', '3', '8–12 RM', null, 'dumbbell-skull-crushers'],
                    ['Abs — Crunches', '3', '40 sec', null, 'crunches'],
                    ['Abs — Lying leg raises', '3', '35 sec', null, 'lying-leg-raises'],
                ]],
                ['Day 2 — Pull', [
                    ['Lat pulldown', '3', '8–12 RM', "{$ss} 1 warm-up set first.", 'lat-pulldown'],
                    ['Chin pulldown', '3', '8–12 RM', $ss2, 'reverse-grip-lat-pulldown'],
                    ['Seated V-bar row', '3', '8–12 RM', null, 'seated-cable-row-v-bar'],
                    ['Face pulls', '4', 'To failure', $drop, 'cable-face-pull'],
                    ['Back extensions', '3', 'To failure', 'Holding 20 kg.', 'back-extensions'],
                    ['Seated dumbbell curls', '3', '8–12 RM', null, 'dumbbell-curl'],
                ]],
                ['Day 3 — Legs', [
                    ['Squat', '3', '5 RM', '3 warm-up sets first.', 'barbell-back-squat'],
                    ['Bulgarian split squat', '3', '8–12 RM each leg'],
                    ['Leg curl (hamstrings)', '2', 'To failure'],
                    ['Leg extensions', '2', 'To failure', $drop, 'leg-extension'],
                    ['Calf raises', '3', 'To failure', null, 'standing-calf-raise'],
                    ['Abs — Russian twist', '3', '25 sec', null, 'russian-twist'],
                    ['Abs — Crunches', '3', '25 sec', null, 'crunches'],
                ]],
                ['Day 4 — Rest · 15k+ steps', []],
                ['Day 5 — Upper', [
                    ['Pull-ups (bodyweight)', '3', 'To failure', '1 warm-up set first.', 'pull-ups'],
                    ['Dips', '3', '12–15 RM', null, 'dips'],
                    ['Standing barbell row', '3', '8–10 RM', null, 'barbell-row'],
                    ['Incline machine chest press', '3', 'To failure', $drop, 'incline-machine-chest-press'],
                    ['Overhead press', '3', '8 RM', null, 'barbell-overhead-press'],
                    ['Rope triceps pushdown', '3', '8–12 RM', $ss, 'rope-triceps-pushdown'],
                    ['Cable biceps curl', '3', '8–12 RM', $ss2, 'single-arm-cable-curl'],
                ]],
                ['Day 6 — Legs', [
                    ['Goblet squat', '4', '8–12 RM', '1 warm-up set first.', 'goblet-squat'],
                    ['Leg press', '4', 'To failure', null, 'leg-press'],
                    ['Romanian deadlift', '3', '8–12', 'Keep your lower back straight.', 'romanian-deadlift'],
                    ['Leg extension', '2', 'To failure', $drop, 'leg-extension'],
                    ['Calf raises', null, null, null, 'standing-calf-raise'],
                    ['Abs — Hanging leg raises', '3', '6–8 RM', null, 'hanging-leg-raises'],
                    ['Abs — Cable crunches', '3', '8–12 RM', null, 'kneeling-cable-crunch'],
                ]],
                ['Day 7 — Rest · 15k+ steps', []],
            ],
            9 => [
                ['Day 1 — Pull', [
                    ['Pull-ups', '5', '10 RM', '2 warm-up sets first.', 'pull-ups'],
                    ['Standing barbell row', '5', '10–12 RM', null, 'barbell-row'],
                    ['Face pulls', '5', 'To failure', null, 'cable-face-pull'],
                    ['Back extensions', '2', '15 RM', 'Holding 20 kg.', 'back-extensions'],
                    ['Strict barbell curl', '4', '10–12 RM', null, 'wall-barbell-curl'],
                    ['Abs — Lying leg raises', '3', '12–15', null, 'lying-leg-raises'],
                    ['Abs — Russian twist', '3', '10', 'Both sides count as one rep.', 'russian-twist'],
                ]],
                ['Day 2 — Push', [
                    ['Incline chest press', '5', '10 RM', '2 light warm-up sets.', 'incline-dumbbell-press'],
                    ['Dips (bodyweight)', '5', '10–15 RM', null, 'dips'],
                    ['Overhead press', '5', '8–10 RM', null, 'barbell-overhead-press'],
                    ['Push-ups', '4', '30 sec', null, 'push-ups'],
                    ['JM press', '5', '5–8 RM'],
                ]],
                ['Day 3 — Legs', [
                    ['Squat', '5', '5–8 RM', '3 warm-up sets first.', 'barbell-back-squat'],
                    ['Bulgarian split squat', '5', '8–10 RM each leg'],
                    ['Leg curl (hamstrings)', '5', '10–15 RM'],
                    ['Calf raises', null, null, null, 'standing-calf-raise'],
                    ['Abs — Crunches', '3', '12', null, 'crunches'],
                    ['Abs — Russian twist', '3', '25 sec', null, 'russian-twist'],
                    ['Abs — Plank shoulder taps', '2', '20 sec', null, 'plank-shoulder-taps'],
                ]],
                ['Day 4 — Rest · 15k+ steps', []],
                ['Day 5 — Chest & back', [
                    ['Standing barbell row', '5', '8–10 RM', '1 warm-up set first.', 'barbell-row'],
                    ['Incline dumbbell chest press', '5', '8–10 RM', null, 'incline-dumbbell-press'],
                    ['Lat pulldown', '4', '8–12 RM', $ss, 'lat-pulldown'],
                    ['Chin pulldown', '4', '8–12 RM', $ss2, 'reverse-grip-lat-pulldown'],
                    ['Machine chest press', '5', '10–12 RM', null, 'machine-chest-press'],
                ]],
                ['Day 6 — Legs', [
                    ['Goblet squat', '3', '8–12 RM', '2 warm-up sets first.', 'goblet-squat'],
                    ['Leg press', '3', 'To failure', null, 'leg-press'],
                    ['Romanian deadlift', '5', '5–8 RM', null, 'romanian-deadlift'],
                    ['Leg extensions', '3', 'To failure', $drop, 'leg-extension'],
                    ['Abs — Weighted crunches', '3', '8–12', 'With 15 kg.', 'kneeling-cable-crunch'],
                    ['Abs — Lying leg raises', '3', '12', null, 'lying-leg-raises'],
                ]],
                ['Day 7 — Arms & shoulders', [
                    ['JM press', '3', '5–8 RM', '2 warm-up sets first.'],
                    ['Dumbbell biceps curls', '3', '8–12 RM', null, 'dumbbell-curl'],
                    ['Rope triceps pushdown', '3', '8–10 RM', $drop, 'rope-triceps-pushdown'],
                    ['Hammer curls', '3', '5–8 RM', 'Heavy.', 'hammer-curl'],
                    ['Straight-bar triceps pushdown', '3', '8–12 RM', 'Same movement as the rope pushdown, with a straight bar.', 'rope-triceps-pushdown'],
                    ['Cable biceps curl', '3', 'To failure', $drop, 'single-arm-cable-curl'],
                ]],
            ],
            10 => [
                ['Day 1 — Rest · 15k+ steps', []],
                ['Day 2 — Back & rear delts', [
                    ['Lat pulldown', '3', '8–12', '2 warm-up sets first.', 'lat-pulldown'],
                    ['V-bar seated row', '4', '6–10 RM', $drop, 'seated-cable-row-v-bar'],
                    ['Face pulls', '3', '8–12 RM', $drop, 'cable-face-pull'],
                    ['Back extensions', '3', '8–12 RM', 'Holding 20 kg.', 'back-extensions'],
                    ['Barbell biceps curl', '3', '5–10 RM', null, 'barbell-curl'],
                    ['Machine row', '3', 'To failure', $drop, 'seated-machine-row'],
                    ['Abs — Leg raises', '3', '10', null, 'lying-leg-raises'],
                    ['Abs — Cable crunches', '3', '8–12', null, 'kneeling-cable-crunch'],
                ]],
                ['Day 3 — Chest & shoulders', [
                    ['Incline dumbbell chest press', '3', '8–12 RM', '2 warm-up sets first.', 'incline-dumbbell-press'],
                    ['Overhead press', '5', '5 RM', null, 'barbell-overhead-press'],
                    ['Weighted dips', '3', '8–12 RM', 'With 15–20 kg.', 'dips'],
                    ['Rope triceps pushdown', '3', 'To failure', $drop, 'rope-triceps-pushdown'],
                    ['Machine chest press', '4', 'To failure', $drop, 'machine-chest-press'],
                ]],
                ['Day 4 — Legs', [
                    ['Squat', '5', '6 RM', '3 warm-up sets first.', 'barbell-back-squat'],
                    ['Bulgarian split squat', '4', '8–12 RM each leg'],
                    ['Leg press', '2', 'To failure', null, 'leg-press'],
                    ['Leg curl (hamstrings)', '3', 'To failure', $drop],
                    ['Leg extensions', '2', 'To failure', null, 'leg-extension'],
                    ['Calf raises', null, null, null, 'standing-calf-raise'],
                    ['Abs — Lying leg raises', '3', '30 sec', null, 'lying-leg-raises'],
                    ['Abs — Russian twist', '3', '30 sec', null, 'russian-twist'],
                ]],
                ['Day 5 — Arms', [
                    ['JM press', '3', '8–10 RM', '2 warm-up sets first.'],
                    ['Barbell biceps curl', '3', '8 RM', null, 'barbell-curl'],
                    ['Hammer curls', '3', '8–12 RM', null, 'hammer-curl'],
                    ['Straight-bar cable pushdown', '3', '8–12 RM', $ss, 'rope-triceps-pushdown'],
                    ['Straight-bar overhead cable extension', '3', '8–12 RM', $ss2, 'cable-overhead-triceps-extension'],
                    ['Cable biceps curl', '3', 'To failure', $drop, 'single-arm-cable-curl'],
                ]],
                ['Day 6 — Rest · 15k+ steps', []],
                ['Day 7 — Rest · 15k+ steps', []],
            ],
            11 => [
                ['Day 1 — Anterior (chest, shoulders, triceps, quads)', [
                    ['Incline dumbbell chest press', '3', '8–12 RM', 'Circuit A (1/3): go straight into the next exercise.', 'incline-dumbbell-press'],
                    ['Overhead dumbbell triceps extension', '3', '12', 'Circuit A (2/3).', 'dumbbell-overhead-triceps-extension'],
                    ['Push-ups', '3', '30 sec', 'Circuit A (3/3): rest 3 min, then repeat the circuit.', 'push-ups', '3 min'],
                    ['Seated dumbbell overhead press', '3', '6–8 RM', 'Circuit B (1/3).', 'seated-dumbbell-shoulder-press'],
                    ['Lateral raises', '3', '8–12', 'Circuit B (2/3).', 'dumbbell-lateral-raise'],
                    ['Grave digger overhead press', '3', '30 sec', 'Circuit B (3/3): rest 3 min, then repeat the circuit.', 'half-kneeling-curl-and-press', '3 min'],
                    ['Reverse lunges', '3', '8 RM', 'Circuit C (1/3).', 'dumbbell-lunges'],
                    ['Goblet squat', '3', '9–12 RM', 'Circuit C (2/3).', 'goblet-squat'],
                    ['Wall sit', '3', '30 sec', 'Circuit C (3/3): rest 3 min, then repeat the circuit.', 'wall-sit', '3 min'],
                ]],
                ['Day 2 — Posterior (back, rear delts, biceps, hamstrings)', [
                    ['Lat pulldown', '3', '8', 'Circuit A (1/3): go straight into the next exercise.', 'lat-pulldown'],
                    ['Chin pulldown', '3', '10–12 RM', 'Circuit A (2/3).', 'reverse-grip-lat-pulldown'],
                    ['Dumbbell biceps curl', '3', '30 sec', 'Circuit A (3/3): rest-pause, then rest 3 min and repeat the circuit.', 'dumbbell-curl', '3 min'],
                    ['Face pulls', '3', '8–10', 'Circuit B (1/3).', 'cable-face-pull'],
                    ['Rope pullover', '3', '10–12', 'Circuit B (2/3).', 'cable-straight-arm-pulldown'],
                    ['V-bar seated row', '3', '8–15 RM', 'Circuit B (3/3): rest 3 min, then repeat the circuit.', 'seated-cable-row-v-bar', '3 min'],
                    ['Romanian deadlift', '3', '8–12', 'Circuit C (1/3).', 'romanian-deadlift'],
                    ['Frog hip thrust', '3', '10–12', 'Circuit C (2/3).', 'dumbbell-glute-bridge'],
                    ['Marchers', '3', '30 sec', 'Circuit C (3/3): rest 3 min, then repeat the circuit.', 'glute-bridge-march', '3 min'],
                    ['Abs — Leg raises', '3', '8–12 RM', null, 'lying-leg-raises'],
                    ['Abs — Cable crunches', '3', '10', null, 'kneeling-cable-crunch'],
                ]],
                ['Day 3 — Rest', []],
                ['Day 4 — Push', [
                    ['Incline bench press', '3', '8–10 RM', '1 warm-up set first.', 'incline-barbell-bench-press'],
                    ['Cable flys', null, '8–12 RM', $ss, 'cable-crossover-fly'],
                    ['Push-ups', null, null, $ss2, 'push-ups'],
                    ['Dips', '3', '8–12 RM', $ss, 'dips'],
                    ['Bench dips', '3', '8–12 RM', $ss2, 'bench-dips'],
                    ['JM press', '5', '8–12 RM'],
                    ['Lateral raises', '3', 'To failure', $drop, 'dumbbell-lateral-raise'],
                    ['Incline machine chest press', '3', 'To failure', null, 'incline-machine-chest-press'],
                    ['Abs — Mountain climbers', '3', '20 sec', null, 'mountain-climbers'],
                    ['Abs — Weighted crunches', '3', '12', 'With 20 kg.', 'kneeling-cable-crunch'],
                ]],
                ['Day 5 — Pull', [
                    ['Weighted pull-ups', '3', '8 RM', 'With 10 kg. 1 warm-up set first.', 'pull-ups'],
                    ['Standing barbell row', '3', '8–12 RM', null, 'barbell-row'],
                    ['Face pulls', '4', 'To failure', $ss, 'cable-face-pull'],
                    ['Rope pullover', '4', 'To failure', $ss2, 'cable-straight-arm-pulldown'],
                    ['Back extensions', '3', 'To failure', 'Holding 20 kg, then a drop set without weight.', 'back-extensions'],
                    ['Hammer curls', '4', '8–12 RM', null, 'hammer-curl'],
                ]],
                ['Day 6 — Legs', [
                    ['Bulgarian split squat', '3', '8–12 RM each leg', $ss],
                    ['Goblet squat', '3', '8 RM', $ss2, 'goblet-squat'],
                    ['Leg extensions', '2', 'To failure', $drop, 'leg-extension'],
                    ['Romanian deadlift', '3', '8–12 RM', null, 'romanian-deadlift'],
                    ['Leg curl (hamstrings)', '3', 'To failure'],
                    ['Calf raises', null, null, null, 'standing-calf-raise'],
                    ['Abs — Russian twist', '3', 'To failure', null, 'russian-twist'],
                    ['Abs — Plank shoulder taps', '3', '30 sec', null, 'plank-shoulder-taps'],
                ]],
                ['Day 7 — Rest · 15k+ steps', []],
            ],
            12 => [
                ['Day 1 — Rest', []],
                ['Day 2 — Final test: max out every exercise', [
                    ['Bulgarian split squat', '1', '8 each leg', '2 light warm-up sets, then 1 set with your 3–5 RM weight.', null, '5–8 min'],
                    ['Pull-ups', '1', 'To failure', '3 warm-up sets, then 1 set to failure with bodyweight.', 'pull-ups', '5–8 min'],
                    ['Standing barbell row', '1', '8', '2 light warm-up sets, then 1 set with your 3–5 RM weight.', 'barbell-row', '5–8 min'],
                    ['Incline bench press', '1', '8', '2 light warm-up sets, then 1 set with your 3–5 RM weight.', 'incline-barbell-bench-press', '5–8 min'],
                    ['Overhead press', '1', '8', '1 warm-up set, then 1 set with your 3–5 RM weight.', 'barbell-overhead-press', '5–8 min'],
                    ['Barbell biceps curl', '1', '8', '2 warm-up sets, then 1 set with your 3–5 RM weight.', 'barbell-curl', '5–8 min'],
                    ['JM press', '1', '8', '2 warm-up sets, then 1 set with your 3–5 RM weight.', null, '5–8 min'],
                ]],
            ],
        ];
    }
}
