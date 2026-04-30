@extends('layouts.admin')

@section('title', 'Edit Event · Admin')

@section('content')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Edit event</h1>
            <p class="mt-1 text-sm text-slate-600">Update event details and photo.</p>
        </div>
        <a href="{{ route('admin.events.index') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-[var(--color-primary)] hover:bg-[var(--color-muted)]">Back</a>
    </div>

    <div class="mt-6 rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6 shadow-sm">
        <form method="POST" action="{{ route('admin.events.update', $event) }}" enctype="multipart/form-data" class="space-y-6" data-loading-message="Saving...">
            @csrf
            @method('PUT')

            @include('admin.events._form', ['event' => $event])

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[var(--color-primary)] px-4 py-3 text-sm font-semibold text-[var(--color-on-primary)] shadow-sm hover:bg-[#001a4d] focus:bg-[#001a4d]">
                    Save changes
                </button>
            </div>
        </form>

        <form method="POST" action="{{ route('admin.events.destroy', $event) }}" onsubmit="return confirm('Delete this event?');" class="mt-6 border-t border-[var(--color-border)] pt-6" data-loading-message="Deleting...">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800 hover:bg-red-100">
                Delete event
            </button>
        </form>
    </div>
@endsection
