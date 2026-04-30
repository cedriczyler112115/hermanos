<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;

class PublicListing
{
    /**
     * @param  array<int,int>  $allowed
     */
    public static function perPage(mixed $raw, array $allowed = [12, 24, 48], int $default = 12): int
    {
        $value = is_string($raw) || is_int($raw) ? (int) $raw : 0;

        return in_array($value, $allowed, true) ? $value : $default;
    }

    public static function queryString(mixed $raw, int $maxLen = 120): string
    {
        $q = trim((string) ($raw ?? ''));
        if ($q === '') {
            return '';
        }

        $q = preg_replace('/\s+/u', ' ', $q) ?? $q;

        return mb_substr($q, 0, $maxLen);
    }

    /**
     * @param  array<int,string>  $columns
     */
    public static function applySearch(Builder $query, string $q, array $columns): Builder
    {
        $q = trim($q);
        if ($q === '') {
            return $query;
        }

        $like = '%'.$q.'%';

        return $query->where(function (Builder $inner) use ($columns, $like) {
            foreach ($columns as $column) {
                $inner->orWhere($column, 'like', $like);
            }
        });
    }
}

