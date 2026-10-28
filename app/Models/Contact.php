<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    // The topics offered on the contact form (resources/js/Pages/Contact.vue).
    public const SUBJECTS = [
        'packages' => 'Packages & Pricing',
        'nutrition' => 'Nutrition Questions',
        'order' => 'Existing Order',
        'other' => 'Other',
    ];

    protected $guarded = [];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function getSubjectLabelAttribute(): string
    {
        return self::SUBJECTS[$this->subject] ?? ($this->subject ?: 'General question');
    }
}
