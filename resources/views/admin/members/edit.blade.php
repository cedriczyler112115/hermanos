@extends('layouts.admin')

@section('title', 'Edit Member · Admin')

@section('content')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div class="min-w-0">
            <h1 class="truncate text-2xl font-semibold text-slate-900">Edit member</h1>
            <p class="mt-1 truncate text-sm text-slate-600">{{ $member->name }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.members.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-[var(--color-primary)] hover:bg-[var(--color-muted)]">Back</a>
            <a href="{{ route('site.members') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-800 hover:bg-[var(--color-muted)]">Preview</a>
        </div>
    </div>

    <div class="mt-6 rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.members.update', $member) }}" enctype="multipart/form-data" class="space-y-6" data-loading-message="Updating...">
            @csrf
            @method('PUT')

            @include('admin.members._form', ['member' => $member])

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[var(--color-primary)] px-4 py-3 text-sm font-semibold text-[var(--color-on-primary)] shadow-sm hover:bg-[#001a4d] focus:bg-[#001a4d]">
                    Save changes
                </button>
            </div>
        </form>

        <div class="mt-4 border-t border-[var(--color-border)] pt-4">
            <form method="POST" action="{{ route('admin.members.destroy', $member) }}" onsubmit="return confirm('Delete this member?');" data-loading-message="Deleting...">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-800 hover:bg-red-100">
                    Delete member
                </button>
            </form>
        </div>
    </div>
@endsection
