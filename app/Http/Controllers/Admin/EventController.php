<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\ImageStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $events = Event::query()
            ->when($q !== '', function ($query) use ($q) {
                $like = '%'.$q.'%';

                $query->where(function ($inner) use ($like) {
                    $inner
                        ->where('title', 'like', $like)
                        ->orWhere('event_type', 'like', $like)
                        ->orWhere('location', 'like', $like);
                });
            })
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.events.index', [
            'events' => $events,
            'q' => $q,
        ]);
    }

    public function create()
    {
        return view('admin.events.create', [
            'event' => new Event(),
        ]);
    }

    public function store(Request $request, ImageStorage $images)
    {
        $validated = $this->validatedEvent($request);

        try {
            $event = DB::transaction(function () use ($request, $validated, $images) {
                if ($request->hasFile('photo')) {
                    $stored = $images->storeImageWithThumb(
                        $request->file('photo'),
                        'events',
                        2000,
                        1200,
                        720,
                        432,
                    );

                    $validated['photo_path'] = $stored['path'];
                    $validated['photo_thumb_path'] = $stored['thumb_path'];
                }

                return Event::query()->create($validated);
            });
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->withErrors(['photo' => 'Failed to save the event photo. Please try a different image.']);
        }

        Log::info('admin.events.create', [
            'user_id' => $request->user()?->id,
            'event_id' => $event->id,
        ]);

        $this->bumpPublicVersion('events_public_version');

        return redirect()
            ->route('admin.events.edit', $event)
            ->with('status', 'Event created.');
    }

    public function edit(Event $event)
    {
        return view('admin.events.edit', [
            'event' => $event,
        ]);
    }

    public function update(Request $request, Event $event, ImageStorage $images)
    {
        $validated = $this->validatedEvent($request);

        $oldPhoto = $event->photo_path;
        $oldThumb = $event->photo_thumb_path;

        try {
            DB::transaction(function () use ($request, $event, $validated, $images, $oldPhoto, $oldThumb) {
                if ($request->hasFile('photo')) {
                    $stored = $images->storeImageWithThumb(
                        $request->file('photo'),
                        'events',
                        2000,
                        1200,
                        720,
                        432,
                    );

                    $validated['photo_path'] = $stored['path'];
                    $validated['photo_thumb_path'] = $stored['thumb_path'];
                }

                $event->update($validated);

                if ($request->hasFile('photo')) {
                    if (is_string($oldPhoto) && $oldPhoto !== '') {
                        Storage::disk('public')->delete($oldPhoto);
                    }
                    if (is_string($oldThumb) && $oldThumb !== '' && $oldThumb !== $oldPhoto) {
                        Storage::disk('public')->delete($oldThumb);
                    }
                }
            });
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->withErrors(['photo' => 'Failed to update the event photo. Please try a different image.']);
        }

        Log::info('admin.events.update', [
            'user_id' => $request->user()?->id,
            'event_id' => $event->id,
        ]);

        $this->bumpPublicVersion('events_public_version');

        return redirect()
            ->route('admin.events.edit', $event)
            ->with('status', 'Event updated.');
    }

    public function destroy(Request $request, Event $event)
    {
        $photo = $event->photo_path;
        $thumb = $event->photo_thumb_path;

        $event->delete();

        if (is_string($photo) && $photo !== '') {
            Storage::disk('public')->delete($photo);
        }
        if (is_string($thumb) && $thumb !== '' && $thumb !== $photo) {
            Storage::disk('public')->delete($thumb);
        }

        Log::info('admin.events.delete', [
            'user_id' => $request->user()?->id,
            'event_id' => $event->id,
        ]);

        $this->bumpPublicVersion('events_public_version');

        return redirect()
            ->route('admin.events.index')
            ->with('status', 'Event deleted.');
    }

    private function validatedEvent(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'schedule' => ['nullable', 'string', 'max:255'],
            'event_type' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:255'],
            'about' => ['nullable', 'string', 'max:10000'],
            'tags' => ['nullable', 'string', 'max:255'],
            'is_published' => ['sometimes', 'boolean'],
            'photo' => ['nullable', 'image', 'max:5120'],
        ]);

        $validated['is_published'] = (bool) ($validated['is_published'] ?? false);

        unset($validated['photo']);

        return $validated;
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
