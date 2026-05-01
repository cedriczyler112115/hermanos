<div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5">
    @forelse (($sheets ?? collect()) as $sheet)
        @php
            $baseUrl = request()->getBaseUrl();
            $baseUrl = is_string($baseUrl) ? rtrim($baseUrl, '/') : '';

            $trackViewPath = route('site.music_sheets.track_view', $sheet, false);
            $downloadIntentPath = route('site.music_sheets.download_intent', $sheet, false);
            $filePath = route('site.music_sheets.file', $sheet, false);
            $downloadPath = route('site.music_sheets.download', $sheet, false);

            $payload = [
                'id' => $sheet->id,
                'title' => $sheet->title,
                'composer' => $sheet->composer,
                'file_url' => $baseUrl.$filePath,
                'is_pdf' => (bool) $sheet->is_pdf,
                'is_image' => (bool) $sheet->is_image,
                'view_count' => (int) ($sheet->view_count ?? 0),
                'download_count' => (int) ($sheet->download_count ?? 0),
                'track_view_url' => $baseUrl.$trackViewPath,
                'download_intent_url' => $baseUrl.$downloadIntentPath,
                'download_url' => $baseUrl.$downloadPath,
            ];
        @endphp

        <button
            type="button"
            class="group overflow-hidden rounded-3xl border border-[var(--color-border)] bg-white text-left shadow-sm transition-colors hover:bg-[var(--color-muted)] focus:bg-[var(--color-muted)]"
            data-music-sheet-open
            data-music-sheet="{{ e(json_encode($payload, JSON_UNESCAPED_SLASHES)) }}"
            aria-label="Preview {{ $sheet->title }}"
        >
            <div class="relative aspect-[4/3] overflow-hidden bg-[var(--color-muted)]">
                @if ($sheet->is_image)
                    <img src="{{ $baseUrl.$filePath }}" alt="{{ $sheet->title }}" class="h-full w-full object-cover" loading="lazy" decoding="async" />
                @else
                    <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-[var(--color-primary)]/35 via-white/10 to-[var(--color-accent)]/35">
                        <div class="rounded-2xl bg-white/90 px-4 py-2 text-sm font-extrabold text-slate-900 ring-1 ring-[var(--color-border)]">
                            PDF
                        </div>
                    </div>
                @endif
            </div>

            <div class="p-5">
                <div class="truncate text-sm font-semibold text-slate-900">{{ $sheet->title }}</div>
                <div class="mt-1 truncate text-xs text-slate-700">{{ $sheet->composer }}</div>
                <div class="mt-2 text-xs font-semibold text-slate-700">
                    <span data-music-sheet-view-count="{{ $sheet->id }}">{{ (int) ($sheet->view_count ?? 0) }}</span> views
                    <span class="mx-1">•</span>
                    <span data-music-sheet-download-count="{{ $sheet->id }}">{{ (int) ($sheet->download_count ?? 0) }}</span> downloads
                </div>
                <div class="mt-3 inline-flex items-center rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] px-3 py-2 text-xs font-semibold text-slate-900 group-hover:bg-white">
                    View / Download
                </div>
            </div>
        </button>
    @empty
        <div class="rounded-3xl border border-[var(--color-border)] bg-[var(--color-muted)] p-8 text-center text-slate-700 sm:col-span-2 md:col-span-3 lg:col-span-5">
            No music sheets found.
        </div>
    @endforelse
</div>

@if ($sheets->hasPages())
    <div class="mt-8 border-t border-[var(--color-border)] pt-6">
        {{ $sheets->onEachSide(2)->links('pagination.public') }}
    </div>
@endif
