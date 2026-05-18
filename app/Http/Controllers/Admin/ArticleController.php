<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Services\ImageStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $articles = Article::query()
            ->when($q !== '', function ($query) use ($q) {
                $like = '%'.$q.'%';
                $query->where(function ($inner) use ($like) {
                    $inner->where('title', 'like', $like)
                        ->orWhere('author', 'like', $like)
                        ->orWhere('category', 'like', $like);
                });
            })
            ->orderByDesc('posted_at')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.articles.index', [
            'articles' => $articles,
            'q' => $q,
        ]);
    }

    public function create()
    {
        return view('admin.articles.create', [
            'article' => new Article(['posted_at' => now(), 'status' => 'draft']),
        ]);
    }

    public function store(Request $request, ImageStorage $images)
    {
        $validated = $this->validateArticle($request);
        $validated['slug'] = $this->generateUniqueSlug($validated['title']);

        try {
            $article = DB::transaction(function () use ($request, $validated, $images) {
                if ($request->hasFile('featured_image')) {
                    $stored = $images->storeImageWithThumb(
                        $request->file('featured_image'),
                        'articles',
                        2000,
                        1200,
                        720,
                        432,
                    );

                    $validated['featured_image_path'] = $stored['path'];
                    $validated['featured_image_thumb_path'] = $stored['thumb_path'];
                }

                return Article::query()->create($validated);
            });
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->withErrors(['featured_image' => 'Failed to save the featured image.']);
        }

        Log::info('admin.articles.create', [
            'user_id' => $request->user()?->id,
            'article_id' => $article->id,
        ]);

        $this->bumpPublicVersion();

        return redirect()
            ->route('admin.articles.edit', $article)
            ->with('status', 'Article created.');
    }

    public function edit(Article $article)
    {
        return view('admin.articles.edit', [
            'article' => $article,
        ]);
    }

    public function update(Request $request, Article $article, ImageStorage $images)
    {
        $validated = $this->validateArticle($request, $article->id);
        
        if ($article->title !== $validated['title']) {
            $validated['slug'] = $this->generateUniqueSlug($validated['title'], $article->id);
        }

        $oldPhoto = $article->featured_image_path;
        $oldThumb = $article->featured_image_thumb_path;

        try {
            DB::transaction(function () use ($request, $article, $validated, $images, $oldPhoto, $oldThumb) {
                if ($request->hasFile('featured_image')) {
                    $stored = $images->storeImageWithThumb(
                        $request->file('featured_image'),
                        'articles',
                        2000,
                        1200,
                        720,
                        432,
                    );

                    $validated['featured_image_path'] = $stored['path'];
                    $validated['featured_image_thumb_path'] = $stored['thumb_path'];
                }

                $article->update($validated);

                if ($request->hasFile('featured_image')) {
                    if (is_string($oldPhoto) && $oldPhoto !== '') {
                        Storage::disk('public')->delete($oldPhoto);
                    }
                    if (is_string($oldThumb) && $oldThumb !== '' && $oldThumb !== $oldPhoto) {
                        Storage::disk('public')->delete($oldThumb);
                    }
                }
            });
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->withErrors(['featured_image' => 'Failed to update the featured image.']);
        }

        Log::info('admin.articles.update', [
            'user_id' => $request->user()?->id,
            'article_id' => $article->id,
        ]);

        $this->bumpPublicVersion();

        return redirect()
            ->route('admin.articles.edit', $article)
            ->with('status', 'Article updated.');
    }

    public function destroy(Request $request, Article $article)
    {
        $photo = $article->featured_image_path;
        $thumb = $article->featured_image_thumb_path;

        $article->delete();

        if (is_string($photo) && $photo !== '') {
            Storage::disk('public')->delete($photo);
        }
        if (is_string($thumb) && $thumb !== '' && $thumb !== $photo) {
            Storage::disk('public')->delete($thumb);
        }

        Log::info('admin.articles.delete', [
            'user_id' => $request->user()?->id,
            'article_id' => $article->id,
        ]);

        $this->bumpPublicVersion();

        return redirect()
            ->route('admin.articles.index')
            ->with('status', 'Article deleted.');
    }

    private function validateArticle(Request $request, $id = null): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'fb_link' => ['nullable', 'string', 'url', 'regex:/^(https?:\/\/)?(www\.)?facebook\.com\/.*$/'],
            'fb_embed_code' => ['nullable', 'string'],
            'posted_at' => ['required', 'date'],
            'status' => ['required', 'string', 'in:draft,published'],
            'featured_image' => ['nullable', 'image', 'max:5120'],
        ]);

        unset($validated['featured_image']);

        return $validated;
    }

    private function generateUniqueSlug(string $title, $id = null): string
    {
        $slug = Str::slug($title);
        if ($slug === '') {
            $slug = 'article';
        }
        $originalSlug = $slug;
        $count = 1;

        while (Article::query()->where('slug', $slug)->when($id, fn($q) => $q->where('id', '!=', $id))->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }

    private function bumpPublicVersion(): void
    {
        $cacheKey = 'articles_public_version';
        if (! Cache::has($cacheKey)) {
            Cache::forever($cacheKey, 1);
        } else {
            Cache::increment($cacheKey);
        }
    }
}
