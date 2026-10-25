<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    public const OPEN = 'open';         // waiting for the coach
    public const ANSWERED = 'answered'; // coach replied
    public const CLOSED = 'closed';

    protected $guarded = [];

    protected $casts = [
        'last_reply_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class)->oldest();
    }

    public function addMessage(User $author, string $body): TicketMessage
    {
        $message = $this->messages()->create(['user_id' => $author->id, 'body' => $body]);

        $this->update([
            'status' => $author->is_admin ? self::ANSWERED : self::OPEN,
            'last_reply_at' => now(),
        ]);

        return $message;
    }
}
