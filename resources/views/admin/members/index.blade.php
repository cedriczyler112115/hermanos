@extends('layouts.admin')

@section('title', 'Members · Admin')

@section('content')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Group Members</h1>
            <p class="mt-1 text-sm text-slate-600">Add, update, and organize the choir roster.</p>
        </div>
        <a href="{{ route('admin.members.create') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[var(--color-primary)] px-4 py-3 text-sm font-semibold text-[var(--color-on-primary)] shadow-sm hover:bg-[#001a4d] focus:bg-[#001a4d]">
            Add member
        </a>
    </div>

    <div class="mt-6 overflow-hidden rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-[var(--color-border)]">
                <thead class="bg-[var(--color-muted)]">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Name</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Role</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Voice</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Active</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-border)]">
                    @forelse ($members as $member)
                        <tr class="hover:bg-[var(--color-muted)]">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 overflow-hidden rounded-full bg-[var(--color-muted)] ring-1 ring-[var(--color-border)]">
                                        @if ($member->photo_path)
                                            <img src="{{ asset('storage/' . $member->photo_path) }}" alt="{{ $member->name }}" class="h-full w-full rounded-full object-cover" />
                                        @else
                                            <div class="flex h-full w-full items-center justify-center text-sm font-semibold text-slate-700">
                                                {{ mb_strtoupper(mb_substr($member->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="truncate text-sm font-semibold text-slate-900">{{ $member->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-800">{{ $member->role?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-800">{{ $member->voicePart?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm">
                                @if ($member->is_active)
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-800 ring-1 ring-emerald-200">Active</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">Hidden</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.members.edit', $member) }}" class="inline-flex min-h-11 items-center rounded-lg px-3 py-2.5 text-sm font-medium text-[var(--color-primary)] hover:bg-[var(--color-muted)]">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.members.destroy', $member) }}" onsubmit="return confirm('Delete this member?');" data-loading-message="Deleting...">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex min-h-11 items-center rounded-lg px-3 py-2.5 text-sm font-medium text-red-700 hover:bg-red-50">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-sm text-slate-600">
                                No members yet. Click “Add member” to create the first profile.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-[var(--color-border)] bg-[var(--color-surface)] px-4 py-3">
            {{ $members->links() }}
        </div>
    </div>
@endsection
