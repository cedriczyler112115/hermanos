@extends('layouts.site')

@section('title', ($album['album_name'] ?? 'Gallery') . ' · Cantores Hermanos')

@section('content')
    @php
        $galleryPhotos = collect($photos ?? [])->values()->map(fn ($p) => [
            'full' => asset('storage/' . $p['photo_path']),
            'thumb' => asset('storage/' . ($p['photo_thumb_path'] ?: $p['photo_path'])),
        ])->all();
    @endphp

    <div class="rounded-3xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6 shadow-sm sm:p-10">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="max-w-3xl">
                <a href="{{ route('site.gallery') }}" class="inline-flex min-h-11 items-center gap-2 rounded-lg px-3 py-2.5 text-sm font-semibold text-[var(--color-primary)] hover:bg-[var(--color-muted)]">
                    <span aria-hidden="true">←</span>
                    <span>Back to albums</span>
                </a>

                <h1 class="mt-4 text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">
                    {{ $album['album_name'] ?? 'Gallery Album' }}
                </h1>

                @if (!empty($album['title']))
                    <p class="mt-3 text-base font-semibold text-slate-800">{{ $album['title'] }}</p>
                @endif

                @if (!empty($album['description']))
                    <p class="mt-3 text-base text-slate-700">{{ $album['description'] }}</p>
                @endif
            </div>
        </div>

        <div class="mt-8" data-gallery-carousel data-gallery-photos='@json($galleryPhotos)' data-gallery-title="{{ $album['album_name'] ?? 'Gallery Album' }}">
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-4">
                @forelse ($galleryPhotos as $index => $photo)
                    <button
                        type="button"
                        class="group relative overflow-hidden rounded-2xl border border-[var(--color-border)] bg-[var(--color-muted)] shadow-sm"
                        data-gallery-open
                        data-index="{{ $index }}"
                        aria-label="Open photo {{ $index + 1 }} of {{ count($galleryPhotos) }}"
                    >
                        <img
                            src="{{ $photo['thumb'] }}"
                            alt="{{ $album['album_name'] ?? 'Photo' }}"
                            class="h-full w-full object-cover"
                            loading="lazy"
                            decoding="async"
                        />
                        <div class="pointer-events-none absolute inset-0 bg-black/0 transition-colors group-hover:bg-black/10"></div>
                    </button>
            @empty
                <div class="col-span-2 rounded-3xl border border-[var(--color-border)] bg-[var(--color-muted)] p-8 text-center text-slate-700 sm:col-span-3 lg:col-span-4">
                    No photos in this album yet.
                </div>
            @endforelse
            </div>
        </div>
    </div>
@endsection
