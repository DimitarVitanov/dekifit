<?php

namespace App\Models;

use App\Models\Concerns\HasAudience;
use Illuminate\Database\Eloquent\Model;

class TrainingPlan extends Model
{
    use HasAudience;

    protected $guarded = [];

    protected $casts = [
        'days' => 'array',
    ];
}
