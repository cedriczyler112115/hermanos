@extends('layouts.site')

@section('title', 'The Board of Directors · Cantores Hermanos')

@section('content')
    <div class="rounded-3xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6 shadow-sm sm:p-10">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="max-w-3xl">
                <h1 class="text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">The Board of Directors</h1>
                <p class="mt-4 text-base text-slate-700">
                    The board members who help guide and support the choir’s direction.
                </p>
            </div>
            <a href="{{ route('site.members') }}" class="inline-flex items-center justify-center rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] px-5 py-3 text-sm font-semibold text-slate-900 hover:bg-[var(--color-muted)]">
                View all members
            </a>
        </div>

        <div class="mt-10 grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
            @forelse (($directors ?? collect()) as $member)
                @php($rolesLabel = $member->role?->name)
                @php($voicesLabel = $member->voicePart?->name)
                <article class="group overflow-hidden rounded-3xl border border-[var(--color-border)] bg-white shadow-sm">
                    <div class="bg-gradient-to-b from-[var(--color-primary)]/10 to-white px-6 pt-6">
                        <div class="mx-auto flex h-20 w-20 items-center justify-center overflow-hidden rounded-full bg-white ring-2 ring-[var(--color-accent)] shadow-sm">
                            @if ($member->photo_path)
                                <img src="{{ asset('storage/' . $member->photo_path) }}" alt="{{ $member->name }}" class="h-full w-full rounded-full object-cover" loading="lazy" />
                            @else
                                <div class="flex h-full w-full items-center justify-center bg-[var(--color-primary)] text-3xl font-semibold text-[var(--color-on-primary)]">
                                    {{ mb_strtoupper(mb_substr($member->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="p-6 pt-4">
                        <h2 class="truncate text-lg font-semibold text-slate-900 group-hover:text-[var(--color-primary)]">{{ $member->name }}</h2>
                        <div class="mt-1 text-sm text-slate-700">
                            {{ $rolesLabel ?: 'Member' }}@if ($voicesLabel) • {{ $voicesLabel }}@endif
                        </div>

                        @if ($member->email_address)
                            <div class="mt-3 truncate text-sm font-semibold text-slate-900">{{ $member->email_address }}</div>
                        @endif

                        <div class="mt-4 flex flex-wrap gap-2">
                            @if ($member->facebook_url)
                                <a href="{{ $member->facebook_url }}" target="_blank" rel="noreferrer" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] px-4 py-3 text-sm font-semibold text-slate-900 hover:bg-[var(--color-muted)]">
                                    Facebook
                                </a>
                            @endif
                            @if ($member->youtube_url)
                                <a href="{{ $member->youtube_url }}" target="_blank" rel="noreferrer" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] px-4 py-3 text-sm font-semibold text-slate-900 hover:bg-[var(--color-muted)]">
                                    YouTube
                                </a>
                            @endif
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-3xl bg-[var(--color-muted)] p-8 ring-1 ring-[var(--color-border)] sm:col-span-2 lg:col-span-4">
                    <div class="text-sm font-semibold text-slate-900">No board members published yet</div>
                    <div class="mt-1 text-sm text-slate-700">
                        Mark members as Board of Directors in the admin panel to display them here.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection
