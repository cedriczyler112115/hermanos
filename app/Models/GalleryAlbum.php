<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'album_name',
    'title',
    'description',
    'tags',
    'cover_photo_path',
    'cover_photo_thumb_path',
    'is_published',
])]
class GalleryAlbum extends Model
{
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function photos(): HasMany
    {
        return $this->hasMany(GalleryPhoto::class)->orderBy('sort_order')->orderBy('id');
    }
}
