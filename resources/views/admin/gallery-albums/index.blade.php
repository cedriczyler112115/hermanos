@extends('layouts.admin')

@section('title', 'Gallery Albums · Admin')

@section('content')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Gallery albums</h1>
            <p class="mt-1 text-sm text-slate-600">Create albums and upload multiple photos for the public Gallery.</p>
        </div>

        <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
            <form method="GET" action="{{ route('admin.gallery_albums.index') }}" class="flex w-full gap-2 sm:w-auto">
                <label for="q" class="sr-only">Search</label>
                <input
                    id="q"
                    name="q"
                    type="search"
                    value="{{ $q ?? '' }}"
                    placeholder="Search albums…"
                    class="w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-sm text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0 sm:w-64"
                />
                <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] px-4 py-3 text-sm font-semibold text-slate-900 hover:bg-[var(--color-muted)]">
                    Search
                </button>
            </form>

            <a href="{{ route('admin.gallery_albums.create') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[var(--color-primary)] px-4 py-3 text-sm font-semibold text-[var(--color-on-primary)] shadow-sm hover:bg-[#001a4d] focus:bg-[#001a4d]">
                Add
            </a>
        </div>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[var(--color-border)]">
                <thead class="bg-[var(--color-muted)]">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Album</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Photos</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Published</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-border)]">
                    @forelse ($albums as $album)
                        <tr class="hover:bg-[var(--color-muted)]">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-14 overflow-hidden rounded-lg bg-[var(--color-muted)] ring-1 ring-[var(--color-border)]">
                                        @if ($album->cover_photo_thumb_path)
                                            <img src="{{ asset('storage/' . $album->cover_photo_thumb_path) }}" alt="{{ $album->album_name }}" class="h-full w-full object-cover" />
                                        @else
                                            <div class="h-full w-full bg-gradient-to-br from-[var(--color-primary)]/30 via-white/10 to-[var(--color-accent)]/30"></div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="truncate text-sm font-semibold text-slate-900">{{ $album->album_name }}</div>
                                        <div class="truncate text-xs text-slate-600">{{ $album->title ?: '—' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-800">{{ $album->photos_count }}</td>
                            <td class="px-4 py-3 text-sm">
                                @if ($album->is_published)
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-800 ring-1 ring-emerald-200">Yes</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">No</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.gallery_albums.edit', $album) }}" class="inline-flex min-h-11 items-center rounded-lg px-3 py-2.5 text-sm font-medium text-[var(--color-primary)] hover:bg-[var(--color-muted)]">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.gallery_albums.destroy', $album) }}" onsubmit="return confirm('Delete this album and all photos?');" data-loading-message="Deleting...">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex min-h-11 items-center rounded-lg px-3 py-2.5 text-sm font-medium text-red-700 hover:bg-red-50">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-sm text-slate-600">
                                No albums yet. Click “Add album” to create the first one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-[var(--color-border)] bg-[var(--color-surface)] px-4 py-3">
            {{ $albums->links() }}
        </div>
    </div>
@endsection

