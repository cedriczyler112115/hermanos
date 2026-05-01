<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\GalleryAlbum;
use App\Models\GalleryPhoto;
use App\Models\Member;
use App\Models\MusicSheet;
use App\Models\Performance;
use App\Models\Role;
use App\Models\SlideshowImage;
use App\Support\PublicListing;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SiteController extends Controller
{
    public function home()
    {
        $slideshowImages = [];
        $slideshowError = '';
        $slideshowWarning = '';

        try {
            $records = SlideshowImage::query()
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->get(['desktop_path', 'mobile_path', 'thumb_path', 'desktop_width', 'desktop_height']);

            if ($records->count() > 0) {
                $slideshowImages = $records
                    ->map(function ($row) {
                        $largePath = ltrim(str_replace('\\', '/', (string) $row->desktop_path), '/');
                        $mediumPath = ltrim(str_replace('\\', '/', (string) $row->mobile_path), '/');
                        $thumbPath = ltrim(str_replace('\\', '/', (string) $row->thumb_path), '/');

                        $largeUrl = asset('storage/'.$largePath);
                        $mediumUrl = asset('storage/'.$mediumPath);
                        $thumbUrl = asset('storage/'.$thumbPath);

                        if ($largePath === $mediumPath && $largePath === $thumbPath) {
                            return [
                                'large' => $largeUrl,
                                'srcset' => '',
                                'sizes' => '',
                            ];
                        }

                        $largeW = (int) ($row->desktop_width ?? 0);
                        $largeW = $largeW > 0 ? $largeW : 1600;
                        $mediumW = (int) round($largeW * 0.6);
                        $mediumW = max(480, min(1400, $mediumW));
                        $thumbW = (int) round($largeW * 0.3);
                        $thumbW = max(240, min(640, $thumbW));

                        $srcset = $thumbUrl.' '.$thumbW.'w, '.$mediumUrl.' '.$mediumW.'w, '.$largeUrl.' '.$largeW.'w';

                        return [
                            'large' => $largeUrl,
                            'srcset' => $srcset,
                            'sizes' => '100vw',
                        ];
                    })
                    ->values()
                    ->all();
            } else {
                $disk = Storage::disk('public');
                if (! $disk->exists('slideshow')) {
                    $slideshowError = 'Slideshow folder not found: storage/app/public/slideshow';
                } else {
                    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                    $paths = [];

                    $desktop = $disk->files('slideshow/desktop');
                    $paths = is_array($desktop) && count($desktop) > 0 ? $desktop : $disk->files('slideshow');

                    $supported = [];
                    $unsupportedCount = 0;

                    foreach ($paths as $path) {
                        $ext = strtolower(pathinfo((string) $path, PATHINFO_EXTENSION));
                        if (in_array($ext, $allowed, true)) {
                            $supported[] = $path;
                        } else {
                            $unsupportedCount++;
                        }
                    }

                    sort($supported);

                    if ($unsupportedCount > 0) {
                        $slideshowWarning = 'Some slideshow files were skipped because the format is not supported.';
                    }

                    $slideshowImages = array_map(function ($path) {
                        $normalized = str_replace('\\', '/', (string) $path);
                        return [
                            'large' => asset('storage/'.ltrim($normalized, '/')),
                            'srcset' => '',
                            'sizes' => '',
                        ];
                    }, $supported);
                }
            }
        } catch (\Throwable $e) {
            report($e);
            $slideshowError = 'Failed to load slideshow images.';
        }

        $upcomingEvents = [
            [
                'title' => 'Sunday Mass Service',
                'date' => 'Every Sunday',
                'location' => 'Parish Church',
                'details' => 'Liturgical songs and choir service for the community.',
            ],
            [
                'title' => 'Rehearsal Night',
                'date' => 'Weekly (Schedule Posted)',
                'location' => 'Choir Room',
                'details' => 'Voice formation, harmony practice, and new repertoire.',
            ],
            [
                'title' => 'Feast Celebration',
                'date' => 'Upcoming Feast Day',
                'location' => 'Community Venue',
                'details' => 'Special performance honoring Señor Santo Niño.',
            ],
        ];

        $members = Member::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->with(['role', 'voicePart'])
            ->limit(8)
            ->get();

        return view('site.home', [
            'slideshowImages' => $slideshowImages,
            'slideshowError' => $slideshowError,
            'slideshowWarning' => $slideshowWarning,
            'upcomingEvents' => $upcomingEvents,
            'members' => $members,
        ]);
    }

    public function history()
    {
        return view('site.history');
    }

    public function events(Request $request)
    {
        $q = PublicListing::queryString($request->query('q', ''));
        $page = max(1, (int) $request->query('page', 1));
        $perPage = PublicListing::perPage($request->query('per_page', 12));

        $version = (int) Cache::get('events_public_version', 1);
        $cacheKey = 'public_events:v'.$version.':q='.md5($q).':per_page='.$perPage.':page='.$page;

        $cached = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($q, $page, $perPage) {
            $base = Event::query()
                ->where('is_published', true)
                ->when($q !== '', fn ($query) => PublicListing::applySearch($query, $q, ['title', 'about', 'tags', 'event_type', 'location']))
                ->orderByDesc('created_at');

            $total = (clone $base)->count();

            $items = $base
                ->forPage($page, $perPage)
                ->get([
                    'id',
                    'title',
                    'schedule',
                    'event_type',
                    'location',
                    'about',
                    'tags',
                    'photo_path',
                    'photo_thumb_path',
                    'created_at',
                ])
                ->map(fn ($event) => [
                    'id' => $event->id,
                    'title' => $event->title,
                    'schedule' => $event->schedule,
                    'event_type' => $event->event_type,
                    'location' => $event->location,
                    'about' => $event->about,
                    'tags' => $event->tags,
                    'photo_path' => $event->photo_path,
                    'photo_thumb_path' => $event->photo_thumb_path,
                    'created_at' => optional($event->created_at)->toDateString(),
                ])
                ->all();

            return [
                'items' => $items,
                'total' => $total,
            ];
        });

        $paginator = new LengthAwarePaginator(
            $cached['items'] ?? [],
            (int) ($cached['total'] ?? 0),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ],
        );

        if ($request->expectsJson()) {
            return response()->json([
                'html' => view('site.partials.events-results', [
                    'events' => $paginator,
                ])->render(),
            ]);
        }

        return view('site.events', [
            'events' => $paginator,
            'q' => $q,
            'perPage' => $perPage,
            'perPageOptions' => [12, 24, 48],
        ]);
    }

    public function gallery(Request $request)
    {
        $q = PublicListing::queryString($request->query('q', ''));
        $page = max(1, (int) $request->query('page', 1));
        $perPage = PublicListing::perPage($request->query('per_page', 12));

        $version = (int) Cache::get('gallery_public_version', 1);
        $cacheKey = 'public_gallery_albums:v'.$version.':q='.md5($q).':per_page='.$perPage.':page='.$page;

        $cached = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($q, $page, $perPage) {
            $base = GalleryAlbum::query()
                ->where('is_published', true)
                ->withCount('photos')
                ->when($q !== '', fn ($query) => PublicListing::applySearch($query, $q, ['album_name', 'title', 'description', 'tags']))
                ->orderByDesc('created_at');

            $total = (clone $base)->count();

            $albums = $base
                ->forPage($page, $perPage)
                ->get([
                    'id',
                    'album_name',
                    'title',
                    'description',
                    'tags',
                    'cover_photo_path',
                    'cover_photo_thumb_path',
                    'created_at',
                ])
                ->values();

            $albumIds = $albums->pluck('id')->all();
            $previewByAlbum = [];

            if (count($albumIds) > 0) {
                $photos = GalleryPhoto::query()
                    ->whereIn('gallery_album_id', $albumIds)
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get(['id', 'gallery_album_id', 'photo_path', 'photo_thumb_path']);

                foreach ($photos as $photo) {
                    $albumId = (int) $photo->gallery_album_id;
                    $previewByAlbum[$albumId] ??= [];

                    if (count($previewByAlbum[$albumId]) >= 3) {
                        continue;
                    }

                    $previewByAlbum[$albumId][] = $photo->photo_thumb_path ?: $photo->photo_path;
                }
            }

            $items = $albums
                ->map(fn ($album) => [
                    'id' => $album->id,
                    'album_name' => $album->album_name,
                    'title' => $album->title,
                    'description' => $album->description,
                    'tags' => $album->tags,
                    'cover_photo_path' => $album->cover_photo_path,
                    'cover_photo_thumb_path' => $album->cover_photo_thumb_path,
                    'photos_count' => (int) $album->photos_count,
                    'preview_thumb_paths' => $previewByAlbum[(int) $album->id] ?? [],
                    'created_at' => optional($album->created_at)->toDateString(),
                ])
                ->all();

            return [
                'items' => $items,
                'total' => $total,
            ];
        });

        $paginator = new LengthAwarePaginator(
            $cached['items'] ?? [],
            (int) ($cached['total'] ?? 0),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ],
        );

        if ($request->expectsJson()) {
            return response()->json([
                'html' => view('site.partials.gallery-results', [
                    'albums' => $paginator,
                ])->render(),
            ]);
        }

        return view('site.gallery', [
            'albums' => $paginator,
            'q' => $q,
            'perPage' => $perPage,
            'perPageOptions' => [12, 24, 48],
        ]);
    }

    public function galleryAlbum(GalleryAlbum $album)
    {
        abort_unless($album->is_published, 404);

        $version = (int) Cache::get('gallery_public_version', 1);
        $cacheKey = 'public_gallery_album:v'.$version.':album='.$album->id.':updated='.optional($album->updated_at)->timestamp;

        $data = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($album) {
            $album->load(['photos']);

            return [
                'album' => [
                    'id' => $album->id,
                    'album_name' => $album->album_name,
                    'title' => $album->title,
                    'description' => $album->description,
                ],
                'photos' => $album->photos->map(fn ($photo) => [
                    'id' => $photo->id,
                    'photo_path' => $photo->photo_path,
                    'photo_thumb_path' => $photo->photo_thumb_path,
                ])->all(),
            ];
        });

        return view('site.gallery-album', [
            'album' => $data['album'],
            'photos' => $data['photos'],
        ]);
    }

    public function performances(Request $request)
    {
        $q = PublicListing::queryString($request->query('q', ''));
        $page = max(1, (int) $request->query('page', 1));
        $perPage = PublicListing::perPage($request->query('per_page', 12));

        $version = (int) Cache::get('performances_public_version', 1);
        $cacheKey = 'public_performances:v'.$version.':q='.md5($q).':per_page='.$perPage.':page='.$page;

        $cached = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($q, $page, $perPage) {
            $base = Performance::query()
                ->when($q !== '', fn ($query) => PublicListing::applySearch($query, $q, ['title', 'description', 'tags']))
                ->orderByDesc('created_at');

            $total = (clone $base)->count();

            $items = $base
                ->forPage($page, $perPage)
                ->get(['id', 'title', 'description', 'youtube_url', 'tags', 'created_at'])
                ->map(function ($performance) {
                    $youtubeId = $this->youtubeVideoId($performance->youtube_url);

                    return [
                        'id' => $performance->id,
                        'title' => $performance->title,
                        'description' => $performance->description,
                        'youtube_url' => $performance->youtube_url,
                        'tags' => $performance->tags,
                        'youtube_embed_url' => $this->youtubeEmbedUrl($performance->youtube_url),
                        'youtube_thumb_url' => $youtubeId ? 'https://i.ytimg.com/vi/'.rawurlencode($youtubeId).'/hqdefault.jpg' : null,
                        'created_at' => optional($performance->created_at)->toDateString(),
                    ];
                })
                ->all();

            return [
                'items' => $items,
                'total' => $total,
            ];
        });

        $paginator = new LengthAwarePaginator(
            $cached['items'] ?? [],
            (int) ($cached['total'] ?? 0),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ],
        );

        if ($request->expectsJson()) {
            return response()->json([
                'html' => view('site.partials.performances-results', [
                    'performances' => $paginator,
                ])->render(),
            ]);
        }

        return view('site.performances', [
            'performances' => $paginator,
            'q' => $q,
            'perPage' => $perPage,
            'perPageOptions' => [12, 24, 48],
        ]);
    }

    public function musicSheets(Request $request)
    {
        $q = PublicListing::queryString($request->query('q', ''), 255);
        $page = max(1, (int) $request->query('page', 1));

        $rawPerPage = $request->query('per_page', 10);
        $showAll = is_string($rawPerPage) && strtolower($rawPerPage) === 'all';
        $perPage = $showAll ? 10 : PublicListing::perPage($rawPerPage, [10, 20, 50], 10);

        $base = MusicSheet::query()
            ->when($q !== '', function ($query) use ($q) {
                $like = '%'.$q.'%';
                $query->where(function ($inner) use ($like) {
                    $inner
                        ->where('title', 'like', $like)
                        ->orWhere('composer', 'like', $like);
                });
            })
            ->orderByDesc('created_at');

        if ($showAll) {
            $items = $base->get();
            $total = $items->count();
            $paginator = new LengthAwarePaginator(
                $items,
                $total,
                max(1, $total),
                1,
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ],
            );
        } else {
            $paginator = $base
                ->paginate($perPage, ['*'], 'page', $page)
                ->withQueryString();
        }

        if ($request->expectsJson()) {
            return response()->json([
                'html' => view('site.partials.music-sheets-results', [
                    'sheets' => $paginator,
                ])->render(),
            ]);
        }

        return view('site.music-sheets', [
            'sheets' => $paginator,
            'q' => $q,
            'perPage' => $showAll ? 'all' : $perPage,
            'perPageOptions' => [10, 20, 50],
        ]);
    }

    public function members()
    {
        $members = Member::query()
            ->where('is_active', true)
            ->with(['role', 'voicePart'])
            ->get();

        $members = $members->sortBy(function (Member $member) {
            $fullName = trim((string) $member->name);
            $parts = preg_split('/\s+/', $fullName) ?: [];
            $last = $parts ? (string) end($parts) : '';

            return [mb_strtolower($last), mb_strtolower($fullName)];
        })->values();

        $groups = $members
            ->groupBy(function (Member $member) {
                return (string) ($member->voicePart?->name ?: 'Unassigned');
            })
            ->sortKeysUsing(function (string $a, string $b) {
                $order = [
                    'soprano' => 1,
                    'alto' => 2,
                    'tenor' => 3,
                    'bass' => 4,
                    'unassigned' => 99,
                ];

                $ka = mb_strtolower(trim($a));
                $kb = mb_strtolower(trim($b));

                $oa = $order[$ka] ?? 50;
                $ob = $order[$kb] ?? 50;

                if ($oa !== $ob) {
                    return $oa <=> $ob;
                }

                return $ka <=> $kb;
            });

        return view('site.members', [
            'memberGroups' => $groups,
        ]);
    }

    public function officers()
    {
        $officerRoles = Role::query()
            ->whereBetween('id', [1, 7])
            ->orderBy('id')
            ->get(['id', 'name']);

        $officerMembersByRoleId = Member::query()
            ->where('is_active', true)
            ->whereIn('role_id', $officerRoles->pluck('id'))
            ->with(['role', 'voicePart'])
            ->orderBy('name')
            ->get()
            ->groupBy('role_id');

        $presidentRole = $officerRoles->firstWhere('id', 1);
        $vicePresidentRole = $officerRoles->firstWhere('id', 2);
        $presidentMembers = $officerMembersByRoleId->get(1, collect());
        $vicePresidentMembers = $officerMembersByRoleId->get(2, collect());

        $remainingOfficers = Member::query()
            ->where('is_active', true)
            ->whereIn('role_id', $officerRoles->pluck('id')->reject(fn ($id) => in_array((int) $id, [1, 2], true)))
            ->with(['role', 'voicePart'])
            ->orderBy('role_id')
            ->orderBy('name')
            ->get();

        $choirMemberRole = Role::query()
            ->whereRaw('LOWER(name) = ?', ['choir member'])
            ->orWhereRaw('LOWER(name) LIKE ?', ['%choir member%'])
            ->orderBy('id')
            ->first(['id', 'name']);

        if (! $choirMemberRole) {
            $choirMemberRole = Role::query()
                ->whereRaw('LOWER(name) LIKE ?', ['%member%'])
                ->orderBy('id')
                ->first(['id', 'name']);
        }

        $choirMembers = $choirMemberRole
            ? Member::query()
                ->where('is_active', true)
                ->where('role_id', $choirMemberRole->id)
                ->with(['role', 'voicePart'])
                ->orderBy('name')
                ->get()
            : collect();

        return view('site.officers', [
            'presidentRole' => $presidentRole,
            'presidentMembers' => $presidentMembers,
            'vicePresidentRole' => $vicePresidentRole,
            'vicePresidentMembers' => $vicePresidentMembers,
            'remainingOfficers' => $remainingOfficers,
            'choirMemberRole' => $choirMemberRole,
            'choirMembers' => $choirMembers,
        ]);
    }

    public function boardOfDirectors()
    {
        $directors = Member::query()
            ->where('is_active', true)
            ->where('is_bod', true)
            ->with(['role', 'voicePart'])
            ->orderBy('name')
            ->get();

        return view('site.board-of-directors', [
            'directors' => $directors,
        ]);
    }

    public function contact()
    {
        return view('site.contact');
    }

    private function youtubeEmbedUrl(?string $url): ?string
    {
        $id = $this->youtubeVideoId($url);
        if (! $id) {
            return null;
        }

        return 'https://www.youtube-nocookie.com/embed/'.rawurlencode($id);
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
