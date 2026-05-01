<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Admin · ' . config('app.name', 'Cantores Hermanos'))</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-dvh">
        <header class="border-b border-[var(--color-border)] bg-[var(--color-surface)]">
            <div class="mx-auto flex max-w-6xl flex-col gap-3 px-4 py-3 sm:px-6">
                <div class="flex items-center justify-between gap-4">
                <a href="{{ route('admin.members.index') }}" class="flex items-center gap-3">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-[var(--color-primary)] text-[var(--color-on-primary)] font-semibold">
                        A
                    </span>
                    <div class="min-w-0">
                        <div class="truncate text-sm font-semibold text-slate-900">Administration</div>
                        <div class="truncate text-xs text-slate-600">Cantores Hermanos Del Sr. Sto. Niño Choir</div>
                    </div>
                </a>

                @auth
                    <div class="flex items-center gap-3">
                        <details class="relative">
                            <summary class="inline-flex min-h-11 cursor-pointer list-none items-center justify-center rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] px-4 py-2.5 text-sm font-semibold text-slate-900 hover:bg-[var(--color-muted)]">
                                Add
                            </summary>
                            <div class="absolute right-0 z-50 mt-2 w-56 overflow-hidden rounded-xl border border-[var(--color-border)] bg-white shadow-lg">
                                <a href="{{ route('admin.members.create') }}" class="block px-4 py-3 text-sm font-medium text-slate-900 hover:bg-[var(--color-muted)]">Add member</a>
                                <a href="{{ route('admin.events.create') }}" class="block px-4 py-3 text-sm font-medium text-slate-900 hover:bg-[var(--color-muted)]">Add event</a>
                                <a href="{{ route('admin.gallery_albums.create') }}" class="block px-4 py-3 text-sm font-medium text-slate-900 hover:bg-[var(--color-muted)]">Add gallery album</a>
                                <a href="{{ route('admin.performances.create') }}" class="block px-4 py-3 text-sm font-medium text-slate-900 hover:bg-[var(--color-muted)]">Add performance</a>
                                <a href="{{ route('admin.music_sheets.create') }}" class="block px-4 py-3 text-sm font-medium text-slate-900 hover:bg-[var(--color-muted)]">Add music sheet</a>
                            </div>
                        </details>
                        <a href="{{ route('site.home') }}" class="inline-flex min-h-11 items-center rounded-lg px-3 py-2.5 text-sm font-medium text-slate-800 hover:bg-[var(--color-muted)] hover:text-[var(--color-primary)]">
                            View site
                        </a>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] px-4 py-2.5 text-sm font-semibold text-slate-900 hover:bg-[var(--color-muted)]">
                                Sign out
                            </button>
                        </form>
                    </div>
                @endauth
                </div>

                @auth
                    <nav class="flex flex-wrap items-center gap-1" aria-label="Admin">
                        <a href="{{ route('admin.members.index') }}" class="min-h-11 rounded-lg px-3 py-2.5 text-sm font-semibold {{ request()->routeIs('admin.members.*') ? 'bg-[var(--color-muted)] text-[var(--color-primary)]' : 'text-slate-800 hover:bg-[var(--color-muted)] hover:text-[var(--color-primary)]' }}">
                            Members
                        </a>
                        <a href="{{ route('admin.events.index') }}" class="min-h-11 rounded-lg px-3 py-2.5 text-sm font-semibold {{ request()->routeIs('admin.events.*') ? 'bg-[var(--color-muted)] text-[var(--color-primary)]' : 'text-slate-800 hover:bg-[var(--color-muted)] hover:text-[var(--color-primary)]' }}">
                            Events
                        </a>
                        <a href="{{ route('admin.gallery_albums.index') }}" class="min-h-11 rounded-lg px-3 py-2.5 text-sm font-semibold {{ request()->routeIs('admin.gallery_albums.*') ? 'bg-[var(--color-muted)] text-[var(--color-primary)]' : 'text-slate-800 hover:bg-[var(--color-muted)] hover:text-[var(--color-primary)]' }}">
                            Gallery Albums
                        </a>
                        <a href="{{ route('admin.performances.index') }}" class="min-h-11 rounded-lg px-3 py-2.5 text-sm font-semibold {{ request()->routeIs('admin.performances.*') ? 'bg-[var(--color-muted)] text-[var(--color-primary)]' : 'text-slate-800 hover:bg-[var(--color-muted)] hover:text-[var(--color-primary)]' }}">
                            Performances
                        </a>
                        <a href="{{ route('admin.slideshow.index') }}" class="min-h-11 rounded-lg px-3 py-2.5 text-sm font-semibold {{ request()->routeIs('admin.slideshow.*') ? 'bg-[var(--color-muted)] text-[var(--color-primary)]' : 'text-slate-800 hover:bg-[var(--color-muted)] hover:text-[var(--color-primary)]' }}">
                            Slideshow
                        </a>
                        <a href="{{ route('admin.music_sheets.index') }}" class="min-h-11 rounded-lg px-3 py-2.5 text-sm font-semibold {{ request()->routeIs('admin.music_sheets.*') ? 'bg-[var(--color-muted)] text-[var(--color-primary)]' : 'text-slate-800 hover:bg-[var(--color-muted)] hover:text-[var(--color-primary)]' }}">
                            Music Sheets
                        </a>
                    </nav>
                @endauth
            </div>
        </header>

        <main class="mx-auto w-full max-w-6xl px-4 py-8 sm:px-6">
            @if (session('status'))
                <div class="mb-6 rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] p-4 text-sm text-slate-800">
                    <span class="inline-flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-[var(--color-accent)]"></span>
                        <span>{{ session('status') }}</span>
                    </span>
                </div>
            @endif

            @yield('content')
        </main>
    </body>
</html>
