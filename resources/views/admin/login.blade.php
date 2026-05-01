@extends('layouts.admin')

@section('title', 'Admin Login · Cantores Hermanos')

@section('content')
    <div class="mx-auto max-w-md">
        <div class="rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6 shadow-sm">
            <h1 class="text-xl font-semibold text-slate-900">Administrator sign in</h1>
            <p class="mt-1 text-sm text-slate-600">Manage group members and website content.</p>

            <form method="POST" action="{{ route('admin.login') }}" class="mt-6 space-y-4" data-loading-message="Signing in...">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-800">Email</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="abgonzales87@gmail.com"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        required
                        class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0"
                    />
                    @error('email')
                        <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-800">Password</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        value="cedie112115"
                        autocomplete="current-password"
                        required
                        class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0"
                    />
                    @error('password')
                        <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
                    @enderror
                </div>

                <div class="flex items-center justify-between gap-3">
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-slate-300 text-[var(--color-primary)] focus:ring-0" />
                        <span>Remember me</span>
                    </label>

                    <a href="{{ route('site.home') }}" class="text-sm font-medium text-[var(--color-primary)] hover:underline">
                        Back to site
                    </a>
                </div>

                <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-[var(--color-primary)] px-4 py-3 text-sm font-semibold text-[var(--color-on-primary)] shadow-sm hover:bg-[#001a4d] focus:bg-[#001a4d]">
                    Sign in
                </button>
            </form>
        </div>
    </div>
@endsection
