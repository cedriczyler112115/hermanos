<div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
    @forelse (($performances ?? collect()) as $performance)
        <button
            type="button"
            class="performance-card overflow-hidden rounded-3xl border border-[var(--color-border)] bg-[var(--color-muted)] text-left shadow-sm"
            data-performance-open
            data-performance='@json($performance)'
            aria-haspopup="dialog"
            aria-controls="performance-modal"
            aria-expanded="false"
        >
            <div class="relative flex aspect-video w-full items-center justify-center bg-gradient-to-br from-[var(--color-primary)]/20 via-white/10 to-[var(--color-accent)]/20">
                @if (!empty($performance['youtube_thumb_url']))
                    <img
                        src="{{ $performance['youtube_thumb_url'] }}"
                        alt="{{ $performance['title'] }}"
                        class="absolute inset-0 h-full w-full object-cover"
                        loading="lazy"
                        decoding="async"
                    />
                    <div class="absolute inset-0 bg-slate-950/25"></div>
                @endif

                <div class="relative z-10 flex h-14 w-14 items-center justify-center rounded-2xl bg-[var(--color-primary)] text-[var(--color-on-primary)] shadow-sm ring-1 ring-white/30">
                    <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M4 4.75A1.75 1.75 0 0 1 6.625 3.2l10 5.25a1.75 1.75 0 0 1 0 3.1l-10 5.25A1.75 1.75 0 0 1 4 15.25V4.75Z" />
                    </svg>
                    <span class="sr-only">Play</span>
                </div>
            </div>
            <div class="p-5">
                <div class="text-base font-semibold text-slate-900">{{ $performance['title'] }}</div>
                <div class="mt-2 text-sm text-slate-700">{{ \Illuminate\Support\Str::limit((string) $performance['description'], 160, '…') }}</div>
            </div>
        </button>
    @empty
        <div class="rounded-3xl border border-[var(--color-border)] bg-white p-6 shadow-sm lg:col-span-2">
            <h2 class="text-lg font-semibold text-slate-900">No performances found</h2>
            <p class="mt-2 text-sm text-slate-700">Try adjusting your search.</p>
        </div>
    @endforelse
</div>

<div class="mt-8 border-t border-[var(--color-border)] pt-6">
    {{ $performances->onEachSide(2)->links('pagination.public') }}
</div>

