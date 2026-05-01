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

        @php
            $today = now()->startOfDay();
            $formatServiceDuration = function ($startDate) use ($today) {
                if (! $startDate) {
                    return null;
                }

                $start = $startDate->copy()->startOfDay();
                $end = $today->copy();

                if ($start->greaterThan($end)) {
                    $years = 0;
                    $months = 0;
                    $days = 0;
                } else {
                    $diff = $start->diff($end);
                    $years = (int) $diff->y;
                    $months = (int) $diff->m;
                    $days = (int) $diff->d;
                }

                $parts = [];
                if ($years !== 0) {
                    $parts[] = $years.' '.($years === 1 ? 'year' : 'years');
                }
                if ($months !== 0) {
                    $parts[] = $months.' '.($months === 1 ? 'month' : 'months');
                }
                if ($days !== 0 || $parts === []) {
                    $parts[] = $days.' '.($days === 1 ? 'day' : 'days');
                }

                if (count($parts) === 1) {
                    return $parts[0];
                }
                if (count($parts) === 2) {
                    return $parts[0].' and '.$parts[1];
                }

                $last = array_pop($parts);
                return implode(' ', $parts).' and '.$last;
            };
        @endphp
        @forelse (($memberGroups ?? collect()) as $voicePartName => $members)
            <section class="{{ $loop->first ? 'mt-8' : 'mt-10 border-t border-[var(--color-border)] pt-10' }}">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-900">{{ $voicePartName }}</h2>
                        <div class="mt-1 text-sm text-slate-700">{{ $members->count() }} {{ $members->count() === 1 ? 'member' : 'members' }}</div>
                    </div>
                </div>

                <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
                    @foreach ($members as $member)
                        @php($profileDescription = $member->description ?? $member->bio)
                        @php($truncatedDescription = $profileDescription ? \Illuminate\Support\Str::words($profileDescription, 20, '…') : null)
                        @php($rolesLabel = $member->role?->name)
                        @php($voicesLabel = $member->voicePart?->name)
                        @php($serviceDuration = $formatServiceDuration($member->start_date))
                        @php($bio = $member->bio)
                        @php($description = $member->description)

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
                                    <h3 class="truncate text-lg font-semibold text-slate-900 group-hover:text-[var(--color-primary)]">{{ $member->name }}</h3>
                                    <p class="mt-1 truncate text-sm text-slate-700">
                                        {{ $rolesLabel ?: 'Member' }}@if ($voicesLabel) • {{ $voicesLabel }}@endif
                                    </p>
                                    @if ($serviceDuration)
                                        <div class="mt-2 inline-flex w-fit items-center rounded-full bg-[var(--color-muted)] px-3 py-1 text-xs font-semibold text-slate-800 ring-1 ring-[var(--color-border)]">
                                            {{ $serviceDuration }} in service
                                        </div>
                                    @endif

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
                                        @php($serviceDurationModal = $formatServiceDuration($member->start_date))
                                        <div class="rounded-2xl bg-[var(--color-muted)] p-5 ring-1 ring-[var(--color-border)]">
                                            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                                @if ($rolesLabel)
                                                    <div>
                                                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-600">Role</dt>
                                                        <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $rolesLabel }}</dd>
                                                    </div>
                                                @endif

                                                @if ($voicesLabel)
                                                    <div>
                                                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-600">Voice part</dt>
                                                        <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $voicesLabel }}</dd>
                                                    </div>
                                                @endif

                                                @if ($member->email_address)
                                                    <div class="sm:col-span-2">
                                                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-600">Email address</dt>
                                                        <dd class="mt-1 text-sm font-semibold text-slate-900 break-words">{{ $member->email_address }}</dd>
                                                    </div>
                                                @endif

                                                @if ($member->start_date)
                                                    <div>
                                                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-600">Start date</dt>
                                                        <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $member->start_date->format('F j, Y') }}</dd>
                                                    </div>
                                                @endif

                                                @if ($serviceDurationModal)
                                                    <div>
                                                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-600">Years in service</dt>
                                                        <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $serviceDurationModal }} in service</dd>
                                                    </div>
                                                @endif

                                                @if ($member->address)
                                                    <div class="sm:col-span-2">
                                                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-600">Address</dt>
                                                        <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $member->address }}</dd>
                                                    </div>
                                                @endif

                                                @if ($member->hobbies)
                                                    <div class="sm:col-span-2">
                                                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-600">Hobbies</dt>
                                                        <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $member->hobbies }}</dd>
                                                    </div>
                                                @endif

                                                @if ($bio)
                                                    <div class="sm:col-span-2">
                                                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-600">Bio</dt>
                                                        <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $bio }}</dd>
                                                    </div>
                                                @endif
                                            </dl>

                                            @if ($member->facebook_url || $member->youtube_url)
                                                <div class="mt-5 flex flex-wrap gap-2">
                                                    @if ($member->facebook_url)
                                                        <a href="{{ $member->facebook_url }}" target="_blank" rel="noreferrer" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[var(--color-border)] bg-white px-4 py-3 text-sm font-semibold text-slate-900 hover:bg-[var(--color-surface)]">
                                                            Facebook
                                                        </a>
                                                    @endif
                                                    @if ($member->youtube_url)
                                                        <a href="{{ $member->youtube_url }}" target="_blank" rel="noreferrer" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-[var(--color-border)] bg-white px-4 py-3 text-sm font-semibold text-slate-900 hover:bg-[var(--color-surface)]">
                                                            YouTube
                                                        </a>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>

                                        @if ($description)
                                            <div class="rounded-2xl bg-[var(--color-muted)] p-5 ring-1 ring-[var(--color-border)]">
                                                <div class="text-sm font-semibold text-slate-900">Description</div>
                                                <div class="mt-2 whitespace-pre-line text-sm text-slate-700">{{ $description }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @empty
            <div class="mt-8 rounded-3xl bg-[var(--color-muted)] p-8 ring-1 ring-[var(--color-border)]">
                <div class="text-sm font-semibold text-slate-900">No members published yet</div>
                <div class="mt-1 text-sm text-slate-700">
                    Please check back soon. Member profiles appear as soon as they are added by the administrator.
                </div>
            </div>
        @endforelse
    </div>
@endsection
