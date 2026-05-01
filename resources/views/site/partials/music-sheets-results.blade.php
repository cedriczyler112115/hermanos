<div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5">
    @forelse (($sheets ?? collect()) as $sheet)
        @php
            $trackViewUrl = route('site.music_sheets.track_view', $sheet);
            $downloadIntentUrl = route('site.music_sheets.download_intent', $sheet);
            $fileUrl = route('site.music_sheets.file', $sheet);
            $downloadUrl = route('site.music_sheets.download', $sheet);

            $payload = [
                'id' => $sheet->id,
                'title' => $sheet->title,
                'composer' => $sheet->composer,
                'file_url' => $fileUrl,
                'is_pdf' => (bool) $sheet->is_pdf,
                'is_image' => (bool) $sheet->is_image,
                'view_count' => (int) ($sheet->view_count ?? 0),
                'download_count' => (int) ($sheet->download_count ?? 0),
                'track_view_url' => $trackViewUrl,
                'download_intent_url' => $downloadIntentUrl,
                'download_url' => $downloadUrl,
            ];
        @endphp

        <div class="group overflow-hidden rounded-3xl border border-[var(--color-border)] bg-white text-left shadow-sm transition-colors hover:bg-[var(--color-muted)]">
            <div class="relative aspect-[4/3] overflow-hidden bg-[var(--color-muted)]">
                @if ($sheet->is_image)
                    <img src="{{ $fileUrl }}" alt="{{ $sheet->title }}" class="h-full w-full object-cover" loading="lazy" decoding="async" />
                @else
                    <div class="absolute inset-0 bg-gradient-to-br from-[var(--color-primary)]/35 via-white/10 to-[var(--color-accent)]/35">
                        <div class="absolute inset-3 overflow-hidden rounded-2xl bg-white/80 ring-1 ring-[var(--color-border)]">
                            <canvas
                                class="block h-full w-full"
                                data-music-sheet-card-pdf
                                data-pdf-url="{{ $fileUrl }}"
                                data-pdf-pages="1"
                                role="img"
                                aria-label="PDF preview: {{ $sheet->title }}"
                            ></canvas>
                            <div class="absolute inset-0 flex items-center justify-center text-xs font-bold text-slate-800" data-music-sheet-card-pdf-fallback>
                                PDF
                            </div>
                        </div>
                    </div>
                @endif
                <button
                    type="button"
                    class="absolute inset-0"
                    data-music-sheet-open
                    data-music-sheet="{{ e(json_encode($payload, JSON_UNESCAPED_SLASHES)) }}"
                    aria-label="View {{ $sheet->title }}"
                ></button>
            </div>

            <div class="p-5">
                <div class="truncate text-sm font-semibold text-slate-900">{{ $sheet->title }}</div>
                <div class="mt-1 truncate text-xs text-slate-700">{{ $sheet->composer }}</div>
                <div class="mt-2 text-xs font-semibold text-slate-700">
                    <span data-music-sheet-view-count="{{ $sheet->id }}">{{ (int) ($sheet->view_count ?? 0) }}</span> views
                    <span class="mx-1">•</span>
                    <span data-music-sheet-download-count="{{ $sheet->id }}">{{ (int) ($sheet->download_count ?? 0) }}</span> downloads
                </div>

                <div class="mt-3 flex items-center gap-2">
                    <a
                        href="{{ $downloadUrl }}"
                        class="inline-flex min-h-10 flex-1 items-center justify-center rounded-xl bg-[var(--color-primary)] px-3 py-2 text-xs font-semibold text-[var(--color-on-primary)] hover:bg-[#001a4d] focus:bg-[#001a4d]"
                        data-music-sheet-download-trigger
                        data-music-sheet="{{ e(json_encode($payload, JSON_UNESCAPED_SLASHES)) }}"
                        aria-label="Download {{ $sheet->title }}"
                    >
                        Download
                    </a>
                </div>
            </div>
        </div>
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
