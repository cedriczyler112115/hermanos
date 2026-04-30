@extends('layouts.site')

@section('title', 'Audio & Video Performances · Cantores Hermanos')

@section('content')
    <div class="rounded-3xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6 shadow-sm sm:p-10" data-live-listing>
        <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
            <div class="max-w-3xl">
                <h1 class="text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">Audio & Video Performances</h1>
                <p class="mt-4 text-base text-slate-700">
                    A curated collection of choir performances. Add official recordings to keep the site updated and inspiring.
                </p>
            </div>

            <form method="GET" action="{{ route('site.performances') }}" class="w-full max-w-md" data-live-form>
                <div class="grid grid-cols-1 gap-2 sm:grid-cols-[1fr_auto]">
                    <div>
                        <label for="q" class="sr-only">Search performances</label>
                        <input
                            id="q"
                            name="q"
                            type="search"
                            value="{{ $q ?? '' }}"
                            placeholder="Search by title, description, or tags…"
                            class="w-full rounded-xl border border-[var(--color-border)] bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0"
                            autocomplete="off"
                        />
                    </div>
                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-1">
                        <label for="per_page" class="sr-only">Records per page</label>
                        <select id="per_page" name="per_page" class="min-h-11 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-sm font-semibold text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0">
                            @foreach (($perPageOptions ?? [12, 24, 48]) as $opt)
                                <option value="{{ $opt }}" @selected((int) ($perPage ?? 12) === (int) $opt)>{{ $opt }}/page</option>
                            @endforeach
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
            @include('site.partials.performances-results', ['performances' => $performances])
        </div>
    </div>

    <div id="performance-modal" class="performance-modal" data-performance-modal data-state="closed" hidden>
        <div class="absolute inset-0" data-performance-close aria-hidden="true"></div>
        <div class="performance-modal-panel" role="dialog" aria-modal="true" aria-labelledby="performance-modal-title" tabindex="-1">
            <div class="flex items-center justify-between gap-3 border-b border-[var(--color-border)] px-5 py-4">
                <div class="min-w-0">
                    <div id="performance-modal-title" class="truncate text-base font-semibold text-slate-900"></div>
                    <div class="mt-1 text-sm text-slate-700" data-performance-modal-meta></div>
                </div>
                <button type="button" class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-xl border border-[var(--color-border)] bg-white text-lg font-semibold text-slate-900 hover:bg-[var(--color-muted)]" data-performance-close aria-label="Close dialog">
                    ×
                </button>
            </div>

            <div class="p-5">
                <div class="overflow-hidden rounded-2xl border border-[var(--color-border)] bg-[var(--color-muted)]">
                    <div class="relative aspect-video">
                        <div class="absolute inset-0 flex items-center justify-center" data-performance-modal-loading>
                            <div class="gallery-carousel-spinner" aria-label="Loading video"></div>
                        </div>
                        <div class="absolute inset-0 hidden items-center justify-center p-4 text-center" data-performance-modal-error>
                            <div class="rounded-2xl bg-white/95 px-4 py-3 text-sm font-semibold text-slate-900">Video failed to load.</div>
                        </div>
                        <iframe
                            class="absolute inset-0 h-full w-full"
                            title="YouTube video player"
                            loading="lazy"
                            referrerpolicy="strict-origin-when-cross-origin"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen
                            data-performance-modal-iframe
                        ></iframe>
                    </div>
                </div>

                <div class="mt-5">
                    <h2 class="text-lg font-semibold text-slate-900">Description</h2>
                    <p class="mt-2 whitespace-pre-line text-sm text-slate-800" data-performance-modal-description></p>
                </div>
            </div>
        </div>
    </div>
@endsection
