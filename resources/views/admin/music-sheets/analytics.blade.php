@extends('layouts.admin')

@section('title', 'Music Sheets Analytics · Admin')

@section('content')
    <div class="flex items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Music Sheets Analytics</h1>
            <p class="mt-1 text-sm text-slate-600">Views and downloads aggregated by day, week, and month.</p>
        </div>
        <a href="{{ route('admin.music_sheets.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-[var(--color-primary)] hover:bg-[var(--color-muted)]">Back</a>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="overflow-hidden rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] shadow-sm">
            <div class="border-b border-[var(--color-border)] bg-[var(--color-muted)] px-4 py-3 text-sm font-semibold text-slate-900">Daily (last 30 days)</div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[var(--color-border)]">
                    <thead class="bg-[var(--color-muted)]">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Date</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold uppercase tracking-wider text-slate-700">Views</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold uppercase tracking-wider text-slate-700">Downloads</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-border)]">
                        @forelse (($daily ?? []) as $bucket => $row)
                            <tr class="hover:bg-[var(--color-muted)]">
                                <td class="px-4 py-2 text-sm text-slate-800">{{ $bucket }}</td>
                                <td class="px-4 py-2 text-right text-sm font-semibold text-slate-900">{{ (int) ($row['view'] ?? 0) }}</td>
                                <td class="px-4 py-2 text-right text-sm font-semibold text-slate-900">{{ (int) ($row['download'] ?? 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-sm text-slate-600">No data yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] shadow-sm">
            <div class="border-b border-[var(--color-border)] bg-[var(--color-muted)] px-4 py-3 text-sm font-semibold text-slate-900">Weekly (last 12 weeks)</div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[var(--color-border)]">
                    <thead class="bg-[var(--color-muted)]">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Week</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold uppercase tracking-wider text-slate-700">Views</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold uppercase tracking-wider text-slate-700">Downloads</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-border)]">
                        @forelse (($weekly ?? []) as $bucket => $row)
                            <tr class="hover:bg-[var(--color-muted)]">
                                <td class="px-4 py-2 text-sm text-slate-800">{{ $bucket }}</td>
                                <td class="px-4 py-2 text-right text-sm font-semibold text-slate-900">{{ (int) ($row['view'] ?? 0) }}</td>
                                <td class="px-4 py-2 text-right text-sm font-semibold text-slate-900">{{ (int) ($row['download'] ?? 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-sm text-slate-600">No data yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] shadow-sm">
            <div class="border-b border-[var(--color-border)] bg-[var(--color-muted)] px-4 py-3 text-sm font-semibold text-slate-900">Monthly (last 12 months)</div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[var(--color-border)]">
                    <thead class="bg-[var(--color-muted)]">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Month</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold uppercase tracking-wider text-slate-700">Views</th>
                            <th class="px-4 py-2 text-right text-xs font-semibold uppercase tracking-wider text-slate-700">Downloads</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-border)]">
                        @forelse (($monthly ?? []) as $bucket => $row)
                            <tr class="hover:bg-[var(--color-muted)]">
                                <td class="px-4 py-2 text-sm text-slate-800">{{ $bucket }}</td>
                                <td class="px-4 py-2 text-right text-sm font-semibold text-slate-900">{{ (int) ($row['view'] ?? 0) }}</td>
                                <td class="px-4 py-2 text-right text-sm font-semibold text-slate-900">{{ (int) ($row['download'] ?? 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-sm text-slate-600">No data yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

