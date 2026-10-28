<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Mail\Markdown;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Names and messages typed into the site's forms end up in emails; this stops
        // them from being read as Markdown links or HTML.
        Markdown::withSecuredEncoding();

        ResetPassword::toMailUsing(fn (User $user, string $token) => (new MailMessage)
            ->subject('Reset your DekiFit password')
            ->markdown('emails.auth.reset-password', [
                'user' => $user,
                'firstName' => Str::before(trim($user->name), ' '),
                'url' => route('password.reset', ['token' => $token, 'email' => $user->getEmailForPasswordReset()]),
                'expires' => config('auth.passwords.users.expire'),
            ]));

        VerifyEmail::toMailUsing(fn (User $user, string $url) => (new MailMessage)
            ->subject('Confirm your email for DekiFit')
            ->markdown('emails.auth.verify-email', [
                'user' => $user,
                'firstName' => Str::before(trim($user->name), ' '),
                'url' => $url,
                'expires' => config('auth.verification.expire', 60),
            ]));
    }
}
