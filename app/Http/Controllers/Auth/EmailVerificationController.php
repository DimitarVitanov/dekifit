<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    /**
     * The signed link from the welcome or confirmation email. The signature is the
     * proof, so it works without being logged in, straight from the inbox.
     */
    public function verify(Request $request, string $id, string $hash)
    {
        $user = User::find($id);

        if (! $user || ! $request->hasValidSignature() || ! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return $request->user()
                ? redirect($this->home($request->user()))->with('error', 'That confirmation link is invalid or has expired. Use "Resend link" to get a new one.')
                : redirect()->route('login')->with('error', 'That confirmation link has expired. Log in and use "Resend link" on your dashboard to get a new one.');
        }

        if (! $user->hasVerifiedEmail() && $user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return $request->user()
            ? redirect($this->home($request->user()))->with('success', 'Thanks, your email address is confirmed.')
            : redirect()->route('login')->with('success', 'Your email address is confirmed. Log in to open your dashboard.');
    }

    public function send(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return back()->with('success', 'Your email address is already confirmed.');
        }

        try {
            $user->sendEmailVerificationNotification();
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', "We couldn't send the email just now. Please try again in a few minutes.");
        }

        return back()->with('success', "We've sent a new confirmation link to {$user->email}.");
    }

    private function home(User $user): string
    {
        return $user->is_admin ? route('admin.orders') : route('portal.dashboard');
    }
}
