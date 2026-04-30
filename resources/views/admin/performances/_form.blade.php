<div class="grid grid-cols-1 gap-4">
    <div>
        <label for="title" class="block text-sm font-medium text-slate-800">Title</label>
        <input id="title" name="title" type="text" value="{{ old('title', $performance->title) }}" maxlength="80" required class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0" />
        <div class="mt-1 text-xs text-slate-600">Maximum 80 characters.</div>
        @error('title')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-slate-800">Description</label>
        <textarea id="description" name="description" rows="8" maxlength="600" required class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0">{{ old('description', $performance->description) }}</textarea>
        <div class="mt-1 text-xs text-slate-600">Maximum 600 characters.</div>
        @error('description')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="youtube_url" class="block text-sm font-medium text-slate-800">YouTube URL</label>
        <input id="youtube_url" name="youtube_url" type="url" value="{{ old('youtube_url', $performance->youtube_url) }}" placeholder="https://www.youtube.com/watch?v=..." required class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0" />
        @error('youtube_url')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="tags" class="block text-sm font-medium text-slate-800">Tags (optional)</label>
        <input id="tags" name="tags" type="text" value="{{ old('tags', $performance->tags) }}" placeholder="e.g. mass, praise, special" class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0" />
        @error('tags')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>
</div>
