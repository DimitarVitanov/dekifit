<?php

namespace Tests\Feature;

use App\Mail\ClientAccessGranted;
use App\Mail\ContactReceived;
use App\Mail\NewContactMessage;
use App\Mail\NewOrderNotification;
use App\Mail\OrderReceived;
use App\Models\Contact;
use App\Models\Order;
use App\Models\Program;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_subscribing_emails_the_customer_and_the_coach(): void
    {
        Mail::fake();
        $program = Program::create(['title' => 'Pro', 'slug' => 'pro', 'price' => 100, 'is_published' => true]);

        $this->post('/checkout', [
            'program_id' => $program->id,
            'package_name' => 'Pro',
            'price' => 1,
            'name' => 'Ana Stojanova',
            'email' => 'ana@example.com',
            'phone' => '+38970000000',
        ])->assertOk();

        Mail::assertSent(NewOrderNotification::class, fn ($mail) => $mail->hasTo(config('dekifit.admin_email')) && $mail->hasReplyTo('ana@example.com'));
        Mail::assertSent(OrderReceived::class, fn ($mail) => $mail->hasTo('ana@example.com'));

        $html = (new OrderReceived(Order::first()))->render();
        $this->assertStringContainsString('Thank you, Ana!', $html);
        $this->assertStringContainsString('€100', $html);
        $this->assertStringContainsString('MK07 2401 1737 2707 105', $html);
        $this->assertStringContainsString('DekiFit Pro – Ana Stojanova', $html);
    }

    public function test_contact_form_auto_replies_and_notifies_the_coach(): void
    {
        Mail::fake();

        $this->post('/contact', [
            'name' => 'Marko Petrov',
            'email' => 'marko@example.com',
            'subject' => 'packages',
            'message' => 'Which package for a beginner?',
        ])->assertSessionHas('success');

        Mail::assertSent(ContactReceived::class, fn ($mail) => $mail->hasTo('marko@example.com'));
        Mail::assertSent(NewContactMessage::class, fn ($mail) => $mail->hasTo(config('dekifit.admin_email')) && $mail->hasReplyTo('marko@example.com'));
        $this->assertStringContainsString('Packages &amp; Pricing', (new NewContactMessage(Contact::first()))->render());
    }

    public function test_contact_form_ignores_bots_that_fill_the_hidden_field(): void
    {
        Mail::fake();

        $this->post('/contact', [
            'name' => 'Bot',
            'email' => 'victim@example.com',
            'message' => 'Buy now',
            'contact_website' => 'http://spam.test',
        ])->assertSessionHas('success');

        Mail::assertNothingSent();
        $this->assertSame(0, Contact::count());
    }

    public function test_form_input_cannot_inject_links_into_emails(): void
    {
        $contact = Contact::create(['name' => 'Eve', 'email' => 'eve@example.com', 'message' => '[Claim your prize](http://evil.test)']);

        $this->assertStringNotContainsString('href="http://evil.test"', (new NewContactMessage($contact))->render());
    }

    public function test_forgot_password_sends_the_branded_reset_email(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'ana@example.com']);

        $this->get('/forgot-password')->assertOk();
        $this->post('/forgot-password', ['email' => 'ana@example.com'])->assertSessionHas('success');
        // Unknown emails get the same answer and no email.
        $this->post('/forgot-password', ['email' => 'nobody@example.com'])->assertSessionHas('success');

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $html = (string) $notification->toMail($user)->render();

            return str_contains($html, 'Reset your password')
                && str_contains($html, route('password.reset', ['token' => $notification->token, 'email' => $user->email]));
        });
        Notification::assertSentTimes(ResetPassword::class, 1);
    }

    public function test_reset_link_sets_a_new_password_and_confirms_the_email(): void
    {
        Notification::fake();
        $user = User::factory()->unverified()->create(['email' => 'ana@example.com', 'must_change_password' => true]);
        $this->post('/forgot-password', ['email' => 'ana@example.com']);

        $token = null;
        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use (&$token) {
            $token = $notification->token;

            return true;
        });

        $this->get("/reset-password/{$token}?email=ana@example.com")->assertOk();
        $this->post('/reset-password', [
            'token' => 'wrong',
            'email' => 'ana@example.com',
            'password' => 'brand-new-pass',
            'password_confirmation' => 'brand-new-pass',
        ])->assertSessionHasErrors('email');

        $this->post('/reset-password', [
            'token' => $token,
            'email' => 'ana@example.com',
            'password' => 'brand-new-pass',
            'password_confirmation' => 'brand-new-pass',
        ])->assertRedirect('/login');

        $user->refresh();
        $this->assertTrue(Hash::check('brand-new-pass', $user->password));
        $this->assertFalse($user->must_change_password);
        $this->assertTrue($user->hasVerifiedEmail());
    }

    public function test_welcome_email_link_confirms_the_email(): void
    {
        Mail::fake();
        $program = Program::create(['title' => 'Pro', 'slug' => 'pro', 'price' => 100, 'is_published' => true]);
        $order = Order::create(['program_id' => $program->id, 'package_name' => 'Pro', 'price' => 100, 'name' => 'Ana Client', 'email' => 'ana@example.com', 'phone' => '1']);
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        $this->actingAs($admin)->post("/admin/orders/{$order->id}/grant");
        $this->post('/logout');

        $mail = null;
        Mail::assertSent(ClientAccessGranted::class, function ($sent) use (&$mail) {
            $mail = $sent;

            return true;
        });
        $client = User::where('email', 'ana@example.com')->first();
        $this->assertFalse($client->hasVerifiedEmail());
        $this->assertNotNull($mail->verifyUrl);
        $this->assertStringContainsString(e($mail->verifyUrl), $mail->render());

        $this->get($mail->verifyUrl)->assertRedirect('/login')->assertSessionHas('success');
        $this->assertTrue($client->fresh()->hasVerifiedEmail());
    }

    public function test_expired_or_tampered_confirmation_links_are_rejected(): void
    {
        $user = User::factory()->unverified()->create();
        $url = URL::temporarySignedRoute('verification.verify', now()->subMinute(), ['id' => $user->id, 'hash' => sha1($user->email)]);

        $this->get($url)->assertRedirect('/login')->assertSessionHas('error');
        $this->get("/email/verify/{$user->id}/".sha1($user->email))->assertRedirect('/login')->assertSessionHas('error');
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_unconfirmed_clients_can_resend_the_branded_confirmation_email(): void
    {
        Notification::fake();
        $client = User::factory()->unverified()->create();

        $this->actingAs($client)->get('/portal')->assertInertia(fn ($page) => $page->where('auth.user.email_verified', false));
        $this->actingAs($client)->post('/email/verification-notification')->assertSessionHas('success');

        Notification::assertSentTo($client, VerifyEmail::class, function ($notification) use ($client) {
            return str_contains((string) $notification->toMail($client)->render(), 'Confirm your email');
        });
    }
}
