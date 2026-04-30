<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'address',
    'hobbies',
    'role_id',
    'voice_part_id',
    'bio',
    'description',
    'photo_path',
    'facebook_url',
    'youtube_url',
    'is_active',
])]
class Member extends Model
{
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function voicePart(): BelongsTo
    {
        return $this->belongsTo(VoicePart::class);
    }
}
