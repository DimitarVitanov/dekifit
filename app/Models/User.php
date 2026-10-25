<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'phone', 'program_id', 'must_change_password', 'unlocked_video_categories'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'must_change_password' => 'boolean',
            'unlocked_video_categories' => 'array',
        ];
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function progressEntries()
    {
        return $this->hasMany(ProgressEntry::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function timeSlots()
    {
        return $this->hasMany(TimeSlot::class);
    }

    /**
     * Videos attached to exercises in the training plans this client can see.
     */
    public function programVideoIds()
    {
        return TrainingPlan::visibleTo($this)->get(['days'])->pluck('days')
            ->flatten(1)->pluck('exercises')->flatten(1)->pluck('video_id')->filter()->unique()->values();
    }

    /**
     * The coach sees everything; a client sees the body parts the coach has
     * unlocked, plus any video attached to their training program.
     */
    public function canWatch(ExerciseVideo $video): bool
    {
        return $this->is_admin
            || in_array($video->category, $this->unlocked_video_categories ?? [], true)
            || $this->programVideoIds()->contains($video->id);
    }

    public function scopeClients($query)
    {
        return $query->where('is_admin', false);
    }
}
