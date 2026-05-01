@extends('layouts.site')

@section('title', 'Cantores Hermanos Del Sr. Sto. Niño Choir')

@section('content')
    <section class="home-slideshow-card mb-10 w-full overflow-hidden rounded-3xl border border-[var(--color-border)] bg-[var(--color-surface)] shadow-sm" data-home-slideshow>
        <div class="relative">
            @if (!empty($slideshowError))
                <div class="p-6">
                    <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800" role="alert">
                        {{ $slideshowError }}
                    </div>
                </div>
            @endif
            @if (empty($slideshowError) && !empty($slideshowWarning))
                <div class="p-6">
                    <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-900" role="status" aria-live="polite">
                        {{ $slideshowWarning }}
                    </div>
                </div>
            @endif

            @php
                $slides = array_values(array_filter((array) ($slideshowImages ?? [])));
            @endphp

            @if (count($slides) === 0)
                <div class="home-slideshow-empty flex items-center justify-center bg-[var(--color-muted)]">
                    <div class="rounded-2xl bg-white/90 px-5 py-4 text-center text-sm font-semibold text-slate-900 ring-1 ring-[var(--color-border)]">
                        No slideshow images found in <span class="font-semibold">storage/app/public/slideshow</span>.
                    </div>
                </div>
            @else
                <div class="home-slideshow-stage" aria-label="Homepage slideshow" tabindex="0" data-home-slideshow-stage>
                    @foreach ($slides as $slide)
                        @php
                            $large = is_array($slide) ? (string) ($slide['large'] ?? '') : (string) $slide;
                            $srcset = is_array($slide) ? (string) ($slide['srcset'] ?? '') : '';
                            $sizes = is_array($slide) ? (string) ($slide['sizes'] ?? '') : '';
                        @endphp
                        <img
                            src="{{ $loop->first ? $large : '' }}"
                            data-src="{{ $large }}"
                            @if ($srcset !== '')
                                srcset="{{ $loop->first ? $srcset : '' }}"
                                data-srcset="{{ $srcset }}"
                            @endif
                            @if ($sizes !== '')
                                sizes="{{ $loop->first ? $sizes : '' }}"
                                data-sizes="{{ $sizes }}"
                            @endif
                            alt=""
                            class="home-slideshow-slide"
                            data-slide
                            data-active="{{ $loop->first ? '1' : '0' }}"
                            loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                            decoding="async"
                            fetchpriority="{{ $loop->first ? 'high' : 'auto' }}"
                        />
                    @endforeach
                    <div class="home-slideshow-overlay" aria-hidden="true"></div>
                    <div class="sr-only" data-home-slideshow-status aria-live="polite"></div>
                </div>

                @if (count($slides) > 1)
                    <div class="home-slideshow-controls" aria-label="Slideshow controls">
                        <button type="button" class="home-slideshow-btn" data-prev aria-label="Previous image" tabindex="0" disabled aria-disabled="true">
                            ‹
                        </button>
                        <button type="button" class="home-slideshow-btn" data-next aria-label="Next image" tabindex="0">
                            ›
                        </button>
                    </div>
                @endif
            @endif
        </div>
    </section>

    <section class="overflow-hidden rounded-3xl border border-[var(--color-border)] bg-[var(--color-surface)] shadow-sm">
        <div class="grid grid-cols-1 gap-10 px-6 py-10 md:grid-cols-2 md:px-10 md:py-14">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full bg-[var(--color-muted)] px-3 py-1 text-xs font-semibold text-slate-700 ring-1 ring-[var(--color-border)]">
                    <span class="h-2 w-2 rounded-full bg-[var(--color-accent)]"></span>
                    <span>Music Ministry • Community • Faith</span>
                </div>
                <h1 class="mt-5 text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">
                    Cantores Hermanos Del Sr. Sto. Niño Choir
                </h1>
                <p class="mt-4 max-w-prose text-base text-slate-700">
                    We are a community choir dedicated to uplifting worship and celebrating the mission of Señor Santo Niño through prayerful music, service, and fellowship.
                </p>

                <div class="mt-7 flex flex-col gap-3 sm:flex-row sm:items-center">
                    <a href="{{ route('site.contact') }}" class="inline-flex items-center justify-center rounded-xl bg-[var(--color-primary)] px-5 py-3 text-sm font-semibold text-[var(--color-on-primary)] shadow-sm hover:bg-[#001a4d] focus:bg-[#001a4d]">
                        Get in touch
                    </a>
                    <a href="{{ route('site.members') }}" class="inline-flex items-center justify-center rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] px-5 py-3 text-sm font-semibold text-slate-900 hover:bg-[var(--color-muted)]">
                        Meet the members
                    </a>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div class="rounded-2xl bg-[var(--color-muted)] p-4 ring-1 ring-[var(--color-border)]">
                        <div class="text-sm font-semibold text-[var(--color-primary)]">Mission</div>
                        <div class="mt-1 text-sm text-slate-700">The mission of the choir group dedicated to Señor Sto. Niño is to glorify God through sacred music and to lead the faithful in prayerful and uplifting worship. Rooted in devotion to the Holy Child Jesus, the group commits to serving the Church with humility, discipline, and unity.

<br><br>We aim to inspire deeper faith and devotion among the community by offering songs that reflect reverence, joy, and thanksgiving. Through our voices and service, we seek to become instruments of God’s love, spreading His message of hope and salvation.

<br><br>Guided by the example of Señor Sto. Niño, we dedicate our talents to strengthening liturgical celebrations, nurturing spiritual growth, and fostering a Christ-centered community.</div>
                    </div>
                    <div class="rounded-2xl bg-[var(--color-muted)] p-4 ring-1 ring-[var(--color-border)]">
<div class="text-sm font-semibold text-[var(--color-primary)]">Purpose</div>
                        <div class="mt-1 text-sm text-slate-700">The purpose of this community is to serve God through music by leading the
congregation in song during Mass and to contribute to the community through
special projects that promote its advocacies and goodwill.</div>
                        <br><div class="text-sm font-semibold text-[var(--color-primary)]">Objective</div>
<div class="mt-2 text-sm text-slate-700 leading-relaxed space-y-2">

    <p class="font-medium text-slate-800">
        The objectives of the community are:
    </p>

    <ol class="list-decimal list-outside pl-6 space-y-1">
        <li>To foster a deeper understanding of the Word of God through the gift of song;</li>
        <li>To engage members in activities that nurture their spiritual growth;</li>
        <li>To raise the musical standard and excellence among members;</li>
        <li>To create and perform original religious compositions;</li>
        <li>To promote musical appreciation within the group and the wider community;</li>
        <li>To initiate and participate in projects that serve the community in meaningful ways;</li>
        <li>To serve God and the community through music and dedicated ministry.</li>
    </ol>

</div>                    </div>
                </div>
            </div>

            <div class="relative">
                <div class="absolute inset-0 rounded-3xl bg-[var(--color-primary)] opacity-10"></div>
                <div class="relative h-full overflow-hidden rounded-3xl border border-[var(--color-border)] bg-gradient-to-br from-[var(--color-primary)] via-[#0a2f88] to-[#001a4d] p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-semibold text-[var(--color-on-primary)]">Featured</div>
                        <div class="inline-flex items-center rounded-full bg-[var(--color-accent)] px-3 py-1 text-xs font-semibold text-[var(--color-on-accent)]">
                            Upcoming
                        </div>
                    </div>

                    <div class="mt-6 space-y-4">
                        @foreach ($upcomingEvents as $event)
                            <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/15">
                                <div class="text-sm font-semibold text-white">{{ $event['title'] }}</div>
                                <div class="mt-1 text-xs text-white/80">{{ $event['date'] }} • {{ $event['location'] }}</div>
                                <div class="mt-2 text-sm text-white/90">{{ $event['details'] }}</div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('site.events') }}" class="inline-flex items-center justify-center rounded-xl bg-[var(--color-accent)] px-4 py-2.5 text-sm font-semibold text-[var(--color-on-accent)] hover:bg-[#f2c200] focus:bg-[#f2c200]">
                            View all events
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-10 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-3xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6 shadow-sm lg:col-span-2">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-slate-900">Choir History</h2>
                    <p class="mt-2 text-sm text-slate-700">
                        Rooted in devotion and community, the choir has grown through years of shared service—supporting liturgical celebrations, feast-day traditions, and parish outreach.
                    </p>
                </div>
                <a href="{{ route('site.history') }}" class="shrink-0 rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] px-4 py-2 text-sm font-semibold text-slate-900 hover:bg-[var(--color-muted)]">
                    Read more
                </a>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="rounded-2xl bg-[var(--color-muted)] p-5 ring-1 ring-[var(--color-border)]">
                    <div class="text-sm font-semibold text-[var(--color-primary)]">Worship-first</div>
                    <div class="mt-1 text-sm text-slate-700">Reverent repertoire that supports prayer and participation.</div>
                </div>
                <div class="rounded-2xl bg-[var(--color-muted)] p-5 ring-1 ring-[var(--color-border)]">
                    <div class="text-sm font-semibold text-[var(--color-primary)]">Community-centered</div>
                    <div class="mt-1 text-sm text-slate-700">A welcoming family for singers of different voice parts.</div>
                </div>
            </div>
            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="rounded-2xl bg-[var(--color-muted)] p-5 ring-1 ring-[var(--color-border)]">
                    <div class="text-sm font-semibold text-[var(--color-primary)]">Faith-driven commitment</div>
                    <div class="mt-1 text-sm text-slate-700">Serving consistently with dedication, humility, and devotion to God.</div>
                </div>
                <div class="rounded-2xl bg-[var(--color-muted)] p-5 ring-1 ring-[var(--color-border)]">
                    <div class="text-sm font-semibold text-[var(--color-primary)]">Spirit-led service</div>
                    <div class="mt-1 text-sm text-slate-700">Guided by prayer and openness to the Holy Spirit in all rehearsals and performances.</div>
                </div>
            </div>            
        </div>

        <div class="rounded-3xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6 shadow-sm">
            <h2 class="text-xl font-semibold text-slate-900">Performances</h2>
            <p class="mt-2 text-sm text-slate-700">Highlights from recent services and celebrations.</p>
            <div class="mt-5 space-y-3">
                <a href="{{ route('site.performances') }}" class="block rounded-2xl bg-[var(--color-muted)] p-4 ring-1 ring-[var(--color-border)] hover:bg-white">
                    <div class="text-sm font-semibold text-slate-900">Audio & video library</div>
                    <div class="mt-1 text-sm text-slate-700">Recordings, choir specials, and featured hymns.</div>
                </a>
                <a href="{{ route('site.gallery') }}" class="block rounded-2xl bg-[var(--color-muted)] p-4 ring-1 ring-[var(--color-border)] hover:bg-white">
                    <div class="text-sm font-semibold text-slate-900">Photo galleries</div>
                    <div class="mt-1 text-sm text-slate-700">Events, rehearsals, and community moments.</div>
                </a>
            </div>
        </div>
    </section>

    <section class="mt-10 rounded-3xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-900">Member Profiles</h2>
                <p class="mt-2 text-sm text-slate-700">Meet the voices that serve the community.</p>
            </div>
            <a href="{{ route('site.members') }}" class="inline-flex items-center justify-center rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] px-4 py-2.5 text-sm font-semibold text-slate-900 hover:bg-[var(--color-muted)]">
                View all members
            </a>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @forelse ($members as $member)
                <div class="group overflow-hidden rounded-2xl bg-[var(--color-muted)] ring-1 ring-[var(--color-border)] hover:bg-white">
                    <div class="p-4">
                        @php($rolesLabel = $member->role?->name)
                        @php($voicesLabel = $member->voicePart?->name)
                        <div class="flex items-center gap-3">
                            <div class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-full bg-white ring-2 ring-[var(--color-accent)] shadow-sm">
                                @if ($member->photo_path)
                                    <img src="{{ asset('storage/' . $member->photo_path) }}" alt="{{ $member->name }}" class="h-full w-full rounded-full object-cover" loading="lazy" />
                                @else
                                    <div class="flex h-full w-full items-center justify-center bg-[var(--color-primary)] text-lg font-semibold text-[var(--color-on-primary)]">
                                        {{ mb_strtoupper(mb_substr($member->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <div class="truncate text-sm font-semibold text-slate-900">{{ $member->name }}</div>
                                <div class="mt-1 truncate text-sm text-slate-700">
                                    {{ $rolesLabel ?: ($voicesLabel ?: 'Member') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl bg-[var(--color-muted)] p-6 ring-1 ring-[var(--color-border)] sm:col-span-2 lg:col-span-4">
                    <div class="text-sm font-semibold text-slate-900">No members published yet</div>
                    <div class="mt-1 text-sm text-slate-700">
                        Use the admin panel to add member profiles and they will appear here automatically.
                    </div>
                </div>
            @endforelse
        </div>
    </section>
@endsection
