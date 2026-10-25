<?php

namespace Tests\Feature;

use App\Mail\ClientAccessGranted;
use App\Models\ExerciseVideo;
use App\Models\NutritionPlan;
use App\Models\Order;
use App\Models\Program;
use App\Models\Ticket;
use App\Models\TimeSlot;
use App\Models\TrainingPlan;
use App\Models\User;
use Database\Seeders\DeusProgramSeeder;
use Database\Seeders\ExerciseLibrarySeeder;
use Database\Seeders\MainGainNutritionSeeder;
use Database\Seeders\ProgramSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CoachingPlatformTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        return $admin;
    }

    private function order(array $attributes = []): Order
    {
        $program = Program::create(['title' => 'Pro', 'slug' => 'pro', 'price' => 80, 'is_published' => true]);

        return Order::create($attributes + [
            'program_id' => $program->id,
            'package_name' => 'Pro',
            'price' => 80,
            'name' => 'Ana Client',
            'email' => 'ana@example.com',
            'phone' => '+38970000000',
        ]);
    }

    public function test_guests_are_sent_to_login(): void
    {
        $this->get('/portal')->assertRedirect('/login');
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_clients_cannot_reach_admin_and_coach_is_redirected_from_portal(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
        $this->actingAs($this->admin())->get('/portal')->assertRedirect('/admin');
    }

    public function test_granting_access_creates_the_client_emails_credentials_and_lets_them_log_in(): void
    {
        Mail::fake();
        $order = $this->order();

        $response = $this->actingAs($this->admin())->post("/admin/orders/{$order->id}/grant");

        $credentials = $response->getSession()->get('credentials');
        $client = User::where('email', 'ana@example.com')->firstOrFail();

        $this->assertFalse($client->is_admin);
        $this->assertTrue($client->must_change_password);
        $this->assertSame($order->program_id, $client->program_id);
        $this->assertTrue(Hash::check($credentials['password'], $client->password));
        $this->assertSame('paid', $order->fresh()->status);
        $this->assertSame($client->id, $order->fresh()->user_id);
        Mail::assertSent(ClientAccessGranted::class, fn ($mail) => $mail->hasTo('ana@example.com') && $mail->password === $credentials['password']);

        // Granting twice must not create a second account or reset anything.
        $this->post("/admin/orders/{$order->id}/grant")->assertSessionHas('error');
        $this->assertSame(1, User::where('email', 'ana@example.com')->count());

        $this->post('/logout');
        $this->post('/login', ['email' => 'ana@example.com', 'password' => $credentials['password']])->assertRedirect('/portal');
        $this->get('/portal')->assertOk();
    }

    public function test_client_cannot_grant_access(): void
    {
        $order = $this->order();

        $this->actingAs(User::factory()->create())->post("/admin/orders/{$order->id}/grant")->assertForbidden();
        $this->assertSame('pending', $order->fresh()->status);
    }

    public function test_checkout_uses_the_real_package_price(): void
    {
        Mail::fake();
        $program = Program::create(['title' => 'Elite', 'slug' => 'elite', 'price' => 120, 'is_published' => true]);

        $this->post('/checkout', [
            'program_id' => $program->id, 'package_name' => 'Elite', 'price' => 1,
            'name' => 'Ana', 'email' => 'ana@example.com', 'phone' => '123',
        ])->assertOk();

        $this->assertEquals(120, Order::first()->price);
    }

    public function test_progress_is_one_entry_per_day_and_private(): void
    {
        $client = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($client)->post('/portal/progress', ['logged_on' => '2026-09-01', 'weight_kg' => 82.4]);
        $this->actingAs($client)->post('/portal/progress', ['logged_on' => '2026-09-01', 'weight_kg' => 82.0]);

        $this->assertSame(1, $client->progressEntries()->count());
        $entry = $client->progressEntries()->first();
        $this->assertSame(82.0, $entry->weight_kg);

        $this->actingAs($other)->delete("/portal/progress/{$entry->id}")->assertForbidden();
        $this->actingAs($client)->get('/portal/progress')->assertOk();
    }

    public function test_notes_are_private(): void
    {
        $client = User::factory()->create();
        $this->actingAs($client)->post('/portal/notes', ['title' => 'Squat cues', 'body' => 'Brace']);
        $note = $client->notes()->firstOrFail();

        $other = User::factory()->create();
        $this->actingAs($other)->put("/portal/notes/{$note->id}", ['title' => 'x'])->assertForbidden();
        $this->actingAs($other)->delete("/portal/notes/{$note->id}")->assertForbidden();
        $this->actingAs($client)->get('/portal/notes')->assertOk();
    }

    public function test_ticket_conversation_between_client_and_coach(): void
    {
        $client = User::factory()->create();
        $this->actingAs($client)->post('/portal/support', ['subject' => 'Bench', 'body' => 'Shoulder hurts']);
        $ticket = Ticket::firstOrFail();
        $this->assertSame(Ticket::OPEN, $ticket->status);

        $this->actingAs(User::factory()->create())->get("/portal/support/{$ticket->id}")->assertForbidden();

        $this->actingAs($this->admin())->post("/admin/tickets/{$ticket->id}", ['body' => 'Lower the weight']);
        $this->assertSame(Ticket::ANSWERED, $ticket->fresh()->status);

        $this->actingAs($client)->post("/portal/support/{$ticket->id}", ['body' => 'Thanks']);
        $this->assertSame(Ticket::OPEN, $ticket->fresh()->status);
        $this->assertSame(3, $ticket->messages()->count());
        $this->actingAs($client)->get("/portal/support/{$ticket->id}")->assertOk();
    }

    public function test_a_slot_can_only_be_booked_once_and_cancelled_by_its_owner(): void
    {
        $admin = $this->admin();
        $start = now()->addDays(2)->startOfHour();

        $this->actingAs($admin)->post('/admin/slots', ['slots' => [
            ['starts_at' => $start->toIso8601String(), 'ends_at' => $start->copy()->addMinutes(30)->toIso8601String()],
            ['starts_at' => $start->copy()->addMinutes(15)->toIso8601String(), 'ends_at' => $start->copy()->addMinutes(45)->toIso8601String()], // overlaps
        ]]);
        $this->assertSame(1, TimeSlot::count());
        $slot = TimeSlot::first();

        $first = User::factory()->create();
        $second = User::factory()->create();

        $this->actingAs($first)->post("/portal/schedule/{$slot->id}/book")->assertSessionHas('success');
        $this->actingAs($second)->post("/portal/schedule/{$slot->id}/book")->assertSessionHas('error');
        $this->assertSame($first->id, $slot->fresh()->user_id);

        $this->actingAs($second)->post("/portal/schedule/{$slot->id}/cancel")->assertForbidden();
        $this->actingAs($first)->post("/portal/schedule/{$slot->id}/cancel");
        $this->assertNull($slot->fresh()->user_id);
        $this->actingAs($first)->get('/portal/schedule')->assertOk();
    }

    public function test_training_programs_reach_only_their_audience(): void
    {
        $admin = $this->admin();
        $pro = Program::create(['title' => 'Pro', 'slug' => 'pro', 'price' => 80, 'is_published' => true]);
        $ana = User::factory()->create(['program_id' => $pro->id]);
        $bob = User::factory()->create();
        $days = [['name' => 'Day 1', 'exercises' => [['name' => 'Squat', 'sets' => '4', 'reps' => '6']]]];

        $this->actingAs($ana)->post('/admin/programs', ['title' => 'Nope', 'days' => $days])->assertForbidden();

        $this->actingAs($admin)->post('/admin/programs', ['title' => 'For Ana', 'user_id' => $ana->id, 'days' => $days])->assertSessionHasNoErrors();
        $this->actingAs($admin)->post('/admin/programs', ['title' => 'For Pro', 'program_id' => $pro->id, 'days' => $days])->assertSessionHasNoErrors();
        $this->actingAs($admin)->post('/admin/programs', ['title' => 'For all', 'days' => $days])->assertSessionHasNoErrors();
        $this->actingAs($admin)->post('/admin/programs', ['title' => 'Broken', 'days' => [['name' => '', 'exercises' => []]]])->assertSessionHasErrors('days.0.name');

        $this->assertSame(['For Ana', 'For Pro', 'For all'], TrainingPlan::visibleTo($ana)->pluck('title')->sort()->values()->all());
        $this->assertSame(['For all'], TrainingPlan::visibleTo($bob)->pluck('title')->all());
        $this->assertSame('For Ana', TrainingPlan::visibleTo($ana)->first()->title); // personal plan comes first

        $this->actingAs($ana)->get('/portal/program')->assertOk();
        $this->actingAs($bob)->get('/portal/program')->assertOk();
    }

    public function test_nutrition_plans_reach_only_their_audience(): void
    {
        $admin = $this->admin();
        $ana = User::factory()->create();
        $bob = User::factory()->create();
        $meals = [['name' => 'Breakfast', 'time' => '08:00', 'items' => [['food' => 'Oats', 'amount' => '80 g']]]];

        $this->actingAs($ana)->post('/admin/nutrition', ['title' => 'Nope', 'meals' => $meals])->assertForbidden();
        $this->actingAs($admin)->post('/admin/nutrition', ['title' => 'Ana cut', 'user_id' => $ana->id, 'calories' => 2000, 'protein_g' => 160, 'meals' => $meals])->assertSessionHasNoErrors();
        $this->actingAs($admin)->post('/admin/nutrition', ['title' => 'Broken', 'meals' => [['name' => 'Lunch', 'items' => [['food' => '']]]]])->assertSessionHasErrors('meals.0.items.0.food');

        $this->assertSame(['Ana cut'], NutritionPlan::visibleTo($ana)->pluck('title')->all());
        $this->assertSame([], NutritionPlan::visibleTo($bob)->pluck('title')->all());

        $this->actingAs($ana)->get('/portal/nutrition')->assertOk();
        $this->actingAs($bob)->get('/portal/nutrition')->assertOk();
        $this->actingAs($ana)->get('/portal')->assertOk();
    }

    public function test_coach_manages_the_video_library(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/videos', ['title' => 'Squat', 'category' => 'Legs', 'url' => 'javascript:alert(1)'])->assertSessionHasErrors('url');
        $this->actingAs($admin)->post('/admin/videos', ['title' => 'Squat', 'category' => 'Legs', 'url' => '//evil.example/x.mp4'])->assertSessionHasErrors('url');
        $this->actingAs($admin)->post('/admin/videos', ['title' => 'Squat', 'category' => 'Legs', 'url' => '/\\evil.example/x.mp4'])->assertSessionHasErrors('url');
        $this->actingAs($admin)->post('/admin/videos', ['title' => 'Squat', 'category' => 'Legs', 'url' => 'https://youtu.be/abcdefghijk'])->assertSessionHasNoErrors();
        $this->actingAs($admin)->post('/admin/videos', ['title' => 'Lunges', 'category' => 'Legs', 'url' => '/videos/exercises/dumbbell-lunges.mp4'])->assertSessionHasNoErrors();
        $this->assertSame(2, ExerciseVideo::count());

        $client = User::factory()->create();
        $this->actingAs($client)->post('/admin/videos', ['title' => 'x', 'category' => 'y', 'url' => 'https://a.b/c.mp4'])->assertForbidden();
        $this->actingAs($client)->get('/portal/videos')->assertOk();
    }

    public function test_exercise_library_seeder_adds_every_clip_once(): void
    {
        $this->seed(ExerciseLibrarySeeder::class);

        $videos = ExerciseVideo::all();
        $this->assertCount(collect(ExerciseLibrarySeeder::LIBRARY)->flatten(1)->count(), $videos);
        foreach ($videos as $video) {
            $this->assertFileExists(ExerciseVideo::hostedPath(basename($video->url)));
            $this->assertFileExists(public_path($video->poster));
            $this->assertFileDoesNotExist(public_path($video->url)); // must not bypass the lock
        }

        // Re-running neither duplicates entries nor overwrites the coach's edits.
        $videos->first()->update(['description' => 'Coach tip']);
        $this->seed(ExerciseLibrarySeeder::class);
        $this->assertSame($videos->count(), ExerciseVideo::count());
        $this->assertSame('Coach tip', $videos->first()->fresh()->description);
    }

    public function test_coach_unlocks_video_library_body_parts_per_client(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('exercise-videos/squat.mp4', 'clip');
        Storage::disk('local')->put('exercise-videos/row.mp4', 'clip');
        Storage::disk('local')->put('exercise-videos/curl.mp4', 'clip');

        $squat = ExerciseVideo::create(['title' => 'Squat', 'category' => 'Legs', 'url' => '/videos/exercises/squat.mp4']);
        $row = ExerciseVideo::create(['title' => 'Row', 'category' => 'Back', 'url' => '/videos/exercises/row.mp4']);
        $curl = ExerciseVideo::create(['title' => 'Curl', 'category' => 'Biceps', 'url' => '/videos/exercises/curl.mp4']);
        $admin = $this->admin();
        $client = User::factory()->create();

        // Everything starts locked: no links are sent and the files can't be streamed.
        $this->actingAs($client)->get('/portal/videos')->assertInertia(fn ($page) => $page
            ->where('videos.0.locked', true)->missing('videos.0.url')->where('videos.0.poster', '/videos/exercises/row.jpg'));
        $this->actingAs($client)->get('/videos/exercises/squat.mp4')->assertForbidden();
        $this->post('/logout');
        $this->get('/videos/exercises/squat.mp4')->assertRedirect('/login');

        // Only the coach can unlock, and only existing body parts.
        $this->actingAs($client)->put("/admin/clients/{$client->id}/library", ['categories' => ['Legs']])->assertForbidden();
        $this->actingAs($admin)->put("/admin/clients/{$client->id}/library", ['categories' => ['Nope']])->assertSessionHasErrors('categories.0');
        $this->actingAs($admin)->put("/admin/clients/{$client->id}/library", ['categories' => ['Legs']])->assertSessionHasNoErrors();
        $this->actingAs($admin)->get('/videos/exercises/row.mp4')->assertOk();

        $client->refresh();
        $this->actingAs($client)->get('/videos/exercises/squat.mp4')->assertOk();
        $this->actingAs($client)->get('/videos/exercises/row.mp4')->assertForbidden();

        // A video in the client's training program plays even though Biceps is locked.
        TrainingPlan::create(['title' => 'Arms', 'user_id' => $client->id, 'days' => [['name' => 'Day 1', 'exercises' => [['name' => 'Curl', 'video_id' => $curl->id]]]]]);
        $this->actingAs($client)->get('/videos/exercises/curl.mp4')->assertOk();
        $this->actingAs($client)->get('/portal/videos')->assertInertia(fn ($page) => $page
            ->where('videos', fn ($videos) => collect($videos)->where('locked', false)->pluck('title')->sort()->values()->all() === ['Curl', 'Squat']));

        // Locking again takes access away.
        $this->actingAs($admin)->put("/admin/clients/{$client->id}/library", ['categories' => []])->assertSessionHasNoErrors();
        $client->refresh();
        $this->actingAs($client)->get('/videos/exercises/squat.mp4')->assertForbidden();
        $this->actingAs($client)->get('/portal')->assertInertia(fn ($page) => $page->where('videoCount', 3)->where('unlockedVideoCount', 1));
    }

    public function test_default_deus_program_and_nutrition_plan_reach_every_client(): void
    {
        $this->seed([DeusProgramSeeder::class, MainGainNutritionSeeder::class]);
        $this->seed([DeusProgramSeeder::class, MainGainNutritionSeeder::class]); // re-running adds nothing

        $plan = TrainingPlan::sole();
        $nutrition = NutritionPlan::sole();
        $client = User::factory()->create();

        $this->assertSame([$plan->id], TrainingPlan::visibleTo($client)->pluck('id')->all());
        $this->assertSame([$nutrition->id], NutritionPlan::visibleTo($client)->pluck('id')->all());
        $this->assertSame(range(1, 12), collect($plan->days)->pluck('week')->unique()->values()->all());

        // Exercises are linked to real library videos, which the client can then stream.
        $videoIds = collect($plan->days)->pluck('exercises')->flatten(1)->pluck('video_id')->filter()->unique();
        $this->assertGreaterThan(40, $videoIds->count());
        $this->assertSame($videoIds->count(), ExerciseVideo::whereIn('id', $videoIds)->count());
        $this->assertTrue($client->canWatch(ExerciseVideo::find($videoIds->first())));

        // The coach can open and save both plans unchanged in the admin.
        $admin = $this->admin();
        $this->actingAs($admin)->put("/admin/programs/{$plan->id}", ['title' => $plan->title, 'description' => $plan->description, 'days' => $plan->days])
            ->assertSessionHasNoErrors();
        $this->assertSame(collect($plan->days)->pluck('week')->all(), collect($plan->fresh()->days)->pluck('week')->all());
        $this->actingAs($admin)->put("/admin/nutrition/{$nutrition->id}", ['title' => $nutrition->title, 'description' => $nutrition->description, 'meals' => $nutrition->meals])
            ->assertSessionHasNoErrors();

        $this->actingAs($client)->get('/portal/program')->assertOk();
        $this->actingAs($client)->get('/portal/nutrition')->assertOk();
        $this->actingAs($client)->get('/portal')->assertOk();
    }

    public function test_public_nutrition_page_only_teases_the_plan(): void
    {
        $this->seed([ProgramSeeder::class, MainGainNutritionSeeder::class]);

        // Visitors get the packages to subscribe to, never the plan itself.
        $this->get('/nutrition')->assertOk()->assertInertia(fn ($page) => $page
            ->component('Nutrition')
            ->has('programs', 3)
            ->where('programs.0.title', 'Basic')
            ->missing('plans')
            ->missing('meals'));
        $this->assertStringNotContainsString('Rio Mare', $this->get('/nutrition')->getContent());
    }

    public function test_start_package_is_renamed_to_basic(): void
    {
        Program::create(['title' => 'Start', 'slug' => 'start', 'price' => 40, 'is_published' => true]);
        Program::create(['title' => 'Pro', 'slug' => 'pro', 'price' => 80, 'is_published' => true, 'content' => 'Everything in Start plus calls.', 'features' => ['Everything in Start, plus:', 'Calls']]);

        (require database_path('migrations/2026_10_18_130000_rename_start_package_to_basic.php'))->up();

        $this->assertSame('Basic', Program::where('slug', 'start')->value('title'));
        $this->assertSame(['Everything in Basic, plus:', 'Calls'], Program::where('slug', 'pro')->first()->features);
    }

    public function test_admin_pages_render(): void
    {
        $admin = $this->admin();
        $client = User::factory()->create();

        foreach (['/admin', '/admin/clients', "/admin/clients/{$client->id}", '/admin/news', '/admin/tickets', '/admin/slots', '/admin/programs', '/admin/videos', '/admin/nutrition'] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    public function test_client_must_know_current_password_to_change_it(): void
    {
        $client = User::factory()->create(['password' => 'old-password', 'must_change_password' => true]);

        $this->actingAs($client)->put('/portal/account/password', [
            'current_password' => 'wrong', 'password' => 'new-password-1', 'password_confirmation' => 'new-password-1',
        ])->assertSessionHasErrors('current_password');

        $this->actingAs($client)->put('/portal/account/password', [
            'current_password' => 'old-password', 'password' => 'new-password-1', 'password_confirmation' => 'new-password-1',
        ])->assertSessionHasNoErrors();

        $this->assertFalse($client->fresh()->must_change_password);
        $this->assertTrue(Hash::check('new-password-1', $client->fresh()->password));
    }
}
