<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Tells the coach about a contact-form message. Replying to it answers the sender.
 */
class NewContactMessage extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Contact $contact)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [new Address($this->contact->email, $this->contact->name)],
            subject: "New message from {$this->contact->name}: {$this->contact->subject_label}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.new-contact',
        );
    }
}
