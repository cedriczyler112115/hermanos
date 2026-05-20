@extends('layouts.site')

@section('title', 'Contact · Cantores Hermanos')

 @push('styles')
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <style>
        trix-toolbar [data-trix-button-group="file-tools"] {
            display: none !important;
        }
        .trix-button-row {
            flex-wrap: wrap !important;
            overflow: visible !important;
        }
        trix-toolbar .trix-button-group {
            margin-bottom: 5px !important;
        }
        trix-editor {
            min-height: 200px !important;
            border: 1px solid var(--color-border) !important;
            border-radius: 0.75rem !important;
            padding: 0.75rem !important;
            background-color: white !important;
        }
        trix-editor:focus {
            border-color: var(--color-primary) !important;
            outline: none !important;
        }
        .loader {
            border: 2px solid #f3f3f3;
            border-top: 2px solid #3498db;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            animation: spin 2s linear infinite;
            display: none;
            margin-right: 8px;
            vertical-align: middle;
        }
        .loader.active {
            display: inline-block;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .btn-loading {
            opacity: 0.7;
            pointer-events: none;
        }
    </style>
@endpush

@section('content')
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-3xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6 shadow-sm sm:p-10 lg:col-span-2">
            <h1 class="text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">Contact Us</h1>
            <p class="mt-4 text-base text-slate-700">
                For invitations, membership inquiries, and coordination, reach out using the details below.
            </p>

            <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="rounded-3xl bg-[var(--color-muted)] p-6 ring-1 ring-[var(--color-border)]">
                    <div class="text-sm font-semibold text-[var(--color-primary)]">Email</div>
                    <div class="mt-1 text-sm text-slate-800">
                        <a class="font-semibold text-slate-900 hover:text-[var(--color-primary)]">admin@cantoreshermanos.org</a>
                    </div>
                    <div class="mt-2 text-sm text-slate-700">Our official email address</div>
                </div>

                <div class="rounded-3xl bg-[var(--color-muted)] p-6 ring-1 ring-[var(--color-border)]">
                    <div class="text-sm font-semibold text-[var(--color-primary)]">Location</div>
                    <div class="mt-1 text-sm text-slate-800">Sto. Nino Diocesan Shrine, Libertad, Butuan City, Agusan Del Norte</div>
                    <div class="mt-2 text-sm text-slate-700"></div>
                </div>
            </div>

            <div class="mt-8 rounded-3xl border border-[var(--color-border)] bg-white p-6 shadow-sm">
                <div class="text-lg font-semibold text-slate-900">Send us a message</div>
                <p class="mt-2 text-sm text-slate-700">Fill out the form below to send a message to all our officers. Include your email address and contact number for us to easily respond your queries. Thank you.</p>

                @if(session('success'))
                    <div class="mt-4 rounded-xl bg-green-50 p-4 text-sm font-semibold text-green-800 border border-green-200">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mt-4 rounded-xl bg-red-50 p-4 text-sm font-semibold text-red-800 border border-red-200">
                        <ul class="list-inside list-disc">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('site.contact.send') }}" class="mt-5 space-y-4" id="contact-form">
                    @csrf
                    <div>
                        <label for="sender_name" class="block text-sm font-medium text-slate-800">Sender Name</label>
                        <input id="sender_name" name="sender_name" type="text" class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0" required />
                    </div>
                    <div>
                        <label for="subject" class="block text-sm font-medium text-slate-800">Subject</label>
                        <input id="subject" name="subject" type="text" class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0" required />
                    </div>
                    <div>
                        <label for="message" class="block text-sm font-medium text-slate-800">Message</label>
                        <input id="message" type="hidden" name="message" value="{{ old('message') }}">
                        <trix-editor input="message" class="mt-1 w-full text-slate-900 shadow-sm"></trix-editor>
                    </div>
                    <button type="submit" id="submit-btn" class="inline-flex items-center justify-center rounded-xl bg-[var(--color-primary)] px-5 py-3 text-sm font-semibold text-[var(--color-on-primary)] shadow-sm hover:bg-[#001a4d] focus:bg-[#001a4d]">
                        <span id="btn-loader" class="loader"></span>
                        <span id="btn-text">Send Message</span>
                    </button>
                </form>
            </div>
        </div>

        <aside class="rounded-3xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6 shadow-sm sm:p-10">
            <h2 class="text-xl font-semibold text-slate-900">New members</h2>
            <p class="mt-2 text-sm text-slate-700">Do you love music, worship, and serving God through song? 
                    Become part of our choir family and use your talent to inspire and uplift the community. Whether you are experienced or still growing in confidence, you are always welcome to join us in this meaningful ministry.</p>
            <div class="mt-6 space-y-3">
                <div class="rounded-2xl bg-[var(--color-muted)] p-4 ring-1 ring-[var(--color-border)]">
                    <div class="text-sm font-semibold text-slate-900">What to expect</div>
                    <div class="mt-1 text-sm text-slate-700">As a choir member, you will take part in regular rehearsals, liturgical celebrations, special community events, and spiritual fellowship. Beyond singing, you’ll build friendships, grow in faith, and become part of a supportive and passionate ministry.</div>
                </div>
                <div class="rounded-2xl bg-[var(--color-muted)] p-4 ring-1 ring-[var(--color-border)]">
                    <div class="text-sm font-semibold text-slate-900">How to join</div>
                    <div class="mt-1 text-sm text-slate-700">Interested in singing with us? Send us a message and we’ll gladly share our rehearsal schedules, upcoming activities, and current voice-part needs. We look forward to welcoming you into our choir family!</div>
                </div>
            </div>
        </aside>
    </div>

@push('scripts')
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>
    <script>
        document.addEventListener('submit', function(e) {
            if (e.target.id === 'contact-form') {
                const btn = document.getElementById('submit-btn');
                const loader = document.getElementById('btn-loader');
                const btnText = document.getElementById('btn-text');
                
                btn.classList.add('btn-loading');
                loader.classList.add('active');
                btnText.innerText = 'Sending...';
            }
        });
    </script>
@endpush
@endsection
