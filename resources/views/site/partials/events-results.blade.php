@php
    $items = ($events ?? collect());
    $count = is_object($items) && method_exists($items, 'count') ? (int) $items->count() : (int) count((array) $items);
@endphp

<section class="event-slideshow-wrap" data-events-slideshow aria-label="Events slideshow">
    @if ($count === 0)
        <div class="rounded-3xl border border-[var(--color-border)] bg-[var(--color-muted)] p-8 text-center text-slate-700">
            No events found.
        </div>
    @else
        <div class="event-slideshow" data-slideshow-root tabindex="0">
            @foreach ($events as $event)
                @php
                    $photoThumb = !empty($event['photo_thumb_path']) ? asset('storage/' . $event['photo_thumb_path']) : null;
                    $photoFull = !empty($event['photo_path']) ? asset('storage/' . $event['photo_path']) : null;
                @endphp
                <div class="event-slide" data-slide data-index="{{ $loop->index }}" data-active="{{ $loop->first ? '1' : '0' }}" aria-hidden="{{ $loop->first ? 'false' : 'true' }}">
                    @if ($photoThumb)
                        <img class="event-slide-thumb" src="{{ $photoThumb }}" alt="" loading="lazy" decoding="async" />
                    @endif
                    @if ($photoFull)
                        <img class="event-slide-full" data-src="{{ $photoFull }}" alt="" loading="lazy" decoding="async" />
                    @endif
                    <div class="event-slide-overlay"></div>
                    <div class="event-slide-gradient"></div>

                    <div class="event-slide-inner">
                        <div class="event-slide-top">
                            @if (!empty($event['event_type']))
                                <span class="event-slide-badge">{{ $event['event_type'] }}</span>
                            @endif
                            <div class="event-slide-counter" data-slideshow-counter aria-live="polite"></div>
                        </div>

                        <div class="event-slide-panel">
                            <h2 class="text-2xl font-semibold text-slate-950 sm:text-3xl">{{ $event['title'] }}</h2>

                            <div class="mt-3 grid grid-cols-1 gap-2 text-sm text-slate-900 sm:grid-cols-2">
                                <div class="flex items-start gap-2">
                                    <span class="mt-2 h-2 w-2 rounded-full bg-[var(--color-accent)]"></span>
                                    <div>
                                        <div class="text-xs font-semibold text-slate-700">Schedule</div>
                                        <div class="font-semibold">{{ !empty($event['schedule']) ? $event['schedule'] : '—' }}</div>
                                    </div>
                                </div>
                                <div class="flex items-start gap-2">
                                    <span class="mt-2 h-2 w-2 rounded-full bg-[var(--color-primary)]"></span>
                                    <div>
                                        <div class="text-xs font-semibold text-slate-700">Location</div>
                                        <div class="font-semibold">{{ !empty($event['location']) ? $event['location'] : '—' }}</div>
                                    </div>
                                </div>
                            </div>

                            @if (!empty($event['tags']))
                                <div class="mt-3 text-xs font-semibold text-slate-700">
                                    Tags:
                                    <span class="font-semibold text-slate-900">{{ $event['tags'] }}</span>
                                </div>
                            @endif

                            <div class="mt-4 max-h-52 overflow-auto pr-1 text-sm text-slate-900 sm:max-h-60">
                                {{ !empty($event['about']) ? $event['about'] : 'No description provided.' }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="event-slideshow-controls">
                <button type="button" class="event-slideshow-btn" data-slideshow-prev aria-label="Previous event">
                    ‹
                </button>
                <button type="button" class="event-slideshow-btn" data-slideshow-next aria-label="Next event">
                    ›
                </button>
            </div>
        </div>
    @endif
</section>

<section class="event-cards-wrap" data-events-cards>
    <div class="mt-8 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse (($events ?? collect()) as $event)
            @php
                $eventForModal = $event;
                $eventForModal['photo_url'] = !empty($event['photo_path']) ? asset('storage/' . $event['photo_path']) : null;
                $eventForModal['photo_thumb_url'] = !empty($event['photo_thumb_path']) ? asset('storage/' . $event['photo_thumb_path']) : null;
            @endphp
            <button
                type="button"
                class="event-card group w-full text-left"
                data-event-open
                data-event='@json($eventForModal)'
                aria-haspopup="dialog"
                aria-controls="event-modal"
                aria-expanded="false"
            >
                @if (!empty($event['photo_thumb_path']))
                    <img
                        class="event-card-thumb"
                        src="{{ asset('storage/' . $event['photo_thumb_path']) }}"
                        alt=""
                        loading="lazy"
                        decoding="async"
                    />
                @else
                    <div class="absolute inset-0 bg-gradient-to-br from-[var(--color-primary)]/30 via-white/10 to-[var(--color-accent)]/30"></div>
                @endif

                @if (!empty($event['photo_path']))
                    <img
                        class="event-card-full"
                        data-event-src="{{ asset('storage/' . $event['photo_path']) }}"
                        alt=""
                        loading="lazy"
                        decoding="async"
                    />
                @endif

                <div class="absolute inset-0 bg-slate-950/35"></div>
                <div class="absolute inset-x-0 bottom-0 h-[40%] bg-gradient-to-t from-white via-white/95 to-transparent"></div>

                <div class="relative flex h-full flex-col justify-between p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="text-sm font-semibold text-white/95">Cantores Hermanos</div>
                        </div>
                        @if (!empty($event['event_type']))
                            <span class="shrink-0 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-slate-900 ring-1 ring-white/60">
                                {{ $event['event_type'] }}
                            </span>
                        @endif
                    </div>

                    <div class="mt-auto">
                        <div class="event-card-content rounded-2xl p-4">
                            <div class="text-lg font-semibold text-slate-950">{{ $event['title'] }}</div>

                            <div class="mt-2 flex flex-col gap-1 text-sm text-slate-900">
                                @if (!empty($event['schedule']))
                                    <div class="flex items-center gap-2">
                                        <span class="h-2 w-2 rounded-full bg-[var(--color-accent)]"></span>
                                        <span class="truncate">{{ $event['schedule'] }}</span>
                                    </div>
                                @endif
                                @if (!empty($event['location']))
                                    <div class="flex items-center gap-2">
                                        <span class="h-2 w-2 rounded-full bg-[var(--color-primary)]"></span>
                                        <span class="truncate">{{ $event['location'] }}</span>
                                    </div>
                                @endif
                            </div>

                            @if (!empty($event['about']))
                                <p class="mt-3 text-sm text-slate-900">
                                    {{ \Illuminate\Support\Str::words((string) $event['about'], 18, '…') }}
                                </p>
                            @endif

                            <div class="mt-4 inline-flex min-h-11 items-center gap-2 rounded-xl bg-[var(--color-primary)] px-4 py-3 text-sm font-semibold text-white shadow-sm">
                                <span>View details</span>
                                <span aria-hidden="true">→</span>
                            </div>
                        </div>
                    </div>
                </div>
            </button>
        @empty
            <div class="rounded-3xl border border-[var(--color-border)] bg-[var(--color-muted)] p-8 text-center text-slate-700 sm:col-span-2 lg:col-span-3">
                No events found.
            </div>
        @endforelse
    </div>
</section>

<div class="mt-8 border-t border-[var(--color-border)] pt-6">
    {{ $events->onEachSide(2)->links('pagination.public') }}
</div>
