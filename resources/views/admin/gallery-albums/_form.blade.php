<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
        <label for="album_name" class="block text-sm font-medium text-slate-800">Album name</label>
        <input id="album_name" name="album_name" type="text" value="{{ old('album_name', $album->album_name) }}" required class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0" />
        @error('album_name')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label for="title" class="block text-sm font-medium text-slate-800">Title</label>
        <input id="title" name="title" type="text" value="{{ old('title', $album->title) }}" class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0" />
        @error('title')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label for="description" class="block text-sm font-medium text-slate-800">Description</label>
        <textarea id="description" name="description" rows="6" class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0">{{ old('description', $album->description) }}</textarea>
        @error('description')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label for="tags" class="block text-sm font-medium text-slate-800">Tags (optional)</label>
        <input id="tags" name="tags" type="text" value="{{ old('tags', $album->tags) }}" placeholder="e.g. procession, outreach, rehearsal" class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0" />
        @error('tags')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div class="flex items-center gap-2 md:col-span-2">
        <input id="is_published" name="is_published" type="checkbox" value="1" {{ old('is_published', $album->is_published ?? true) ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-[var(--color-primary)] focus:ring-0" />
        <label for="is_published" class="text-sm font-medium text-slate-800">Show publicly</label>
        @error('is_published')
            <div class="text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div class="md:col-span-2">
        <div class="flex items-baseline justify-between gap-3">
            <label for="photos" class="block text-sm font-medium text-slate-800">Photos</label>
            <div class="text-xs text-slate-600">Bulk upload supported</div>
        </div>

        <div class="mt-2 rounded-2xl border border-dashed border-[var(--color-border)] bg-[var(--color-muted)] p-4" data-upload-dropzone>
            <div class="flex flex-col items-start gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="text-sm font-semibold text-slate-900">Drag & drop images here</div>
                    <div class="mt-1 text-sm text-slate-700">Or choose multiple files to upload.</div>
                </div>
                <label class="inline-flex min-h-11 cursor-pointer items-center justify-center rounded-xl border border-[var(--color-border)] bg-white px-4 py-3 text-sm font-semibold text-slate-900 hover:bg-[var(--color-muted)]">
                    <input id="photos" name="photos[]" type="file" accept="image/*" multiple class="sr-only" data-upload-input />
                    Choose files
                </label>
            </div>
        </div>

        @error('photos')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror
        @error('photos.*')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror

        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4" data-upload-preview></div>
    </div>
</div>
