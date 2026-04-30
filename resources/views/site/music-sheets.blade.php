@extends('layouts.site')

@section('title', 'Free Music Sheets · Cantores Hermanos')

@section('content')
    <div class="rounded-3xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6 shadow-sm sm:p-10" data-live-listing data-music-sheets-gallery>
        <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
            <div class="max-w-3xl">
                <h1 class="text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">Free Music Sheets</h1>
                <p class="mt-4 text-base text-slate-700">
                    Browse free music sheets by title or composer. Tap a card to view or download.
                </p>
            </div>

            <form method="GET" action="{{ route('site.music_sheets') }}" class="w-full max-w-md" data-live-form>
                <div class="grid grid-cols-1 gap-2 sm:grid-cols-[1fr_auto]">
                    <div>
                        <label for="q" class="sr-only">Search music sheets</label>
                        <input
                            id="q"
                            name="q"
                            type="search"
                            value="{{ $q ?? '' }}"
                            placeholder="Search title or composer…"
                            class="w-full rounded-xl border border-[var(--color-border)] bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0"
                            autocomplete="off"
                        />
                    </div>
                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-1">
                        <label for="per_page" class="sr-only">Records per page</label>
                        <select id="per_page" name="per_page" class="min-h-11 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-sm font-semibold text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0">
                            @foreach (($perPageOptions ?? [10, 20, 50]) as $opt)
                                <option value="{{ $opt }}" @selected((string) ($perPage ?? 10) === (string) $opt)>{{ $opt }}/page</option>
                            @endforeach
                            <option value="all" @selected((string) ($perPage ?? '') === 'all')>All</option>
                        </select>
                        <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[var(--color-primary)] px-4 py-3 text-sm font-semibold text-[var(--color-on-primary)] shadow-sm hover:bg-[#001a4d] focus:bg-[#001a4d]">
                            Search
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="mt-4 hidden items-center gap-3 text-sm text-slate-700" data-live-loading aria-live="polite">
            <div class="gallery-carousel-spinner" aria-label="Loading results"></div>
            <div>Loading…</div>
        </div>
        <div class="mt-4 hidden rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800" data-live-error role="alert"></div>

        <div data-live-results>
            @include('site.partials.music-sheets-results', ['sheets' => $sheets])
        </div>
    </div>

    <div id="music-sheet-preview" class="performance-modal" data-music-sheet-modal data-state="closed" hidden>
        <div class="absolute inset-0" data-music-sheet-close aria-hidden="true"></div>
        <div class="performance-modal-panel" role="dialog" aria-modal="true" aria-labelledby="music-sheet-modal-title" tabindex="-1">
            <div class="flex items-center justify-between gap-3 border-b border-[var(--color-border)] px-5 py-4">
                <div class="min-w-0">
                    <div id="music-sheet-modal-title" class="truncate text-base font-semibold text-slate-900"></div>
                    <div class="mt-1 text-sm text-slate-700" data-music-sheet-modal-meta></div>
                </div>
                <button type="button" class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-xl border border-[var(--color-border)] bg-white text-lg font-semibold text-slate-900 hover:bg-[var(--color-muted)]" data-music-sheet-close aria-label="Close dialog">
                    ×
                </button>
            </div>

            <div class="p-5">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="text-sm font-semibold text-slate-700">
                        <span data-music-sheet-modal-view-count>0</span> views
                        <span class="mx-1">•</span>
                        <span data-music-sheet-modal-download-count>0</span> downloads
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[var(--color-border)] bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 hover:bg-[var(--color-muted)]" data-music-sheet-prev aria-label="Previous music sheet" tabindex="0" disabled aria-disabled="true">
                            ‹
                        </button>
                        <button type="button" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[var(--color-border)] bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 hover:bg-[var(--color-muted)]" data-music-sheet-next aria-label="Next music sheet" tabindex="0">
                            ›
                        </button>
                        <a href="#" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[var(--color-primary)] px-4 py-2.5 text-sm font-semibold text-[var(--color-on-primary)] shadow-sm hover:bg-[#001a4d] focus:bg-[#001a4d]" data-music-sheet-download>
                            Download
                        </a>
                    </div>
                </div>

                <div class="mt-4 overflow-hidden rounded-2xl border border-[var(--color-border)] bg-[var(--color-muted)]">
                    <div class="relative h-[70vh] min-h-[18rem]">
                        <div class="absolute inset-0 flex flex-col items-center justify-center gap-3 p-4 text-center" data-music-sheet-loading>
                            <div class="gallery-carousel-spinner" aria-label="Loading preview"></div>
                            <div class="text-sm font-semibold text-slate-700">Loading preview…</div>
                            <div class="text-xs text-slate-600" data-music-sheet-loading-hint hidden>This may take longer than 2 seconds.</div>
                        </div>
                        <div class="absolute inset-0 hidden items-center justify-center p-4 text-center" data-music-sheet-error>
                            <div class="rounded-2xl bg-white/95 px-4 py-3 text-sm font-semibold text-slate-900">
                                Preview failed to load.
                                <div class="mt-2">
                                    <a href="#" class="font-semibold text-[var(--color-primary)] hover:underline" data-music-sheet-fallback>Open file</a>
                                </div>
                            </div>
                        </div>

                        <iframe class="absolute inset-0 hidden h-full w-full" title="Music sheet PDF preview" loading="lazy" data-music-sheet-pdf></iframe>
                        <img class="absolute inset-0 hidden h-full w-full object-contain" alt="" loading="lazy" decoding="async" data-music-sheet-image />
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
