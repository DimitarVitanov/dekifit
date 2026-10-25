<?php

namespace Database\Seeders;

use App\Models\ExerciseVideo;
use Illuminate\Database\Seeder;

/**
 * Coach Dejan's exercise videos, grouped by body part. Each clip is
 * storage/app/private/exercise-videos/{slug}.mp4 (streamed only to clients the
 * coach has unlocked) with a public poster at public/videos/exercises/{slug}.jpg.
 *
 * Safe to run on production and to re-run: existing entries (matched by file)
 * are left alone, so tips the coach edits in the admin are never overwritten.
 *   php artisan db:seed --class=ExerciseLibrarySeeder --force
 */
class ExerciseLibrarySeeder extends Seeder
{
    public const LIBRARY = [
        'Chest' => [
            ['machine-chest-press', 'Machine chest press', 'Set the seat so the handles line up with mid-chest. Keep your shoulder blades pinned to the pad and press without slamming into lockout.'],
            ['incline-machine-chest-press', 'Incline machine chest press', 'Set the seat so the handles start at upper-chest height. Drive through your palms, keep your back on the pad and control the way back.'],
            ['incline-barbell-bench-press', 'Incline barbell bench press', 'Bench at 30–45°. Shoulder blades back and down, lower the bar to your upper chest and press it back up over your shoulders.'],
            ['incline-dumbbell-press', 'Incline dumbbell press', 'Bench at 30–45°. Lower the dumbbells to the sides of your upper chest with elbows slightly tucked, then press up and in.'],
            ['incline-smith-machine-press', 'Incline Smith machine press', 'Line the bench up so the bar touches your upper chest. Elbows at roughly 45° and full control on the way down.'],
            ['cable-crossover-fly', 'Cable crossover fly', 'Keep a slight, fixed bend in the elbows. Bring the handles together in a hugging arc, squeeze your chest and let them back slowly.'],
            ['push-ups', 'Push-ups', 'Hands just wider than your shoulders, body in one straight line. Lower your chest to the floor and push back up without letting your hips sag.'],
            ['dips', 'Dips', 'Lean slightly forward to bring in the chest. Lower until your upper arms are about parallel to the floor, then press back up.'],
            ['negative-dips', 'Negative dips', 'Start at the top and lower yourself slowly over 3–5 seconds. Step back up and repeat. Builds the strength for full dips.'],
        ],
        'Back' => [
            ['band-assisted-pull-ups', 'Band-assisted pull-ups', 'Loop the band over the bar and put your foot in it. Pull your chest towards the bar and lower under control. Move to a lighter band as you get stronger.'],
            ['pull-ups', 'Pull-ups', 'Hands slightly wider than your shoulders. Start from a dead hang, pull your chest towards the bar and lower all the way down.'],
            ['lat-pulldown', 'Lat pulldown', 'Wide grip, chest up. Pull the bar to your upper chest by driving your elbows down, then let it rise slowly into a full stretch.'],
            ['reverse-grip-lat-pulldown', 'Reverse-grip lat pulldown', 'Underhand grip, about shoulder-width. Pull to your upper chest with elbows close to your body and control the return.'],
            ['single-arm-cable-pulldown', 'Single-arm cable pulldown', 'Pull the handle down towards your side, leading with the elbow. Pause at the bottom and let the lat stretch at the top.'],
            ['barbell-row', 'Barbell row', 'Hinge forward with a flat back, bar hanging under your shoulders. Row it to your lower ribs and lower it with control.'],
            ['seated-machine-row', 'Seated machine row', 'Chest against the pad. Pull the handles back by squeezing your shoulder blades together, then extend your arms fully.'],
            ['seated-cable-row-v-bar', 'Seated cable row (V-bar)', 'Sit tall with a slight bend in the knees. Pull the handle to your stomach, squeeze your shoulder blades and return without rounding your back.'],
            ['single-arm-seated-cable-row', 'Single-arm seated cable row', 'Sit tall and row the handle to your hip while keeping your torso still. Reach forward for a full stretch on every rep.'],
            ['cable-straight-arm-pulldown', 'Cable pullover', 'Arms almost straight, slight hinge at the hips. Sweep the handle down to your thighs using your lats, then let it return slowly.'],
            ['back-extensions', 'Back extensions', 'Hips on the pad. Lower your torso with a flat back, then rise until you are in line with your legs. Don\'t over-arch at the top.'],
        ],
        'Shoulders' => [
            ['seated-dumbbell-shoulder-press', 'Seated dumbbell shoulder press', 'Back against the bench, dumbbells at ear height. Press overhead and lower under control.'],
            ['barbell-overhead-press', 'Barbell overhead press', 'Bar on the front of your shoulders, glutes and core tight. Press straight up, moving your head back slightly to let the bar pass.'],
            ['dumbbell-lateral-raise', 'Dumbbell lateral raise', 'Slight bend in the elbows. Raise the dumbbells out to the sides up to shoulder height, leading with your elbows, and lower slowly.'],
            ['cable-face-pull', 'Cable face pull', 'Cable at face height. Pull towards your face with elbows high and wide, pulling the handles apart at the end.'],
            ['half-kneeling-curl-and-press', 'Half-kneeling curl & press', 'Kneel on one knee with your core braced. Curl the dumbbell to your shoulder, press it overhead, then reverse the movement with control.'],
        ],
        'Biceps' => [
            ['barbell-curl', 'Barbell curl', 'Elbows pinned to your sides. Curl the bar up without swinging and lower it slowly all the way down.'],
            ['wall-barbell-curl', 'Wall barbell curl', 'Back and elbows against the wall so you can\'t swing. Curl the bar up, squeeze and lower slowly. Strict reps only.'],
            ['dumbbell-curl', 'Dumbbell curl', 'Palms facing forward, elbows at your sides. Curl up, squeeze your biceps and lower under control.'],
            ['hammer-curl', 'Hammer curl', 'Palms facing each other, as if holding a hammer. Curl up without moving your elbows. Works the biceps and forearms.'],
            ['single-arm-cable-curl', 'Single-arm cable curl', 'Elbow by your side. Curl the handle up to your shoulder and resist the cable on the way down.'],
        ],
        'Triceps' => [
            ['close-grip-bench-press', 'Close-grip bench press', 'Hands about shoulder-width apart. Lower the bar to your lower chest with elbows close to your body, then press up.'],
            ['dumbbell-skull-crushers', 'Dumbbell skull crushers', 'Arms pointing up, elbows fixed. Lower the dumbbells beside your head by bending only at the elbows, then extend.'],
            ['dumbbell-overhead-triceps-extension', 'Dumbbell overhead triceps extension', 'Hold one dumbbell overhead with both hands. Lower it behind your head with elbows pointing up, then extend fully.'],
            ['cable-overhead-triceps-extension', 'Cable overhead triceps extension', 'Face away from the cable with elbows by your ears. Extend your arms forward and overhead, then let the weight stretch your triceps.'],
            ['rope-triceps-pushdown', 'Rope triceps pushdown', 'Elbows tucked at your sides. Push the rope down and spread it apart at the bottom, then let it back up without moving your elbows.'],
            ['single-arm-cable-pushdown', 'Single-arm cable pushdown', 'Elbow pinned to your side. Push the handle down to full extension and control it on the way up.'],
            ['bench-dips', 'Bench dips', 'Hands on the edge of the bench, legs out in front. Lower until your elbows are at about 90°, then push back up, keeping your hips close to the bench.'],
        ],
        'Legs' => [
            ['barbell-back-squat', 'Barbell back squat', 'Bar on your upper back, feet about shoulder-width. Brace, squat to at least parallel and drive up through the whole foot.'],
            ['goblet-squat', 'Goblet squat', 'Hold the dumbbell at your chest. Chest up, squat deep with elbows inside your knees and stand tall.'],
            ['leg-press', 'Leg press', 'Feet shoulder-width on the platform. Lower until your knees reach about 90° without your lower back lifting, and don\'t lock your knees at the top.'],
            ['leg-extension', 'Leg extension', 'Pad on your lower shins, knees lined up with the machine\'s pivot. Extend fully, squeeze your quads and lower slowly.'],
            ['dumbbell-lunges', 'Dumbbell lunges', 'Take a long step and lower until your back knee almost touches the floor. Keep your torso upright and push through the front heel.'],
            ['romanian-deadlift', 'Romanian deadlift', 'Soft knees. Push your hips back and slide the bar down your thighs with a flat back until you feel your hamstrings stretch, then drive your hips forward.'],
            ['wall-sit', 'Wall sit', 'Back flat against the wall, thighs parallel to the floor, knees over ankles. Hold for time and keep breathing.'],
            ['seated-calf-raise', 'Seated calf raise', 'Pad on your lower thighs, balls of your feet on the platform. Lower your heels for a full stretch, then rise as high as you can.'],
            ['standing-calf-raise', 'Standing calf raise', 'Balls of your feet on the edge, legs straight. Drop your heels low, push up onto your toes and pause at the top.'],
            ['dumbbell-glute-bridge', 'Dumbbell glute bridge', 'Dumbbell on your hips, feet flat and close to your glutes. Drive through your heels, squeeze your glutes at the top and lower slowly.'],
            ['glute-bridge-march', 'Glute bridge march', 'Lift into a bridge and hold it. Lift one knee at a time without letting your hips drop or twist.'],
        ],
        'Abs' => [
            ['crunches', 'Crunches', 'Hands lightly behind your head, knees bent. Curl your shoulders off the floor with your abs, not your neck, and lower slowly.'],
            ['kneeling-cable-crunch', 'Kneeling cable crunch', 'Kneel facing the cable with the rope by your head. Crunch down by rounding your spine while keeping your hips still.'],
            ['lying-leg-raises', 'Lying leg raises', 'Lie flat with your hands by your sides. Raise your straight legs to vertical and lower them slowly without arching your lower back.'],
            ['hanging-leg-raises', 'Hanging leg raises', 'Hang from the bar without swinging. Raise your straight legs as high as you can and lower them under control.'],
            ['hanging-knee-raises', 'Hanging knee raises', 'Hang from the bar and pull your knees up towards your chest. Lower slowly, with no swinging.'],
            ['tucked-leg-raises', 'Tucked leg raises', 'Lie on your back with knees bent. Bring your knees towards your chest, lifting your hips slightly off the floor, then lower slowly.'],
            ['russian-twist', 'Russian twist', 'Sit with knees bent, lean back slightly and hold a plate. Rotate side to side, turning your shoulders, not just your arms.'],
            ['plank-hip-twists', 'Plank hip twists', 'Forearm plank. Rotate your hips from side to side towards the floor, keeping your core tight and shoulders over your elbows.'],
            ['mountain-climbers', 'Mountain climbers', 'High plank with hands under your shoulders. Drive your knees towards your chest one at a time, keeping your hips low.'],
            ['plank-shoulder-taps', 'Plank shoulder taps', 'High plank, feet slightly wider than your hips. Tap the opposite shoulder with each hand while keeping your hips still.'],
            ['sit-up-knee-tuck', 'Sit-up with alternating knee tuck', 'Lie flat with your arms overhead. Sit up while driving one knee towards your chest, lower back down and switch sides.'],
        ],
    ];

    public function run(): void
    {
        $added = 0;

        foreach (self::LIBRARY as $category => $exercises) {
            foreach ($exercises as [$slug, $title, $tip]) {
                $video = ExerciseVideo::firstOrCreate(
                    ['url' => "/videos/exercises/{$slug}.mp4"],
                    ['title' => $title, 'category' => $category, 'description' => $tip],
                );
                $added += $video->wasRecentlyCreated ? 1 : 0;
            }
        }

        $this->command?->info("Exercise library: {$added} videos added.");
    }
}
