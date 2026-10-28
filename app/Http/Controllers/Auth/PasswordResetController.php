<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class PasswordResetController extends Controller
{
    public function create()
    {
        return Inertia::render('Auth/ForgotPassword');
    }

    public function store(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        try {
            Password::sendResetLink($request->only('email'));
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', "We couldn't send the email just now. Please try again in a few minutes, or message your coach.");
        }

        // The same answer whether or not the account exists, so the form can't be used to find out who's a client.
        return back()->with('success', 'If there is a DekiFit account for that email, a reset link is on its way. Check your inbox (and the spam folder).');
    }

    public function edit(Request $request, string $token)
    {
        return Inertia::render('Auth/ResetPassword', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                    'must_change_password' => false,
                ])->save();

                // They opened the link we emailed, so the address is confirmed too.
                if (! $user->hasVerifiedEmail()) {
                    $user->markEmailAsVerified();
                }

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => $status === Password::INVALID_TOKEN
                    ? 'This reset link is invalid or has expired. Please request a new one.'
                    : __($status),
            ]);
        }

        return redirect()->route('login')->with('success', 'Your password has been reset. Log in with your new password.');
    }
}
