@if ($articles->isEmpty())
    <div class="rounded-2xl border border-[var(--color-border)] bg-[var(--color-muted)] p-12 text-center">
        <div class="text-base font-medium text-slate-800">No articles found.</div>
        <p class="mt-1 text-sm text-slate-600">Try adjusting your search query.</p>
    </div>
@else
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($articles as $article)
            <article class="group flex flex-col overflow-hidden rounded-3xl border border-[var(--color-border)] bg-[var(--color-surface)] shadow-sm transition-shadow hover:shadow-md">
                <a href="{{ route('site.articles.detail', $article['slug'] ?? 'article-' . $article['id']) }}" class="relative block aspect-[16/9] overflow-hidden bg-[var(--color-muted)]">
                    @if ($article['featured_image_thumb_path'])
                        <img 
                            src="{{ asset('storage/' . $article['featured_image_thumb_path']) }}" 
                            alt="{{ $article['title'] }}" 
                            class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                            loading="lazy"
                        />
                    @else
                        <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-[var(--color-primary)]/20 to-[var(--color-accent)]/20">
                            <span class="text-xs font-semibold uppercase tracking-wider text-[var(--color-primary)]/60">No image</span>
                        </div>
                    @endif
                    @if ($article['category'])
                        <div class="absolute left-4 top-4">
                            <span class="inline-flex items-center rounded-lg bg-white/90 px-2.5 py-1 text-xs font-bold uppercase tracking-wider text-[var(--color-primary)] shadow-sm backdrop-blur-sm">
                                {{ $article['category'] }}
                            </span>
                        </div>
                    @endif
                </a>

                <div class="flex flex-1 flex-col p-6">
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                        @php
                            try {
                                $displayDate = \Carbon\Carbon::parse($article['posted_at'])->format('M d, Y');
                            } catch (\Exception $e) {
                                $displayDate = '---';
                            }
                        @endphp
                        <time datetime="{{ $article['posted_at'] }}">{{ $displayDate }}</time>
                        @if ($article['author'])
                            <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                            <span>By {{ $article['author'] }}</span>
                        @endif
                    </div>

                    <h2 class="mt-3 text-xl font-bold leading-tight text-slate-900 group-hover:text-[var(--color-primary)]">
                        <a href="{{ route('site.articles.detail', $article['slug'] ?? 'article-' . $article['id']) }}">
                            {{ $article['title'] }}
                        </a>
                    </h2>

                    <p class="mt-3 line-clamp-3 text-sm leading-relaxed text-slate-700">
                        {{ $article['excerpt'] }}
                    </p>

                    <div class="mt-auto pt-6">
                        <a href="{{ route('site.articles.detail', $article['slug'] ?? 'article-' . $article['id']) }}" class="inline-flex items-center gap-2 text-sm font-bold text-[var(--color-primary)] hover:underline">
                            Read more
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    <div class="mt-10 border-t border-[var(--color-border)] pt-8">
        {{ $articles->links('pagination.public') }}
    </div>
@endif
