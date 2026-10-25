<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClientAccessGranted extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * $password is only set when a new account was created for this order.
     */
    public function __construct(public User $user, public Order $order, public ?string $password = null)
    {
        //
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
        );
    }
}
