@extends('layouts.site')

@section('title', $article->title . ' · Cantores Hermanos')

@section('content')
    <div class="mx-auto max-w-4xl">
        <a href="{{ route('site.articles') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[var(--color-primary)] hover:underline">
            ← Back to articles
        </a>

        <article class="mt-6 overflow-hidden rounded-3xl border border-[var(--color-border)] bg-[var(--color-surface)] shadow-sm">
            @if ($article->featured_image_path)
                <div class="relative aspect-[21/9] w-full overflow-hidden bg-[var(--color-muted)]">
                    <img 
                        src="{{ asset('storage/' . $article->featured_image_path) }}" 
                        alt="{{ $article->title }}" 
                        class="h-full w-full object-cover"
                    />
                </div>
            @endif

            <div class="p-6 sm:p-10">
                <div class="flex flex-wrap items-center gap-3 text-sm font-semibold text-slate-500">
                    @if ($article->category)
                        <span class="inline-flex items-center rounded-lg bg-[var(--color-primary)]/10 px-2.5 py-1 text-xs font-bold uppercase tracking-wider text-[var(--color-primary)]">
                            {{ $article->category }}
                        </span>
                        <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                    @endif
                    <time datetime="{{ $article->posted_at }}">{{ $article->posted_at->format('F d, Y') }}</time>
                    @if ($article->author)
                        <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                        <span>By {{ $article->author }}</span>
                    @endif
                </div>

                <h1 class="mt-4 text-3xl font-bold tracking-tight text-slate-900 sm:text-5xl">
                    {{ $article->title }}
                </h1>

                @if ($fbEmbedHtml)
                    <div class="mt-8 flex justify-center overflow-hidden rounded-2xl border border-[var(--color-border)] bg-[var(--color-muted)] p-4">
                        {!! $fbEmbedHtml !!}
                    </div>
                @endif

                <div class="prose prose-slate mt-8 max-w-none prose-p:leading-relaxed prose-p:text-slate-800 prose-headings:text-slate-900">
                    {!! nl2br(e($article->description)) !!}
                </div>
            </div>
        </article>
    </div>

    @if (($article->fb_link && !str_contains($article->fb_link, 'facebook.com/plugins/')) || ($article->fb_embed_code && !str_contains($article->fb_embed_code, '<iframe')))
        {{-- Facebook SDK for Social Plugins --}}
        <div id="fb-root"></div>
        <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v17.0" nonce="article_embed"></script>
    @endif
@endsection
