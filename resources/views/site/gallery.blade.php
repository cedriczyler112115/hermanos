@extends('layouts.site')

@section('title', 'Photo Galleries · Cantores Hermanos')

@section('content')
    <div class="rounded-3xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6 shadow-sm sm:p-10" data-live-listing>
        <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
            <div class="max-w-3xl">
                <h1 class="text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">Photo Galleries</h1>
                <p class="mt-4 text-base text-slate-700">
                    Moments from services, rehearsals, and community celebrations. Add official images to match the choir’s royal blue and gold identity.
                </p>
            </div>

            <form method="GET" action="{{ route('site.gallery') }}" class="w-full max-w-md" data-live-form>
                <div class="grid grid-cols-1 gap-2 sm:grid-cols-[1fr_auto]">
                    <div>
                        <label for="q" class="sr-only">Search albums</label>
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
            @include('site.partials.gallery-results', ['albums' => $albums])
        </div>
    </div>
@endsection
