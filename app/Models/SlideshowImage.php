<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlideshowImage extends Model
{
    protected $fillable = [
        'uploaded_by',
        'base_name',
        'original_name',
        'original_mime',
        'original_size',
        'original_path',
        'desktop_path',
        'desktop_size',
        'desktop_width',
        'desktop_height',
        'mobile_path',
        'mobile_size',
        'thumb_path',
        'thumb_size',
    ];
}

