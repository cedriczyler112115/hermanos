@extends('layouts.admin')

@section('title', 'Edit Gallery Album · Admin')

@section('content')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Edit gallery album</h1>
            <p class="mt-1 text-sm text-slate-600">Update album details, upload more photos, and reorder existing images.</p>
        </div>
        <a href="{{ route('admin.gallery_albums.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-[var(--color-primary)] hover:bg-[var(--color-muted)]">Back</a>
    </div>

    <div class="mt-6 rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.gallery_albums.update', $album) }}" enctype="multipart/form-data" class="space-y-6" data-loading-message="Saving...">
            @csrf
            @method('PUT')

            @include('admin.gallery-albums._form', ['album' => $album])

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[var(--color-primary)] px-4 py-3 text-sm font-semibold text-[var(--color-on-primary)] shadow-sm hover:bg-[#001a4d] focus:bg-[#001a4d]">
                    Save changes
                </button>
            </div>
        </form>
    </div>

    <div class="mt-6 rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6 shadow-sm">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">Photos</h2>
                <p class="mt-1 text-sm text-slate-700">Drag photos to reorder. Changes save automatically.</p>
            </div>
            <div class="text-sm text-slate-700">
                <span class="font-semibold">{{ $album->photos->count() }}</span> total
            </div>
        </div>

        <div class="mt-4">
            <div class="text-sm text-slate-700" data-photo-reorder-status></div>
            <div
                class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4"
                data-photo-sort
                data-reorder-url="{{ route('admin.gallery_albums.photos.reorder', $album) }}"
            >
                @foreach ($album->photos as $photo)
                    <div
                        class="group relative overflow-hidden rounded-2xl border border-[var(--color-border)] bg-[var(--color-muted)] shadow-sm"
                        data-photo-id="{{ $photo->id }}"
                        draggable="true"
                    >
                        <img
                            src="{{ asset('storage/' . ($photo->photo_thumb_path ?: $photo->photo_path)) }}"
                            alt="{{ $album->album_name }}"
                            class="h-36 w-full object-cover"
                            loading="lazy"
                        />
                        <div class="absolute inset-0 bg-black/0 transition-colors group-hover:bg-black/10"></div>

                        <form method="POST" action="{{ route('admin.gallery_albums.photos.destroy', [$album, $photo]) }}" onsubmit="return confirm('Delete this photo?');" class="absolute right-3 top-3" data-loading-message="Deleting...">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-white/90 px-3 py-2 text-xs font-semibold text-red-800 ring-1 ring-[var(--color-border)] hover:bg-white">
                                Delete
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>

            @if ($album->photos->isEmpty())
                <div class="rounded-3xl border border-[var(--color-border)] bg-[var(--color-muted)] p-8 text-center text-slate-700">
                    No photos yet. Use the upload box above to add images.
                </div>
            @endif
        </div>
    </div>

    <form method="POST" action="{{ route('admin.gallery_albums.destroy', $album) }}" onsubmit="return confirm('Delete this album and all photos?');" class="mt-6 rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6 shadow-sm" data-loading-message="Deleting...">
        @csrf
        @method('DELETE')
        <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800 hover:bg-red-100">
            Delete album
        </button>
    </form>
@endsection

