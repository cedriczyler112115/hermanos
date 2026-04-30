<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Performance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PerformanceController extends Controller
{
    private const TITLE_MAX = 80;
    private const DESCRIPTION_MAX = 600;

    public function index(Request $request)
    {
        $perPageRaw = strtolower(trim((string) $request->query('per_page', '10')));
        $allowed = ['10', '20', '50', 'all'];
        if (! in_array($perPageRaw, $allowed, true)) {
            $perPageRaw = '10';
        }

        $query = Performance::query()->orderByDesc('created_at');

        $isAll = $perPageRaw === 'all';

        if ($isAll) {
            $performances = $query->get();
        } else {
            $performances = $query->paginate((int) $perPageRaw)->withQueryString();
        }

        return view('admin.performances.index', [
            'performances' => $performances,
            'perPage' => $perPageRaw,
            'perPageOptions' => ['10', '20', '50', 'all'],
            'isAll' => $isAll,
        ]);
    }

    public function create()
    {
        return view('admin.performances.create', [
            'performance' => new Performance(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        $performance = Performance::query()->create($validated);

        $this->bumpPublicVersion('performances_public_version');

        return redirect()
            ->route('admin.performances.edit', $performance)
            ->with('status', 'Performance created.');
    }

    public function edit(Performance $performance)
    {
        return view('admin.performances.edit', [
            'performance' => $performance,
        ]);
    }

    public function update(Request $request, Performance $performance)
    {
        $validated = $this->validated($request);

        $performance->update($validated);

        $this->bumpPublicVersion('performances_public_version');

        return redirect()
            ->route('admin.performances.edit', $performance)
            ->with('status', 'Performance updated.');
    }

    public function destroy(Performance $performance)
    {
        $performance->delete();

        $this->bumpPublicVersion('performances_public_version');

        return redirect()
            ->route('admin.performances.index')
            ->with('status', 'Performance deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:'.self::TITLE_MAX],
            'description' => ['required', 'string', 'max:'.self::DESCRIPTION_MAX],
            'youtube_url' => [
                'required',
                'url',
                'max:2048',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $embed = $this->youtubeEmbedUrl(is_string($value) ? $value : null);
                    if (! $embed) {
                        $fail('Please provide a valid YouTube URL.');
                    }
                },
            ],
            'tags' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function youtubeEmbedUrl(?string $url): ?string
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
        if ($id === '') {
            return null;
        }

        return 'https://www.youtube-nocookie.com/embed/'.rawurlencode($id);
    }

    private function bumpPublicVersion(string $cacheKey): void
    {
        if (! Cache::has($cacheKey)) {
            Cache::forever($cacheKey, 1);

            return;
        }

        Cache::increment($cacheKey);
    }
}
