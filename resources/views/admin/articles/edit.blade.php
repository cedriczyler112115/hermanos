@extends('layouts.admin')

@section('title', 'Edit Article · Admin')

@section('content')
    <div class="mx-auto max-w-2xl">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.articles.index') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] text-slate-600 hover:bg-[var(--color-muted)]">
                    ←
                </a>
                <div>
                    <h1 class="text-2xl font-semibold text-slate-900">Edit article</h1>
                    <p class="mt-1 text-sm text-slate-600">Update article details and settings.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.articles.destroy', $article) }}" onsubmit="return confirm('Delete this article?');" data-loading-message="Deleting...">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex min-h-11 items-center rounded-lg px-3 py-2.5 text-sm font-medium text-red-700 hover:bg-red-50">
                    Delete
                </button>
            </form>
        </div>

        <form method="POST" action="{{ route('admin.articles.update', $article) }}" enctype="multipart/form-data" class="mt-8 rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6 shadow-sm" data-loading-message="Updating article...">
            @csrf
            @method('PUT')
            @include('admin.articles._form')

            <div class="mt-8 border-t border-[var(--color-border)] pt-6">
                <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-[var(--color-primary)] px-4 py-3 text-sm font-semibold text-[var(--color-on-primary)] shadow-sm hover:bg-[#001a4d] focus:bg-[#001a4d]">
                    Update article
                </button>
            </div>
        </form>
    </div>
@endsection
