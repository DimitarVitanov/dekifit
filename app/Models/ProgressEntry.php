<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgressEntry extends Model
{
    protected $guarded = [];

    protected $casts = [
        'logged_on' => 'date:Y-m-d',
        'weight_kg' => 'float',
        'body_fat_pct' => 'float',
        'waist_cm' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
