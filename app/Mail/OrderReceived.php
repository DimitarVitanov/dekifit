<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

/**
 * Sent to the customer as soon as they subscribe: the order summary, the bank
 * transfer details and what happens after they pay.
 */
class OrderReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your DekiFit {$this->order->package_name} package: payment details",
        );
    }

    public function content(): Content
    {
        $bank = config('dekifit.bank');

        return new Content(
            markdown: 'emails.order-received',
            with: [
                'firstName' => Str::before(trim($this->order->name), ' '),
                'price' => (float) $this->order->price, // "100.00" -> 100, as the site shows it
                'bank' => $bank,
                'iban' => trim(chunk_split($bank['iban'], 4, ' ')),
                // Same payment description as the checkout pages, so the coach can match the transfer.
                'reference' => "DekiFit {$this->order->package_name} – {$this->order->name}",
            ],
        );
    }
}
