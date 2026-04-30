@php
    $current = $paginator->currentPage();
    $last = $paginator->lastPage();
    $start = max(1, $current - 2);
    $end = min($last, $current + 2);

    if (($end - $start) < 4) {
        $missing = 4 - ($end - $start);
        $start = max(1, $start - $missing);
        $end = min($last, $start + 4);
    }
@endphp

@if ($paginator->hasPages())
    <nav class="flex flex-wrap items-center justify-center gap-2" role="navigation" aria-label="Pagination navigation">
        <a
            href="{{ $paginator->url(1) }}"
            class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[var(--color-border)] bg-white px-3 text-sm font-semibold text-slate-900 shadow-sm hover:bg-[var(--color-muted)] disabled:pointer-events-none disabled:opacity-50"
            aria-label="First page"
            @if ($paginator->onFirstPage()) aria-disabled="true" tabindex="-1" @endif
        >
            First
        </a>

        <a
            href="{{ $paginator->previousPageUrl() ?: $paginator->url(1) }}"
            class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[var(--color-border)] bg-white px-3 text-sm font-semibold text-slate-900 shadow-sm hover:bg-[var(--color-muted)]"
            aria-label="Previous page"
            @if ($paginator->onFirstPage()) aria-disabled="true" tabindex="-1" @endif
        >
            Prev
        </a>

        <div class="flex flex-wrap items-center justify-center gap-2" aria-label="Page numbers">
            @for ($page = $start; $page <= $end; $page++)
                @if ($page === $current)
                    <span
                        class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-xl bg-[var(--color-primary)] px-3 text-sm font-semibold text-[var(--color-on-primary)] shadow-sm"
                        aria-current="page"
                    >
                        {{ $page }}
                    </span>
                @else
                    <a
                        href="{{ $paginator->url($page) }}"
                        class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-xl border border-[var(--color-border)] bg-white px-3 text-sm font-semibold text-slate-900 shadow-sm hover:bg-[var(--color-muted)]"
                        aria-label="Go to page {{ $page }}"
                    >
                        {{ $page }}
                    </a>
                @endif
            @endfor
        </div>

        <a
            href="{{ $paginator->nextPageUrl() ?: $paginator->url($last) }}"
            class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[var(--color-border)] bg-white px-3 text-sm font-semibold text-slate-900 shadow-sm hover:bg-[var(--color-muted)]"
            aria-label="Next page"
            @if (! $paginator->hasMorePages()) aria-disabled="true" tabindex="-1" @endif
        >
            Next
        </a>

        <a
            href="{{ $paginator->url($last) }}"
            class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[var(--color-border)] bg-white px-3 text-sm font-semibold text-slate-900 shadow-sm hover:bg-[var(--color-muted)]"
            aria-label="Last page"
            @if (! $paginator->hasMorePages()) aria-disabled="true" tabindex="-1" @endif
        >
            Last
        </a>
    </nav>
@endif

