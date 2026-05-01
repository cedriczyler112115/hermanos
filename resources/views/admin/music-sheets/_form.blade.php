<div class="grid grid-cols-1 gap-4">
    <div>
        <label for="title" class="block text-sm font-medium text-slate-800">Title</label>
        <input id="title" name="title" type="text" value="{{ old('title', $sheet->title) }}" maxlength="255" required class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0" />
        @error('title')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="composer" class="block text-sm font-medium text-slate-800">Composer</label>
        <input id="composer" name="composer" type="text" value="{{ old('composer', $sheet->composer) }}" maxlength="255" required class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0" />
        @error('composer')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="file" class="block text-sm font-medium text-slate-800">File (PDF or image)</label>
        <input id="file" name="file" type="file" accept=".pdf,.jpg,.jpeg,.png,.gif,application/pdf,image/*" {{ $fileRequired ? 'required' : '' }} class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0" />
        <div class="mt-1 text-xs text-slate-600">Allowed: PDF, JPG, JPEG, PNG, GIF. Max size: 10MB.</div>
        @error('file')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror

        @if (!$fileRequired && $sheet->file_path)
            @php
                $baseUrl = request()->getBaseUrl();
                $baseUrl = is_string($baseUrl) && $baseUrl !== '/' ? rtrim($baseUrl, '/') : '';
                $fileUrl = $baseUrl.route('site.music_sheets.file', $sheet, false);
                $downloadUrl = $baseUrl.route('site.music_sheets.download', $sheet, false);
            @endphp
            <div class="mt-3 rounded-2xl bg-[var(--color-muted)] p-4 ring-1 ring-[var(--color-border)]">
                <div class="text-sm font-semibold text-slate-900">Current file</div>
                <div class="mt-1 text-sm text-slate-700">
                    <a href="{{ $fileUrl }}" class="font-semibold text-[var(--color-primary)] hover:underline" target="_blank" rel="noopener">
                        {{ $sheet->file_original_name ?: 'View file' }}
                    </a>
                    <span class="mx-1">•</span>
                    <a href="{{ $downloadUrl }}" class="font-semibold text-[var(--color-primary)] hover:underline">
                        Download
                    </a>
                </div>
                @if ($sheet->is_image)
                    <div class="mt-3 overflow-hidden rounded-xl bg-white ring-1 ring-[var(--color-border)]">
                        <img src="{{ $fileUrl }}" alt="{{ $sheet->title }}" class="h-auto w-full object-contain" loading="lazy" decoding="async" />
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
