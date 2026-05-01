@extends('layouts.admin')

@section('title', 'Slideshow · Admin')

@section('content')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Slideshow</h1>
            <p class="mt-1 text-sm text-slate-600">
                Upload homepage slideshow images. Images are optimized and resized for the homepage slideshow.
            </p>
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3" data-admin-slideshow>
        <div class="overflow-hidden rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] shadow-sm lg:col-span-1">
            <div class="border-b border-[var(--color-border)] bg-[var(--color-muted)] px-4 py-3 text-sm font-semibold text-slate-900">Upload images</div>
            <div class="p-4">
                <form method="POST" action="{{ route('admin.slideshow.store') }}" enctype="multipart/form-data" data-admin-slideshow-form>
                    @csrf
                    <input type="hidden" name="target_width" value="" data-admin-slideshow-target-width />
                    <input type="hidden" name="target_height" value="" data-admin-slideshow-target-height />

                    <div class="rounded-2xl border-2 border-dashed border-[var(--color-border)] bg-white p-4 text-center transition-colors" data-admin-slideshow-drop>
                        <div class="text-sm font-semibold text-slate-900">Drag & drop images here</div>
                        <div class="mt-1 text-xs text-slate-600">Or browse to upload (JPEG, PNG, WebP, JFIF, TIF, HEIF).</div>

                        <input id="photos" name="photos[]" type="file" accept=".jpg,.jpeg,.png,.webp,.jfif,.tif,.heif" multiple class="sr-only" data-admin-slideshow-input />

                        <div class="mt-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-center">
                            <button type="button" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] px-4 py-3 text-sm font-semibold text-slate-900 hover:bg-[var(--color-muted)]" data-admin-slideshow-browse>
                                Browse files
                            </button>
                            <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[var(--color-primary)] px-4 py-3 text-sm font-semibold text-[var(--color-on-primary)] shadow-sm hover:bg-[#001a4d]" data-admin-slideshow-upload disabled aria-disabled="true">
                                Upload
                            </button>
                        </div>
                    </div>

                    @error('photos')
                        <div class="mt-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800">{{ $message }}</div>
                    @enderror
                    @if ($errors->has('photos.*'))
                        <div class="mt-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800">One or more selected files are invalid.</div>
                    @endif

                    <div class="mt-4 hidden rounded-xl border border-[var(--color-border)] bg-[var(--color-muted)] p-3" data-admin-slideshow-queue>
                        <div class="flex items-center justify-between gap-3 text-xs font-semibold text-slate-700">
                            <div>Selected files</div>
                            <div class="text-[11px] font-semibold text-slate-600">
                                Target size: <span data-admin-slideshow-target-label>—</span>
                            </div>
                        </div>
                        <ul class="mt-2 space-y-1 text-xs text-slate-700" data-admin-slideshow-queue-list></ul>
                    </div>

                    <div class="mt-4 hidden" data-admin-slideshow-target-wrap>
                        <div class="text-xs font-semibold text-slate-700">Optimized preview (first selected image)</div>
                        <div class="mt-2 overflow-hidden rounded-2xl border border-[var(--color-border)] bg-[var(--color-muted)]">
                            <div class="home-slideshow-stage" data-admin-slideshow-target aria-hidden="true">
                                <img src="" alt="" class="home-slideshow-slide" data-admin-slideshow-target-preview hidden decoding="async" />
                                <div class="home-slideshow-overlay" aria-hidden="true"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 hidden" data-admin-slideshow-progress>
                        <div class="flex items-center justify-between text-xs font-semibold text-slate-700">
                            <span>Uploading…</span>
                            <span data-admin-slideshow-progress-text>0%</span>
                        </div>
                        <div class="mt-2 h-2 overflow-hidden rounded-full bg-[var(--color-muted)]">
                            <div class="h-2 w-0 rounded-full bg-[var(--color-primary)] transition-[width] duration-150" data-admin-slideshow-progress-bar></div>
                        </div>
                        <div class="mt-2 text-xs text-slate-600">If processing takes longer than 2 seconds, please keep this page open.</div>
                    </div>

                    <div class="mt-4 hidden rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800" data-admin-slideshow-error role="alert"></div>
                </form>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] shadow-sm lg:col-span-2">
            <div class="flex flex-col gap-3 border-b border-[var(--color-border)] bg-[var(--color-muted)] px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="text-sm font-semibold text-slate-900">Uploaded images</div>
                <form method="POST" action="{{ route('admin.slideshow.bulk_delete') }}" class="flex items-center gap-2" data-admin-slideshow-bulk-form>
                    @csrf
                    <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800 hover:bg-red-100 disabled:opacity-50" data-admin-slideshow-bulk-delete disabled aria-disabled="true">
                        Delete selected
                    </button>
                </form>
            </div>

            @if ($images->count() === 0)
                <div class="p-8 text-center text-sm text-slate-700">No slideshow images uploaded yet.</div>
            @else
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                        @foreach ($images as $image)
                            <div class="group overflow-hidden rounded-2xl border border-[var(--color-border)] bg-white shadow-sm">
                                <button type="button" class="relative block h-40 w-full bg-[var(--color-muted)]" data-admin-slideshow-preview data-preview-src="{{ asset('storage/'.ltrim($image->desktop_path, '/')) }}" aria-label="Preview slideshow image">
                                    <img
                                        src="{{ asset('storage/'.ltrim($image->desktop_path, '/')) }}"
                                        alt=""
                                        class="h-40 w-full object-cover"
                                        loading="lazy"
                                        decoding="async"
                                    />
                                    <div class="absolute inset-0 hidden items-center justify-center bg-black/30 text-xs font-semibold text-white group-hover:flex">Preview</div>
                                </button>

                                <div class="p-3">
                                    <div class="flex items-start justify-between gap-2">
                                        <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-700">
                                            <input type="checkbox" class="h-4 w-4 rounded border-[var(--color-border)] text-[var(--color-primary)]" value="{{ $image->id }}" data-admin-slideshow-select />
                                            Select
                                        </label>
                                        <form method="POST" action="{{ route('admin.slideshow.destroy', $image) }}" onsubmit="return confirm('Delete this slideshow image? This will remove all variants.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg px-2 py-1 text-xs font-semibold text-red-700 hover:bg-red-50">Delete</button>
                                        </form>
                                    </div>

                                    <div class="mt-2 text-xs text-slate-600">
                                        <div>{{ optional($image->created_at)->format('M d, Y') }}</div>
                                        <div>{{ number_format(max(0, (int) ($image->desktop_size ?? 0)) / 1024, 0) }} KB</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if ($images->hasPages())
                        <div class="mt-6 border-t border-[var(--color-border)] pt-5">
                            {{ $images->onEachSide(1)->links() }}
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div class="fixed inset-0 z-[90] hidden items-center justify-center bg-black/70 p-4" data-admin-slideshow-preview-modal role="dialog" aria-modal="true" aria-label="Slideshow image preview">
        <button type="button" class="absolute inset-0" data-admin-slideshow-preview-close aria-label="Close preview"></button>
        <div class="relative w-full max-w-5xl overflow-hidden rounded-2xl border border-white/10 bg-black">
            <div class="flex items-center justify-between gap-4 border-b border-white/10 px-4 py-3">
                <div class="text-sm font-semibold text-white">Preview</div>
                <button type="button" class="rounded-lg px-3 py-2 text-sm font-semibold text-white/90 hover:bg-white/10" data-admin-slideshow-preview-close>Close</button>
            </div>
            <div class="bg-black">
                <img src="" alt="" class="max-h-[80vh] w-full object-contain" data-admin-slideshow-preview-img loading="eager" decoding="async" />
            </div>
        </div>
    </div>
@endsection
