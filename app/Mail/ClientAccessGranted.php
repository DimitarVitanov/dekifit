<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class ClientAccessGranted extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Opening the dashboard from this email also confirms the address, so a
     * new client never needs a separate confirmation email.
     */
    public ?string $verifyUrl = null;

    /**
     * $password is only set when a new account was created for this order.
     */
    public function __construct(public User $user, public Order $order, public ?string $password = null)
    {
        if (! $user->hasVerifiedEmail()) {
            $this->verifyUrl = URL::temporarySignedRoute('verification.verify', now()->addDays(7), [
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ]);
        }
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your DekiFit client dashboard is ready',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.client-access',
            with: [
                'firstName' => Str::before(trim($this->user->name), ' '),
                'dashboardUrl' => $this->verifyUrl ?? route('login'),
            ],
        );
    }
}
