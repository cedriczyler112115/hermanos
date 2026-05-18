<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
        <label for="title" class="block text-sm font-medium text-slate-800">Title</label>
        <input id="title" name="title" type="text" value="{{ old('title', $article->title) }}" required class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0" />
        @error('title')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="author" class="block text-sm font-medium text-slate-800">Author</label>
        <input id="author" name="author" type="text" value="{{ old('author', $article->author) }}" class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0" />
        @error('author')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="category" class="block text-sm font-medium text-slate-800">Category</label>
        <input id="category" name="category" type="text" value="{{ old('category', $article->category) }}" class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0" />
        @error('category')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="posted_at" class="block text-sm font-medium text-slate-800">Date Posted</label>
        <input id="posted_at" name="posted_at" type="datetime-local" value="{{ old('posted_at', $article->posted_at ? $article->posted_at->format('Y-m-d\TH:i') : '') }}" required class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0" />
        @error('posted_at')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="status" class="block text-sm font-medium text-slate-800">Status</label>
        <select id="status" name="status" class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0">
            <option value="draft" {{ old('status', $article->status) === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="published" {{ old('status', $article->status) === 'published' ? 'selected' : '' }}>Published</option>
        </select>
        @error('status')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label for="fb_link" class="block text-sm font-medium text-slate-800">Facebook Embed Link</label>
        <input id="fb_link" name="fb_link" type="url" value="{{ old('fb_link', $article->fb_link) }}" placeholder="https://www.facebook.com/..." class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0" />
        <p class="mt-1 text-xs text-slate-500">Option 1: Paste the URL of the Facebook post or video.</p>
        @error('fb_link')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label for="fb_embed_code" class="block text-sm font-medium text-slate-800">Facebook Embed Code (Iframe)</label>
        <textarea id="fb_embed_code" name="fb_embed_code" rows="3" placeholder='<iframe src="..." ...></iframe>' class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-sm text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0">{{ old('fb_embed_code', $article->fb_embed_code) }}</textarea>
        <p class="mt-1 text-xs text-slate-500">Option 2: Paste the full &lt;iframe&gt; embed code from Facebook.</p>
        @error('fb_embed_code')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label for="description" class="block text-sm font-medium text-slate-800">Description / Content</label>
        <textarea id="description" name="description" rows="10" class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0">{{ old('description', $article->description) }}</textarea>
        @error('description')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label for="featured_image" class="block text-sm font-medium text-slate-800">Featured Image (optional)</label>
        <input id="featured_image" name="featured_image" type="file" accept="image/*" class="mt-1 block w-full text-sm text-slate-700 file:mr-4 file:rounded-lg file:border file:border-[var(--color-border)] file:bg-[var(--color-surface)] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-slate-900 hover:file:bg-[var(--color-muted)]" />
        @error('featured_image')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror

        @if ($article->featured_image_thumb_path || $article->featured_image_path)
            <div class="mt-3">
                <div class="text-sm text-slate-700">Current featured image</div>
                <img src="{{ asset('storage/' . ($article->featured_image_thumb_path ?: $article->featured_image_path)) }}" alt="{{ $article->title }}" class="mt-2 h-32 w-full max-w-md rounded-2xl object-cover ring-1 ring-[var(--color-border)]" />
            </div>
        @endif
    </div>
</div>
