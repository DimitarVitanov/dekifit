<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $guarded = [];

    protected $casts = [
        'features' => 'array',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->orderBy('sort_order');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
