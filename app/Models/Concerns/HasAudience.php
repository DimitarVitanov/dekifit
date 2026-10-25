<?php

namespace App\Models\Concerns;

use App\Models\Program;
use App\Models\User;

/**
 * For plans the coach hands out to one client (user_id), to everyone on a
 * package (program_id), or — when both are null — to every client.
 */
trait HasAudience
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Plans a client can see, their personal ones first.
     */
    public function scopeVisibleTo($query, User $user)
    {
        return $query->where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orWhere(function ($query) use ($user) {
                    $query->whereNull('user_id')->where(function ($query) use ($user) {
                        $query->whereNull('program_id');

                        if ($user->program_id) {
                            $query->orWhere('program_id', $user->program_id);
                        }
                    });
                });
        })->orderByRaw('user_id is null')->latest('updated_at');
    }
}
