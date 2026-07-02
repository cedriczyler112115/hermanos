<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'author',
        'category',
        'description',
        'fb_link',
        'fb_embed_code',
        'posted_at',
        'featured_image_path',
        'featured_image_thumb_path',
        'status',
    ];

    protected $casts = [
        'posted_at' => 'datetime',
    ];
}
