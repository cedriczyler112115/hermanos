@extends('layouts.admin')

@section('title', 'Add Member · Admin')

@section('content')
    <div class="flex items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Add member</h1>
            <p class="mt-1 text-sm text-slate-600">Create a new public profile for the choir roster.</p>
        </div>
        <a href="{{ route('admin.members.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-[var(--color-primary)] hover:bg-[var(--color-muted)]">Back</a>
    </div>

    <div class="mt-6 rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.members.store') }}" enctype="multipart/form-data" class="space-y-6" data-loading-message="Saving...">
            @csrf

            @include('admin.members._form', ['member' => $member])

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[var(--color-primary)] px-4 py-3 text-sm font-semibold text-[var(--color-on-primary)] shadow-sm hover:bg-[#001a4d] focus:bg-[#001a4d]">
                    Create member
                </button>
            </div>
        </form>
    </div>
@endsection
