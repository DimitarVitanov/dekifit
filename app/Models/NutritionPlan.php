<?php

namespace App\Models;

use App\Models\Concerns\HasAudience;
use Illuminate\Database\Eloquent\Model;

class NutritionPlan extends Model
{
    use HasAudience;

    protected $guarded = [];

    protected $casts = [
        'meals' => 'array',
    ];
}
