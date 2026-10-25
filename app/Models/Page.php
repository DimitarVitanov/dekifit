<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class)->orderBy('sort_order');
    }

    public function visibleSections(): HasMany
    {
        return $this->hasMany(Section::class)->where('is_visible', true)->orderBy('sort_order');
    }

    public function section(string $key): ?Section
    {
        return $this->sections()->where('key', $key)->first();
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }
}
