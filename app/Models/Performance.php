<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'title',
    'description',
    'youtube_url',
    'tags',
])]
class Performance extends Model
{
    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        $id = $this->youtubeVideoId($this->youtube_url);
        if (! $id) {
            return null;
        }

        return 'https://www.youtube-nocookie.com/embed/'.rawurlencode($id).'?rel=0&playsinline=1';
    }

    private function youtubeVideoId(?string $url): ?string
    {
        $url = is_string($url) ? trim($url) : '';
        if ($url === '') {
            return null;
        }

        $id = null;
        $parts = parse_url($url);

        $host = isset($parts['host']) ? strtolower((string) $parts['host']) : '';
        $path = isset($parts['path']) ? (string) $parts['path'] : '';
        $query = isset($parts['query']) ? (string) $parts['query'] : '';

        if (str_contains($host, 'youtu.be')) {
            $id = ltrim($path, '/');
        } elseif (str_contains($host, 'youtube.com')) {
            if (str_starts_with($path, '/watch')) {
                parse_str($query, $q);
                $id = isset($q['v']) ? (string) $q['v'] : null;
            } elseif (str_starts_with($path, '/embed/')) {
                $id = trim(str_replace('/embed/', '', $path), '/');
            } elseif (str_starts_with($path, '/shorts/')) {
                $id = trim(str_replace('/shorts/', '', $path), '/');
            }
        }

        $id = is_string($id) ? trim($id) : '';

        return $id !== '' ? $id : null;
    }
}
