@extends('layouts.admin')

@section('title', 'Performances · Admin')

@section('content')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Performances</h1>
            <p class="mt-1 text-sm text-slate-600">Manage audio & video performance entries for the public Performances page.</p>
        </div>
        <a href="{{ route('admin.performances.create') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[var(--color-primary)] px-4 py-3 text-sm font-semibold text-[var(--color-on-primary)] shadow-sm hover:bg-[#001a4d] focus:bg-[#001a4d]">
            Add performance
        </a>
    </div>

    <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="text-sm text-slate-700">
            @if (!empty($isAll))
                Showing <span class="font-semibold">{{ $performances->count() }}</span> performances
            @else
                Showing <span class="font-semibold">{{ $performances->count() }}</span> of <span class="font-semibold">{{ $performances->total() }}</span>
            @endif
        </div>

        <form method="GET" action="{{ route('admin.performances.index') }}" class="flex items-center gap-2">
            <label for="per_page" class="text-sm font-medium text-slate-800">Per page</label>
            <select
                id="per_page"
                name="per_page"
                class="rounded-xl border border-[var(--color-border)] bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0"
                onchange="this.form.submit()"
            >
                @foreach ($perPageOptions as $option)
                    <option value="{{ $option }}" {{ (string) $perPage === (string) $option ? 'selected' : '' }}>
                        {{ $option === 'all' ? 'All' : $option }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @forelse ($performances as $performance)
            <div class="overflow-hidden rounded-3xl border border-[var(--color-border)] bg-[var(--color-surface)] shadow-sm">
                <div class="relative flex aspect-video w-full items-center justify-center bg-gradient-to-br from-[var(--color-primary)]/20 via-white/10 to-[var(--color-accent)]/20">
                    @if ($performance->youtube_embed_url)
                        <iframe
                            class="absolute inset-0 h-full w-full"
                            src="{{ $performance->youtube_embed_url }}"
                            title="{{ $performance->title }}"
                            loading="lazy"
                            referrerpolicy="strict-origin-when-cross-origin"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen
                        ></iframe>
                    @else
                        <div class="flex flex-col items-center gap-3 text-center">
                            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-[var(--color-primary)] text-[var(--color-on-primary)] shadow-sm">
                                <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path d="M4 4.75A1.75 1.75 0 0 1 6.625 3.2l10 5.25a1.75 1.75 0 0 1 0 3.1l-10 5.25A1.75 1.75 0 0 1 4 15.25V4.75Z" />
                                </svg>
                            </div>
                            <div class="max-w-sm text-sm font-semibold text-slate-800">
                                Invalid YouTube URL
                            </div>
                        </div>
                    @endif
                </div>

                <div class="p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="truncate text-base font-semibold text-slate-900">{{ $performance->title }}</div>
                            <div class="mt-1 text-sm text-slate-700">
                                {{ \Illuminate\Support\Str::words($performance->description, 24, '…') }}
                            </div>
                        </div>

                        <div class="shrink-0">
                            <a href="{{ route('admin.performances.edit', $performance) }}" class="inline-flex min-h-11 items-center rounded-lg px-3 py-2.5 text-sm font-semibold text-[var(--color-primary)] hover:bg-[var(--color-muted)]">
                                Edit
                            </a>
                        </div>
                    </div>

                    <div class="mt-4 border-t border-[var(--color-border)] pt-4">
                        <form method="POST" action="{{ route('admin.performances.destroy', $performance) }}" onsubmit="return confirm('Delete this performance?');" data-loading-message="Deleting...">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800 hover:bg-red-100">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-3xl border border-[var(--color-border)] bg-[var(--color-muted)] p-8 text-center text-slate-700 lg:col-span-2">
                No performances yet. Click “Add performance” to create the first one.
            </div>
        @endforelse
    </div>

    @if (empty($isAll))
        <div class="mt-8 border-t border-[var(--color-border)] pt-6">
            {{ $performances->links() }}
        </div>
    @endif
@endsection
