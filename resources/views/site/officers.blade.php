@extends('layouts.site')

@section('title', 'Organizational Structure · Cantores Hermanos')

@section('content')
    <div class="rounded-3xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6 shadow-sm sm:p-10">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="max-w-3xl">
                <h1 class="text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">Organizational Structure</h1>
                <p class="mt-4 text-base text-slate-700">
                    The organizational structure of a choir group is the clear arrangement of roles and voice sections that defines leadership, coordination, and member responsibilities to ensure unified, orderly, and harmonious musical performance.
                </p>
            </div>
            <a href="{{ route('site.members') }}" class="inline-flex items-center justify-center rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] px-5 py-3 text-sm font-semibold text-slate-900 hover:bg-[var(--color-muted)]">
                View all members
            </a>
        </div>

        @php($presidentMembers = ($presidentMembers ?? collect()))
        @php($vicePresidentMembers = ($vicePresidentMembers ?? collect()))

        <div class="mt-10 space-y-10">
            <section>
                <div class="flex items-center justify-center">
                    <div class="w-full max-w-md">
                        <div class="mb-4 text-center">
                            <div class="text-lg font-semibold text-slate-900">{{ $presidentRole?->name ?: 'President' }}</div>
                        </div>
                        @if ($presidentMembers->count() === 0)
                            <div class="rounded-3xl bg-[var(--color-muted)] p-8 ring-1 ring-[var(--color-border)] text-center">
                                <div class="text-sm font-semibold text-slate-900">No president assigned</div>
                                <div class="mt-1 text-sm text-slate-700">Assign the President role (Role ID 1) to a member.</div>
                            </div>
                        @else
                            <div class="grid grid-cols-1 gap-4">
                                @foreach ($presidentMembers as $member)
                                    @php($rolesLabel = $member->role?->name)
                                    @php($voicesLabel = $member->voicePart?->name)
                                    <article class="h-full overflow-hidden rounded-3xl border border-[var(--color-border)] bg-white shadow-sm">
                                        <div class="bg-gradient-to-b from-[var(--color-primary)]/10 to-white px-6 pt-6">
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
                                        <div class="flex flex-1 flex-col p-6 pt-4">
                                            <h3 class="truncate text-center text-lg font-semibold text-slate-900">{{ $member->name }}</h3>
                                            <p class="mt-1 truncate text-center text-sm text-slate-700">
                                                {{ $rolesLabel ?: 'Officer' }}@if ($voicesLabel) • {{ $voicesLabel }}@endif
                                            </p>
                                            @if ($member->email_address)
                                                <div class="mt-2 truncate text-center text-sm font-semibold text-slate-900">{{ $member->email_address }}</div>
                                            @endif
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </section>

            <section>
                <div class="flex items-center justify-center">
                    <div class="w-full max-w-md">
                        <div class="mb-4 text-center">
                            <div class="text-sm font-semibold text-slate-800" style="margin-top:15px;">{{ $vicePresidentRole?->name ?: 'Vice President' }}</div>
                        </div>
                        @if ($vicePresidentMembers->count() === 0)
                            <div class="rounded-3xl bg-[var(--color-muted)] p-8 ring-1 ring-[var(--color-border)] text-center">
                                <div class="text-sm font-semibold text-slate-900" >No vice president assigned</div>
                                <div class="mt-1 text-sm text-slate-700">Assign the Vice President role (Role ID 2) to a member.</div>
                            </div>
                        @else
                            <div class="grid grid-cols-1 gap-4">
                                @foreach ($vicePresidentMembers as $member)
                                    @php($rolesLabel = $member->role?->name)
                                    @php($voicesLabel = $member->voicePart?->name)
                                    <article class="h-full overflow-hidden rounded-3xl border border-[var(--color-border)] bg-white shadow-sm">
                                        <div class="bg-gradient-to-b from-[var(--color-primary)]/10 to-white px-6 pt-6">
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
                                        <div class="flex flex-1 flex-col p-6 pt-4">
                                            <h3 class="truncate text-center text-lg font-semibold text-slate-900">{{ $member->name }}</h3>
                                            <p class="mt-1 truncate text-center text-sm text-slate-700">
                                                {{ $rolesLabel ?: 'Officer' }}@if ($voicesLabel) • {{ $voicesLabel }}@endif
                                            </p>
                                            @if ($member->email_address)
                                                <div class="mt-2 truncate text-center text-sm font-semibold text-slate-900">{{ $member->email_address }}</div>
                                            @endif
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </section>

            <section>
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-900">Officers</h2>
                        <div class="mt-1 text-sm text-slate-700">{{ ($remainingOfficers ?? collect())->count() }} {{ ($remainingOfficers ?? collect())->count() === 1 ? 'member' : 'members' }}</div>
                    </div>
                </div>

                <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach (($remainingOfficers ?? collect()) as $member)
                        @php($rolesLabel = $member->role?->name)
                        @php($voicesLabel = $member->voicePart?->name)
                        <article class="h-full overflow-hidden rounded-3xl border border-[var(--color-border)] bg-white shadow-sm">
                            <div class="bg-gradient-to-b from-[var(--color-primary)]/10 to-white px-6 pt-6">
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
                            <div class="flex flex-1 flex-col p-6 pt-4">
                                <h3 class="truncate text-center text-lg font-semibold text-slate-900">{{ $member->name }}</h3>
                                <p class="mt-1 truncate text-center text-sm text-slate-700">
                                    {{ $rolesLabel ?: 'Officer' }}@if ($voicesLabel) • {{ $voicesLabel }}@endif
                                </p>
                                @if ($member->email_address)
                                    <div class="mt-2 truncate text-center text-sm font-semibold text-slate-900">{{ $member->email_address }}</div>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            @php($choirMembers = ($choirMembers ?? collect()))
            @if ($choirMembers->count() > 0)
                <section>
                    <div class="my-12" aria-hidden="true">
                        <div class="relative">
                            <div class="h-1 w-full rounded-full bg-gradient-to-r from-transparent via-[var(--color-border)] to-transparent"></div>
                            <div class="absolute inset-x-0 -top-5 flex justify-center" style="margin-top: 10px;margin-bottom: 10px;">
                                <div class="rounded-full border border-white/20 bg-gradient-to-r from-[var(--color-primary)] via-[#0a2f88] to-[var(--color-primary)] px-6 py-2 text-xs font-extrabold tracking-[0.28em] text-white shadow-lg shadow-[var(--color-primary)]/20 ring-1 ring-[var(--color-border)]">
                                    CHOIR MEMBER
                                </div>
                            </div>
                            <div class="mt-6 flex items-center justify-center">
                                <div class="h-2.5 w-2.5 rounded-full bg-[var(--color-accent)] ring-4 ring-[var(--color-surface)]"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ($choirMembers as $member)
                            @php($rolesLabel = $member->role?->name)
                            @php($voicesLabel = $member->voicePart?->name)
                            <article class="h-full overflow-hidden rounded-3xl border border-[var(--color-border)] bg-white shadow-sm">
                                <div class="bg-gradient-to-b from-[var(--color-primary)]/10 to-white px-6 pt-6">
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
                                <div class="flex flex-1 flex-col p-6 pt-4">
                                    <h3 class="truncate text-center text-lg font-semibold text-slate-900">{{ $member->name }}</h3>
                                    <p class="mt-1 truncate text-center text-sm text-slate-700">
                                        {{ $rolesLabel ?: 'Member' }}@if ($voicesLabel) • {{ $voicesLabel }}@endif
                                    </p>
                                    @if ($member->email_address)
                                        <div class="mt-2 truncate text-center text-sm font-semibold text-slate-900">{{ $member->email_address }}</div>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>
@endsection
