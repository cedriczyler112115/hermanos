<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title',
    'composer',
    'file_path',
    'file_original_name',
    'file_mime',
    'file_size',
    'view_count',
    'download_count',
])]
class MusicSheet extends Model
{
    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'view_count' => 'integer',
            'download_count' => 'integer',
        ];
    }

    public function getFileUrlAttribute(): string
    {
        return asset('storage/'.ltrim((string) $this->file_path, '/'));
    }

    public function getIsPdfAttribute(): bool
    {
        $mime = strtolower((string) $this->file_mime);
        if ($mime === 'application/pdf') {
            return true;
        }

        return str_ends_with(strtolower((string) $this->file_path), '.pdf');
    }

    public function getIsImageAttribute(): bool
    {
        return ! $this->is_pdf;
    }
}
