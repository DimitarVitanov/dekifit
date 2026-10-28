<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

/**
 * Automatic reply to whoever used the contact form. It deliberately doesn't
 * echo their message back, so the form can't be used to send spam to others.
 */
class ContactReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Contact $contact)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thanks for getting in touch with DekiFit',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact-received',
            with: [
                'firstName' => Str::limit(Str::before(trim($this->contact->name), ' '), 40, ''),
            ],
        );
    }
}
