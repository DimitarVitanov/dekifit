<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ExerciseVideo extends Model
{
    /**
     * Clips hosted on this site use URLs under this prefix. The .mp4 itself is kept
     * in private storage and only streamed to clients who may watch it; the
     * matching .jpg poster sits in public/ so locked videos can still show a preview.
     */
    public const HOSTED = '/videos/exercises/';

    protected $guarded = [];

    protected $appends = ['poster'];

    public static function hostedPath(string $file): string
    {
        return Storage::disk('local')->path('exercise-videos/'.$file);
    }

    protected function poster(): Attribute
    {
        return Attribute::get(function () {
            $url = $this->attributes['url'] ?? '';

            return str_starts_with($url, self::HOSTED) ? preg_replace('/\.mp4$/', '.jpg', $url) : null;
        });
    }
}
