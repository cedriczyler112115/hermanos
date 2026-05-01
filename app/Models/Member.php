<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'email_address',
    'start_date',
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
    'is_bod',
])]
class Member extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_bod' => 'boolean',
            'start_date' => 'date',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function voicePart(): BelongsTo
    {
        return $this->belongsTo(VoicePart::class);
    }
}
