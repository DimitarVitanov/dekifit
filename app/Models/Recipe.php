<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $guarded = [];

    protected $casts = [
        'ingredients' => 'array',
        'instructions' => 'array',
        'tags' => 'array',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->orderBy('published_at', 'desc');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function getTotalTimeAttribute(): int
    {
        return ($this->prep_time ?? 0) + ($this->cook_time ?? 0);
    }

    public function getFormattedTimeAttribute(): string
    {
        $total = $this->total_time;
        if ($total < 60) {
            return $total . ' min';
        }
        $hours = floor($total / 60);
        $mins = $total % 60;
        return $hours . 'h ' . ($mins > 0 ? $mins . 'm' : '');
    }
}
