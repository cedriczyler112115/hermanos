<div class="mt-8 columns-1 gap-4 sm:columns-2 lg:columns-3">
    @forelse (($albums ?? collect()) as $album)
        <a href="{{ route('site.gallery.album', $album['id']) }}" class="group mb-4 block break-inside-avoid overflow-hidden rounded-3xl border border-[var(--color-border)] bg-white shadow-sm transition-colors hover:bg-[var(--color-muted)] focus:bg-[var(--color-muted)]">
            <div class="relative aspect-[4/3] overflow-hidden bg-[var(--color-muted)]">
                @php
                    $count = (int) ($album['photos_count'] ?? 0);
                    $stack = array_values(array_filter((array) ($album['preview_thumb_paths'] ?? [])));

                    if (empty($stack) && !empty($album['cover_photo_thumb_path'])) {
                        $stack = [$album['cover_photo_thumb_path']];
                    }
                @endphp

                @if (!empty($stack))
                    <div class="album-stack" aria-hidden="true">
                        @if (!empty($stack[2]))
                            <img src="{{ asset('storage/' . $stack[2]) }}" alt="" class="album-stack-item album-stack-back" loading="lazy" />
                        @endif
                        @if (!empty($stack[1]))
                            <img src="{{ asset('storage/' . $stack[1]) }}" alt="" class="album-stack-item album-stack-mid" loading="lazy" />
                        @endif
                        <img src="{{ asset('storage/' . $stack[0]) }}" alt="{{ $album['album_name'] }}" class="album-stack-item album-stack-front" loading="lazy" />
                    </div>
                @else
                    <div class="absolute inset-0 bg-gradient-to-br from-[var(--color-primary)]/35 via-white/10 to-[var(--color-accent)]/35"></div>
                @endif

                @if ($count > 1)
                    <div class="absolute left-4 top-4 inline-flex items-center gap-2 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-slate-900 ring-1 ring-[var(--color-border)]">
                        <span class="h-2 w-2 rounded-full bg-[var(--color-accent)]"></span>
                        <span>Multiple photos</span>
                    </div>
                @endif

                <div class="absolute bottom-4 left-4 right-4">
                    <div class="inline-flex w-full items-center justify-between gap-3 rounded-2xl bg-white/90 px-4 py-3 ring-1 ring-[var(--color-border)]">
                        <div class="min-w-0">
                            <div class="truncate text-sm font-semibold text-slate-900">{{ $album['album_name'] }}</div>
                            @if (!empty($album['title']))
                                <div class="truncate text-xs text-slate-700">{{ $album['title'] }}</div>
                            @endif
                        </div>
                        <div class="shrink-0 text-xs font-semibold text-slate-800">
                            {{ (int) ($album['photos_count'] ?? 0) }} photos
                        </div>
                    </div>
                </div>
            </div>

            @if (!empty($album['description']))
                <div class="p-5">
                    <p class="text-sm text-slate-700">
                        {{ \Illuminate\Support\Str::words((string) $album['description'], 18, '…') }}
                    </p>
                </div>
            @endif
        </a>
    @empty
        <div class="rounded-3xl border border-[var(--color-border)] bg-[var(--color-muted)] p-8 text-center text-slate-700">
            No albums found.
        </div>
    @endforelse
</div>

<div class="mt-8 border-t border-[var(--color-border)] pt-6">
    {{ $albums->onEachSide(2)->links('pagination.public') }}
</div>

