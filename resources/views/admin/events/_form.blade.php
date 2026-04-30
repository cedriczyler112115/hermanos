<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
        <label for="title" class="block text-sm font-medium text-slate-800">Title</label>
        <input id="title" name="title" type="text" value="{{ old('title', $event->title) }}" required class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0" />
        @error('title')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="schedule" class="block text-sm font-medium text-slate-800">Schedule</label>
        <input id="schedule" name="schedule" type="text" value="{{ old('schedule', $event->schedule) }}" class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0" />
        @error('schedule')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="event_type" class="block text-sm font-medium text-slate-800">Event type</label>
        <input id="event_type" name="event_type" type="text" value="{{ old('event_type', $event->event_type) }}" class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0" />
        @error('event_type')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label for="location" class="block text-sm font-medium text-slate-800">Location</label>
        <input id="location" name="location" type="text" value="{{ old('location', $event->location) }}" class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0" />
        @error('location')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label for="about" class="block text-sm font-medium text-slate-800">About the event</label>
        <textarea id="about" name="about" rows="7" class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0">{{ old('about', $event->about) }}</textarea>
        @error('about')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label for="tags" class="block text-sm font-medium text-slate-800">Tags (optional)</label>
        <input id="tags" name="tags" type="text" value="{{ old('tags', $event->tags) }}" placeholder="e.g. fiesta, youth, charity" class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0" />
        @error('tags')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div class="flex items-center gap-2 md:col-span-2">
        <input id="is_published" name="is_published" type="checkbox" value="1" {{ old('is_published', $event->is_published ?? true) ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-[var(--color-primary)] focus:ring-0" />
        <label for="is_published" class="text-sm font-medium text-slate-800">Show publicly</label>
        @error('is_published')
            <div class="text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label for="photo" class="block text-sm font-medium text-slate-800">Photo (optional)</label>
        <input id="photo" name="photo" type="file" accept="image/*" class="mt-1 block w-full text-sm text-slate-700 file:mr-4 file:rounded-lg file:border file:border-[var(--color-border)] file:bg-[var(--color-surface)] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-slate-900 hover:file:bg-[var(--color-muted)]" />
        @error('photo')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror

        @if ($event->photo_thumb_path || $event->photo_path)
            <div class="mt-3">
                <div class="text-sm text-slate-700">Current photo</div>
                <img src="{{ asset('storage/' . ($event->photo_thumb_path ?: $event->photo_path)) }}" alt="{{ $event->title }}" class="mt-2 h-32 w-full max-w-md rounded-2xl object-cover ring-1 ring-[var(--color-border)]" />
            </div>
        @endif
    </div>
</div>
