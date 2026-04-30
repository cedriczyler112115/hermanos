<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title',
    'schedule',
    'event_type',
    'location',
    'about',
    'tags',
    'photo_path',
    'photo_thumb_path',
    'is_published',
])]
class Event extends Model
{
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }
}
