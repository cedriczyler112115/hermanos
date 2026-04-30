@extends('layouts.site')

@section('title', 'Contact · Cantores Hermanos')

@section('content')
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-3xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6 shadow-sm sm:p-10 lg:col-span-2">
            <h1 class="text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">Contact</h1>
            <p class="mt-4 text-base text-slate-700">
                For invitations, membership inquiries, and coordination, reach out using the details below.
            </p>

            <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="rounded-3xl bg-[var(--color-muted)] p-6 ring-1 ring-[var(--color-border)]">
                    <div class="text-sm font-semibold text-[var(--color-primary)]">Email</div>
                    <div class="mt-1 text-sm text-slate-800">
                        <a class="font-semibold text-slate-900 hover:text-[var(--color-primary)]" href="mailto:info@example.com">info@example.com</a>
                    </div>
                    <div class="mt-2 text-sm text-slate-700">Replace with your official choir email.</div>
                </div>

                <div class="rounded-3xl bg-[var(--color-muted)] p-6 ring-1 ring-[var(--color-border)]">
                    <div class="text-sm font-semibold text-[var(--color-primary)]">Location</div>
                    <div class="mt-1 text-sm text-slate-800">Parish Church / Community Venue</div>
                    <div class="mt-2 text-sm text-slate-700">Update the address and meeting info as needed.</div>
                </div>
            </div>

            <div class="mt-8 rounded-3xl border border-[var(--color-border)] bg-white p-6 shadow-sm">
                <div class="text-lg font-semibold text-slate-900">Quick message</div>
                <p class="mt-2 text-sm text-slate-700">This simple form opens your email app with a pre-filled message.</p>

                <form method="GET" action="mailto:info@example.com" class="mt-5 space-y-4">
                    <div>
                        <label for="subject" class="block text-sm font-medium text-slate-800">Subject</label>
                        <input id="subject" name="subject" type="text" class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0" />
                    </div>
                    <div>
                        <label for="body" class="block text-sm font-medium text-slate-800">Message</label>
                        <textarea id="body" name="body" rows="5" class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0"></textarea>
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-[var(--color-primary)] px-5 py-3 text-sm font-semibold text-[var(--color-on-primary)] shadow-sm hover:bg-[#001a4d] focus:bg-[#001a4d]">
                        Open email
                    </button>
                </form>
            </div>
        </div>

        <aside class="rounded-3xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6 shadow-sm sm:p-10">
            <h2 class="text-xl font-semibold text-slate-900">New members</h2>
            <p class="mt-2 text-sm text-slate-700">Interested in joining? We welcome committed singers willing to grow in ministry.</p>
            <div class="mt-6 space-y-3">
                <div class="rounded-2xl bg-[var(--color-muted)] p-4 ring-1 ring-[var(--color-border)]">
                    <div class="text-sm font-semibold text-slate-900">What to expect</div>
                    <div class="mt-1 text-sm text-slate-700">Rehearsals, liturgical service, and community events.</div>
                </div>
                <div class="rounded-2xl bg-[var(--color-muted)] p-4 ring-1 ring-[var(--color-border)]">
                    <div class="text-sm font-semibold text-slate-900">How to join</div>
                    <div class="mt-1 text-sm text-slate-700">Send a message and we’ll share rehearsal schedules and voice-part needs.</div>
                </div>
            </div>
        </aside>
    </div>
@endsection
