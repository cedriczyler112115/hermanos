<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', config('app.name', 'Cantores Hermanos Del Sr. Sto. Niño Choir'))</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="flex min-h-dvh flex-col">
        <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-md focus:bg-[var(--color-surface)] focus:px-4 focus:py-2 focus:text-slate-900 focus:shadow">
            Skip to content
        </a>

        <header class="sticky top-0 z-40 border-b border-white/10 bg-gradient-to-r from-[var(--color-primary)] via-[#0a2f88] to-[var(--color-primary)] shadow-lg shadow-[var(--color-primary)]/15">
            <div class="h-1 w-full bg-[var(--color-accent)]"></div>
            <div class="mx-auto flex max-w-6xl items-center justify-between gap-3 px-4 py-3 sm:px-6">
                <a href="{{ route('site.home') }}" class="group flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-full bg-white/95 ring-2 ring-[var(--color-accent)] shadow-sm shadow-black/10">
                        <img src="{{ file_exists(storage_path('app/public/logo/logo.jpg')) ? asset('storage/logo/logo.jpg') : asset('favicon.ico') }}" alt="Cantores Hermanos choir logo" class="h-12 w-12 rounded-full object-cover" />
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="truncate text-sm font-semibold tracking-wide text-white sm:text-base">
                                Cantores Hermanos Del Sr. Sto. Niño Choir
                            </span>
                        </div>
                        <p class="hidden truncate text-xs text-white/75 sm:block">
                            Faith • Service • Music Ministry
                        </p>
                    </div>
                </a>

                <nav class="hidden items-center gap-1 md:flex" aria-label="Primary">
                    @php($navBase = 'min-h-11 rounded-lg px-3 py-2.5 text-sm font-semibold text-white/90 transition-colors hover:bg-white/10 hover:text-[var(--color-accent)] focus:bg-white/10 focus:text-[var(--color-accent)]')
                    @php($navActive = 'bg-white/15 text-[var(--color-accent)]')

                    <div class="relative" data-home-menu>
                        @php($homeActive = request()->routeIs('site.home') || request()->routeIs('site.history') || request()->routeIs('site.officers') || request()->routeIs('site.board_of_directors'))
                        <button href="{{ route('site.home') }}"
                            type="button"
                            class="{{ $navBase }} inline-flex items-center gap-2 {{ $homeActive ? $navActive : '' }}"
                            aria-haspopup="true"
                            aria-expanded="false"
                            data-home-menu-trigger
                        >
                            Home
                            <span class="text-xs" aria-hidden="true">▾</span>
                        </button>
                        <div
                            class="absolute left-0 z-50 mt-2 hidden w-56 overflow-hidden rounded-xl border border-white/10 bg-[var(--color-primary)] shadow-lg"
                            data-home-menu-panel
                        >
                            <a href="{{ route('site.home') }}" class="block px-4 py-3 text-sm font-semibold text-white/90 hover:bg-white/10 hover:text-[var(--color-accent)] focus:bg-white/10 focus:text-[var(--color-accent)]" @if (request()->routeIs('site.home')) aria-current="page" @endif>
                                Home
                            </a>
                            <a href="{{ route('site.history') }}" class="block px-4 py-3 text-sm font-semibold text-white/90 hover:bg-white/10 hover:text-[var(--color-accent)] focus:bg-white/10 focus:text-[var(--color-accent)]" @if (request()->routeIs('site.history')) aria-current="page" @endif>
                                About Us
                            </a>
                            <a href="{{ route('site.officers') }}" class="block px-4 py-3 text-sm font-semibold text-white/90 hover:bg-white/10 hover:text-[var(--color-accent)] focus:bg-white/10 focus:text-[var(--color-accent)]" @if (request()->routeIs('site.officers')) aria-current="page" @endif>
                                The Officers
                            </a>
                            <a href="{{ route('site.board_of_directors') }}" class="block px-4 py-3 text-sm font-semibold text-white/90 hover:bg-white/10 hover:text-[var(--color-accent)] focus:bg-white/10 focus:text-[var(--color-accent)]" @if (request()->routeIs('site.board_of_directors')) aria-current="page" @endif>
                                The BOD
                            </a>
                            <a href="{{ route('site.contact') }}" class="block px-4 py-3 text-sm font-semibold text-white/90 hover:bg-white/10 hover:text-[var(--color-accent)] focus:bg-white/10 focus:text-[var(--color-accent)]">
                                Contact Us
                            </a>
                        </div>
                    </div>
                    <a href="{{ route('site.events') }}" class="{{ $navBase }} {{ request()->routeIs('site.events') ? $navActive : '' }}" @if (request()->routeIs('site.events')) aria-current="page" @endif>
                        Events
                    </a>
                    <a href="{{ route('site.gallery') }}" class="{{ $navBase }} {{ request()->routeIs('site.gallery*') ? $navActive : '' }}" @if (request()->routeIs('site.gallery*')) aria-current="page" @endif>
                        Gallery
                    </a>
                    <a href="{{ route('site.performances') }}" class="{{ $navBase }} {{ request()->routeIs('site.performances') ? $navActive : '' }}" @if (request()->routeIs('site.performances')) aria-current="page" @endif>
                        Performances
                    </a>
                    <a href="{{ route('site.music_sheets') }}" class="{{ $navBase }} {{ request()->routeIs('site.music_sheets') ? $navActive : '' }}" @if (request()->routeIs('site.music_sheets')) aria-current="page" @endif>
                        Free Music Sheets
                    </a>
                    <a href="{{ route('site.members') }}" class="{{ $navBase }} {{ request()->routeIs('site.members') ? $navActive : '' }}" @if (request()->routeIs('site.members')) aria-current="page" @endif>
                        Choir Members
                    </a>
                </nav>

                <button
                    type="button"
                    class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-lg border border-white/20 bg-white/10 p-2 text-white transition-colors hover:bg-white/15 focus:bg-white/15 md:hidden"
                    aria-label="Open navigation menu"
                    aria-expanded="false"
                    data-mobile-nav-toggle
                >
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M3 5h14a1 1 0 0 1 0 2H3a1 1 0 1 1 0-2Zm0 6h14a1 1 0 1 1 0 2H3a1 1 0 1 1 0-2Zm0 6h14a1 1 0 1 1 0 2H3a1 1 0 1 1 0-2Z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>

            <div class="border-t border-white/10 bg-[var(--color-primary)] md:hidden" hidden data-mobile-nav-panel>
                <nav class="mx-auto flex max-w-6xl flex-col gap-1 px-4 py-3 sm:px-6" aria-label="Mobile">
                    @php($mobileBase = 'min-h-11 rounded-lg px-4 py-3 text-sm font-semibold text-white/90 transition-colors hover:bg-white/10 hover:text-[var(--color-accent)] focus:bg-white/10 focus:text-[var(--color-accent)]')
                    @php($mobileActive = 'bg-white/15 text-[var(--color-accent)]')

                    <div class="rounded-lg border border-white/10 bg-white/5" data-mobile-home>
                        @php($homeMobileActive = request()->routeIs('site.home') || request()->routeIs('site.history') || request()->routeIs('site.officers') || request()->routeIs('site.board_of_directors'))
                        <button
                            type="button"
                            class="{{ $mobileBase }} flex w-full items-center justify-between {{ $homeMobileActive ? $mobileActive : '' }}"
                            aria-expanded="false"
                            aria-controls="mobile-home-menu"
                            data-mobile-home-toggle
                        >
                            <span>Home</span>
                            <span class="text-xs" aria-hidden="true">▾</span>
                        </button>
                        <div id="mobile-home-menu" class="hidden px-2 pb-2" data-mobile-home-panel>
                            <a href="{{ route('site.home') }}" class="{{ $mobileBase }} {{ request()->routeIs('site.home') ? $mobileActive : '' }}" @if (request()->routeIs('site.home')) aria-current="page" @endif>
                                Home
                            </a>
                            <a href="{{ route('site.history') }}" class="{{ $mobileBase }} {{ request()->routeIs('site.history') ? $mobileActive : '' }}" @if (request()->routeIs('site.history')) aria-current="page" @endif>
                                About Us
                            </a>
                            <a href="{{ route('site.officers') }}" class="{{ $mobileBase }} {{ request()->routeIs('site.officers') ? $mobileActive : '' }}" @if (request()->routeIs('site.officers')) aria-current="page" @endif>
                                The Officers
                            </a>
                            <a href="{{ route('site.board_of_directors') }}" class="{{ $mobileBase }} {{ request()->routeIs('site.board_of_directors') ? $mobileActive : '' }}" @if (request()->routeIs('site.board_of_directors')) aria-current="page" @endif>
                                The Board of Directors
                            </a>
                            <a href="{{ route('site.contact') }}" class="{{ $mobileBase }}">
                                Contact Us
                            </a>
                        </div>
                    </div>
                    <a href="{{ route('site.events') }}" class="{{ $mobileBase }} {{ request()->routeIs('site.events') ? $mobileActive : '' }}" @if (request()->routeIs('site.events')) aria-current="page" @endif>
                        Events
                    </a>
                    <a href="{{ route('site.gallery') }}" class="{{ $mobileBase }} {{ request()->routeIs('site.gallery*') ? $mobileActive : '' }}" @if (request()->routeIs('site.gallery*')) aria-current="page" @endif>
                        Gallery
                    </a>
                    <a href="{{ route('site.performances') }}" class="{{ $mobileBase }} {{ request()->routeIs('site.performances') ? $mobileActive : '' }}" @if (request()->routeIs('site.performances')) aria-current="page" @endif>
                        Performances
                    </a>
                    <a href="{{ route('site.music_sheets') }}" class="{{ $mobileBase }} {{ request()->routeIs('site.music_sheets') ? $mobileActive : '' }}" @if (request()->routeIs('site.music_sheets')) aria-current="page" @endif>
                        Free Music Sheets
                    </a>
                    <a href="{{ route('site.members') }}" class="{{ $mobileBase }} {{ request()->routeIs('site.members') ? $mobileActive : '' }}" @if (request()->routeIs('site.members')) aria-current="page" @endif>
                        Members
                    </a>
                </nav>
            </div>
        </header>

        <main id="main" class="mx-auto w-full max-w-6xl flex-1 px-4 py-8 sm:px-6">
            @yield('content')
        </main>

        <footer class="border-t border-white/10 bg-gradient-to-b from-[var(--color-primary)] to-[#001a4d]">
            <div class="h-1 w-full bg-[var(--color-accent)]"></div>
            <div class="mx-auto flex max-w-6xl flex-col gap-6 px-4 py-10 sm:px-6 md:flex-row md:items-start md:justify-between">
                <div class="max-w-md">
                    <div class="text-sm font-semibold text-white">Cantores Hermanos Del Sr. Sto. Niño Choir</div>
                    <div class="mt-2 h-1 w-16 rounded-full bg-[var(--color-accent)]"></div>
                    <p class="mt-3 text-sm text-white/80">
                        A community choir dedicated to honoring Señor Santo Niño through music, service, brotherhood and fellowship.
                    </p>
                </div>
                <div class="flex flex-col gap-2 text-sm text-white/80">
                    <a class="font-semibold text-white/90 transition-colors hover:text-[var(--color-accent)]" href="{{ route('site.contact') }}">Contact</a>
                    <a class="font-semibold text-white/90 transition-colors hover:text-[var(--color-accent)]" href="{{ route('site.members') }}">Members</a>
                    <a class="font-semibold text-white/90 transition-colors hover:text-[var(--color-accent)]" href="{{ route('admin.login.form') }}">Admin</a>
                </div>
                <div class="text-sm text-white/75">
                    <div class="inline-flex items-center gap-2">
                        <span>Made with <svg width="24" height="24" viewBox="0 0 24 24" fill="red">
  <path d="M12 21s-6-4.35-9.33-8.22C-.5 7.39 3.24 1 8.4 4.28 10.08 5.32 12 7.5 12 7.5s1.92-2.18 3.6-3.22C20.76 1 24.5 7.39 21.33 12.78 18 16.65 12 21 12 21z"></path>
</svg> by Ariel B. Gonzales</span>
                    </div>
                    <div class="mt-2 text-white/70">© {{ now()->year }} Cantores Hermanos Del Sr. Sto. Niño Choir</div>
                </div>
            </div>
        </footer>
    </body>
</html>
