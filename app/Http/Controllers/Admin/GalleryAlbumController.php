<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryAlbum;
use App\Models\GalleryPhoto;
use App\Services\ImageStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GalleryAlbumController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $albums = GalleryAlbum::query()
            ->withCount('photos')
            ->when($q !== '', function ($query) use ($q) {
                $like = '%'.$q.'%';

                $query->where(function ($inner) use ($like) {
                    $inner
                        ->where('album_name', 'like', $like)
                        ->orWhere('title', 'like', $like);
                });
            })
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.gallery-albums.index', [
            'albums' => $albums,
            'q' => $q,
        ]);
    }

    public function create()
    {
        return view('admin.gallery-albums.create', [
            'album' => new GalleryAlbum(),
        ]);
    }

    public function store(Request $request, ImageStorage $images)
    {
        $validated = $this->validatedAlbum($request, true);

        try {
            $album = DB::transaction(function () use ($request, $validated, $images) {
                $album = GalleryAlbum::query()->create($validated);

                $this->storePhotos($album, $request, $images);

                return $album->fresh(['photos']);
            });
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->withErrors(['photos' => 'Failed to save one or more images. Please try again.']);
        }

        Log::info('admin.gallery_albums.create', [
            'user_id' => $request->user()?->id,
            'album_id' => $album->id,
        ]);

        $this->bumpPublicVersion('gallery_public_version');

        return redirect()
            ->route('admin.gallery_albums.edit', $album)
            ->with('status', 'Gallery album created.');
    }

    public function edit(GalleryAlbum $gallery_album)
    {
        $gallery_album->load(['photos']);

        return view('admin.gallery-albums.edit', [
            'album' => $gallery_album,
        ]);
    }

    public function update(Request $request, GalleryAlbum $gallery_album, ImageStorage $images)
    {
        $validated = $this->validatedAlbum($request, false);

        try {
            DB::transaction(function () use ($request, $validated, $gallery_album, $images) {
                $gallery_album->update($validated);
                $this->storePhotos($gallery_album, $request, $images);
            });
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->withErrors(['photos' => 'Failed to save one or more images. Please try again.']);
        }

        Log::info('admin.gallery_albums.update', [
            'user_id' => $request->user()?->id,
            'album_id' => $gallery_album->id,
        ]);

        $this->bumpPublicVersion('gallery_public_version');

        return redirect()
            ->route('admin.gallery_albums.edit', $gallery_album)
            ->with('status', 'Gallery album updated.');
    }

    public function destroy(Request $request, GalleryAlbum $gallery_album)
    {
        $gallery_album->load('photos');

        foreach ($gallery_album->photos as $photo) {
            $this->deletePhotoFiles($photo->photo_path, $photo->photo_thumb_path);
        }

        $this->deletePhotoFiles($gallery_album->cover_photo_path, $gallery_album->cover_photo_thumb_path);

        $gallery_album->delete();

        Log::info('admin.gallery_albums.delete', [
            'user_id' => $request->user()?->id,
            'album_id' => $gallery_album->id,
        ]);

        $this->bumpPublicVersion('gallery_public_version');

        return redirect()
            ->route('admin.gallery_albums.index')
            ->with('status', 'Gallery album deleted.');
    }

    public function destroyPhoto(Request $request, GalleryAlbum $gallery_album, GalleryPhoto $photo)
    {
        if ($photo->gallery_album_id !== $gallery_album->id) {
            abort(404);
        }

        $isCover = is_string($gallery_album->cover_photo_path) && $gallery_album->cover_photo_path !== '' && $gallery_album->cover_photo_path === $photo->photo_path;

        $this->deletePhotoFiles($photo->photo_path, $photo->photo_thumb_path);
        $photo->delete();

        if ($isCover) {
            $next = $gallery_album->photos()->first();
            $gallery_album->update([
                'cover_photo_path' => $next?->photo_path,
                'cover_photo_thumb_path' => $next?->photo_thumb_path,
            ]);
        }

        Log::info('admin.gallery_photos.delete', [
            'user_id' => $request->user()?->id,
            'album_id' => $gallery_album->id,
            'photo_id' => $photo->id,
        ]);

        $this->bumpPublicVersion('gallery_public_version');

        return redirect()
            ->route('admin.gallery_albums.edit', $gallery_album)
            ->with('status', 'Photo deleted.');
    }

    public function reorderPhotos(Request $request, GalleryAlbum $gallery_album)
    {
        $validated = $request->validate([
            'photo_ids' => ['required', 'array', 'min:1'],
            'photo_ids.*' => ['required', 'integer'],
        ]);

        $photoIds = array_values(array_unique($validated['photo_ids']));

        $existing = $gallery_album->photos()->pluck('id')->all();
        sort($existing);
        $incoming = $photoIds;
        sort($incoming);

        if ($existing !== $incoming) {
            return response()->json(['message' => 'Invalid photo order.'], 422);
        }

        DB::transaction(function () use ($gallery_album, $photoIds) {
            foreach ($photoIds as $index => $id) {
                GalleryPhoto::query()
                    ->where('gallery_album_id', $gallery_album->id)
                    ->whereKey($id)
                    ->update(['sort_order' => $index]);
            }
        });

        Log::info('admin.gallery_photos.reorder', [
            'user_id' => $request->user()?->id,
            'album_id' => $gallery_album->id,
            'photo_ids' => $photoIds,
        ]);

        $this->bumpPublicVersion('gallery_public_version');

        return response()->json(['message' => 'Order saved.']);
    }

    private function validatedAlbum(Request $request, bool $isCreate): array
    {
        $rules = [
            'album_name' => ['required', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'tags' => ['nullable', 'string', 'max:255'],
            'is_published' => ['sometimes', 'boolean'],
            'photos' => ['nullable', 'array'],
            'photos.*' => ['nullable', 'image', 'max:5120'],
        ];

        if ($isCreate) {
            $rules['photos'] = ['required', 'array', 'min:1'];
            $rules['photos.*'] = ['required', 'image', 'max:5120'];
        }

        $validated = $request->validate($rules);

        $validated['is_published'] = (bool) ($validated['is_published'] ?? false);

        unset($validated['photos']);

        return $validated;
    }

    private function storePhotos(GalleryAlbum $album, Request $request, ImageStorage $images): void
    {
        $files = $request->file('photos');
        if (! is_array($files) || count($files) === 0) {
            return;
        }

        $maxSort = (int) ($album->photos()->max('sort_order') ?? 0);
        $sort = $maxSort + 1;

        foreach ($files as $file) {
            if (! $file) {
                continue;
            }

            $stored = $images->storeImageWithThumb(
                $file,
                'gallery/photos',
                2200,
                1600,
                640,
                480,
            );

            $photo = $album->photos()->create([
                'photo_path' => $stored['path'],
                'photo_thumb_path' => $stored['thumb_path'],
                'sort_order' => $sort,
            ]);

            $sort++;

            if (! $album->cover_photo_path) {
                $album->update([
                    'cover_photo_path' => $photo->photo_path,
                    'cover_photo_thumb_path' => $photo->photo_thumb_path,
                ]);
            }
        }
    }

    private function deletePhotoFiles(?string $path, ?string $thumbPath): void
    {
        if (is_string($path) && $path !== '') {
            Storage::disk('public')->delete($path);
        }
        if (is_string($thumbPath) && $thumbPath !== '' && $thumbPath !== $path) {
            Storage::disk('public')->delete($thumbPath);
        }
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
