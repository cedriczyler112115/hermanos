@extends('layouts.site')

@section('title', 'Upcoming Events · Cantores Hermanos')

@section('content')
    <div class="rounded-3xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6 shadow-sm sm:p-10" data-live-listing data-events-view-root data-view="slideshow">
        <div class="flex flex-col gap-6 md:flex-row md:items-start md:justify-between">
            <div class="max-w-3xl md:pt-1">
                <h1 class="text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">Events</h1>
                <p class="mt-4 text-base text-slate-700">
                    Stay updated with choir services, rehearsals, feast celebrations, and community events.
                </p>
            </div>

            <div class="w-full max-w-md">
                <form method="GET" action="{{ route('site.events') }}" class="w-full" data-live-form>
                <div class="grid grid-cols-1 gap-2 sm:grid-cols-[1fr_auto]">
                    <div>
                        <label for="q" class="sr-only">Search events</label>
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

                <div class="mt-3 flex items-center justify-end">
                    <div class="inline-flex rounded-xl border border-[var(--color-border)] bg-white p-1 shadow-sm" role="group" aria-label="Event view mode">
                        <button type="button" class="events-view-toggle-btn rounded-lg px-4 py-2 text-sm font-semibold" data-events-view="slideshow" aria-pressed="true">
                            Slideshow
                        </button>
                        <button type="button" class="events-view-toggle-btn rounded-lg px-4 py-2 text-sm font-semibold" data-events-view="cards" aria-pressed="false">
                            Cards
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 hidden items-center gap-3 text-sm text-slate-700" data-live-loading aria-live="polite">
            <div class="gallery-carousel-spinner" aria-label="Loading results"></div>
            <div>Loading…</div>
        </div>
        <div class="mt-4 hidden rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800" data-live-error role="alert"></div>

        <div data-live-results>
            @include('site.partials.events-results', ['events' => $events])
        </div>
    </div>

    <div id="event-modal" class="event-modal" data-event-modal data-state="closed" hidden>
        <div class="absolute inset-0" data-event-close aria-hidden="true"></div>
        <div class="event-modal-panel" role="dialog" aria-modal="true" aria-labelledby="event-modal-title" tabindex="-1">
            <div class="flex items-center justify-between gap-3 border-b border-[var(--color-border)] px-5 py-4">
                <div class="min-w-0">
                    <div id="event-modal-title" class="truncate text-base font-semibold text-slate-900"></div>
                    <div class="mt-1 flex flex-col gap-1 text-sm text-slate-700 sm:flex-row sm:items-center sm:gap-3">
                        <div data-event-modal-schedule></div>
                        <div class="hidden h-1 w-1 rounded-full bg-slate-400 sm:block"></div>
                        <div data-event-modal-location></div>
                    </div>
                </div>
                <button type="button" class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-xl border border-[var(--color-border)] bg-white text-lg font-semibold text-slate-900 hover:bg-[var(--color-muted)]" data-event-close aria-label="Close dialog">
                    ×
                </button>
            </div>

            <div class="p-5">
                    <div class="overflow-hidden rounded-2xl border border-[var(--color-border)] bg-[var(--color-muted)]">
                        <div class="relative aspect-[16/9]">
                            <img class="absolute inset-0 h-full w-full object-cover" data-event-modal-thumb alt="" loading="lazy" decoding="async" />
                            <img class="absolute inset-0 h-full w-full object-cover opacity-0 transition-opacity duration-200" data-event-modal-full alt="" loading="lazy" decoding="async" />
                            <div class="absolute inset-0 bg-slate-950/20"></div>
                        </div>
                    </div>

                    <div class="mt-5">
                        <h2 class="text-lg font-semibold text-slate-900">About</h2>
                        <p class="mt-2 whitespace-pre-line text-sm text-slate-800" data-event-modal-about></p>
                    </div>
            </div>
        </div>
    </div>
@endsection
