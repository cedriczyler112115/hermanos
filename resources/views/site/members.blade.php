@extends('layouts.site')

@section('title', 'Members · Cantores Hermanos')

@section('content')
    <div class="rounded-3xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6 shadow-sm sm:p-10">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="max-w-3xl">
                <h1 class="text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">Member Profiles</h1>
                <p class="mt-4 text-base text-slate-700">
                    Meet the group members of Cantores Hermanos Del Sr. Sto. Niño Choir. This section updates automatically as the administrator adds member information.
                </p>
            </div>
            <a href="{{ route('site.contact') }}" class="inline-flex items-center justify-center rounded-xl bg-[var(--color-primary)] px-5 py-3 text-sm font-semibold text-[var(--color-on-primary)] shadow-sm hover:bg-[#001a4d] focus:bg-[#001a4d]">
                Join the choir
            </a>
        </div>

        <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
            @forelse ($members as $member)
                @php($profileDescription = $member->description ?? $member->bio)
                @php($truncatedDescription = $profileDescription ? \Illuminate\Support\Str::words($profileDescription, 20, '…') : null)
                @php($rolesLabel = $member->role?->name)
                @php($voicesLabel = $member->voicePart?->name)

                <article class="h-full overflow-hidden rounded-3xl border border-[var(--color-border)] bg-white shadow-sm">
                    <button
                        type="button"
                        class="group flex h-full w-full flex-col text-left focus:outline-none"
                        data-member-open
                        aria-controls="member-modal-{{ $member->id }}"
                        aria-expanded="false"
                    >
                        <div class="bg-gradient-to-b from-[var(--color-primary)]/10 to-white px-6 pt-6">
                            <div class="member-avatar mx-auto flex items-center justify-center bg-white ring-2 ring-[var(--color-accent)] shadow-sm transition group-hover:ring-[var(--color-primary)]">
                                @if ($member->photo_path)
                                    <img src="{{ asset('storage/' . $member->photo_path) }}" alt="{{ $member->name }}" loading="lazy" />
                                @else
                                    <div class="flex h-full w-full items-center justify-center bg-[var(--color-primary)] text-4xl font-semibold text-[var(--color-on-primary)]">
                                        {{ mb_strtoupper(mb_substr($member->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="flex flex-1 flex-col p-6 pt-4">
                            <h2 class="truncate text-lg font-semibold text-slate-900 group-hover:text-[var(--color-primary)]">{{ $member->name }}</h2>
                            <p class="mt-1 truncate text-sm text-slate-700">
                                {{ $rolesLabel ?: 'Member' }}@if ($voicesLabel) • {{ $voicesLabel }}@endif
                            </p>

                            @if ($truncatedDescription)
                                <p class="mt-4 text-sm text-slate-700">{{ $truncatedDescription }}</p>
                            @endif

                            <div class="mt-auto inline-flex items-center gap-2 pt-5 text-sm font-semibold text-[var(--color-primary)]">
                                <span>Read more</span>
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L10.94 10 7.23 6.29a.75.75 0 1 1 1.06-1.06l4.24 4.24a.75.75 0 0 1 0 1.06l-4.24 4.24a.75.75 0 0 1-1.06-.02Z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </button>
                </article>

                <div id="member-modal-{{ $member->id }}" class="member-modal" data-member-modal data-state="closed" hidden>
                    <div class="member-modal-backdrop" data-member-close aria-hidden="true"></div>
                    <div class="member-modal-panel" role="dialog" aria-modal="true" aria-label="Member details" tabindex="-1">
                        <div class="flex items-start justify-between gap-4 border-b border-[var(--color-border)] px-6 py-5">
                            <div class="min-w-0">
                                <div class="text-lg font-semibold text-slate-900">{{ $member->name }}</div>
                                <div class="mt-1 text-sm text-slate-700">
                                    {{ $rolesLabel ?: 'Member' }}@if ($voicesLabel) • {{ $voicesLabel }}@endif
                                </div>
                            </div>
                            <button type="button" class="inline-flex min-h-11 items-center rounded-xl border border-[var(--color-border)] bg-white px-4 py-3 text-sm font-semibold text-slate-900 hover:bg-[var(--color-muted)]" data-member-close>
                                Close
                            </button>
                        </div>

                        <div class="grid grid-cols-1 gap-6 px-6 py-6 sm:grid-cols-3">
                            <div class="sm:col-span-1">
                                <div class="member-avatar mx-auto flex items-center justify-center bg-white ring-2 ring-[var(--color-accent)] shadow-sm">
                                    @if ($member->photo_path)
                                        <img src="{{ asset('storage/' . $member->photo_path) }}" alt="{{ $member->name }}" loading="lazy" />
                                    @else
                                        <div class="flex h-full w-full items-center justify-center bg-[var(--color-primary)] text-4xl font-semibold text-[var(--color-on-primary)]">
                                            {{ mb_strtoupper(mb_substr($member->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="space-y-4 sm:col-span-2">
                                @if ($member->address)
                                    <div class="rounded-2xl bg-[var(--color-muted)] p-4 ring-1 ring-[var(--color-border)]">
                                        <div class="text-sm font-semibold text-slate-900">Address</div>
                                        <div class="mt-1 text-sm text-slate-700">{{ $member->address }}</div>
                                    </div>
                                @endif

                                @if ($member->hobbies)
                                    <div class="rounded-2xl bg-[var(--color-muted)] p-4 ring-1 ring-[var(--color-border)]">
                                        <div class="text-sm font-semibold text-slate-900">Hobbies</div>
                                        <div class="mt-1 text-sm text-slate-700">{{ $member->hobbies }}</div>
                                    </div>
                                @endif

                                @if ($profileDescription)
                                    <div class="rounded-2xl bg-[var(--color-muted)] p-4 ring-1 ring-[var(--color-border)]">
                                        <div class="text-sm font-semibold text-slate-900">Description</div>
                                        <div class="mt-1 text-sm text-slate-700">{{ $profileDescription }}</div>
                                    </div>
                                @endif

                                @if ($member->facebook_url || $member->youtube_url)
                                    <div class="flex flex-wrap gap-2">
                                        @if ($member->facebook_url)
                                            <a href="{{ $member->facebook_url }}" target="_blank" rel="noreferrer" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[var(--color-border)] bg-white px-4 py-3 text-sm font-semibold text-slate-900 hover:bg-[var(--color-muted)]">
                                                Facebook
                                            </a>
                                        @endif
                                        @if ($member->youtube_url)
                                            <a href="{{ $member->youtube_url }}" target="_blank" rel="noreferrer" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[var(--color-border)] bg-white px-4 py-3 text-sm font-semibold text-slate-900 hover:bg-[var(--color-muted)]">
                                                YouTube
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-3xl bg-[var(--color-muted)] p-8 ring-1 ring-[var(--color-border)] md:col-span-3">
                    <div class="text-sm font-semibold text-slate-900">No members published yet</div>
                    <div class="mt-1 text-sm text-slate-700">
                        Please check back soon. Member profiles appear as soon as they are added by the administrator.
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $members->links() }}
        </div>
    </div>
@endsection
