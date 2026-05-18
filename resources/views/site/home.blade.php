@extends('layouts.site')

@section('title', 'Cantores Hermanos Del Sr. Sto. Niño Choir')

@section('content')
    <section class="home-slideshow-card mb-10 w-full overflow-hidden rounded-3xl border border-[var(--color-border)] bg-[var(--color-surface)] shadow-sm" data-home-slideshow>
        <div class="relative">
            @if (!empty($slideshowError))
                <div class="p-6">
                    <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800" role="alert">
                        {{ $slideshowError }}
                    </div>
                </div>
            @endif
            @if (empty($slideshowError) && !empty($slideshowWarning))
                <div class="p-6">
                    <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-900" role="status" aria-live="polite">
                        {{ $slideshowWarning }}
                    </div>
                </div>
            @endif

            @php
                $slides = array_values(array_filter((array) ($slideshowImages ?? [])));
            @endphp

            @if (count($slides) === 0)
                <div class="home-slideshow-empty flex items-center justify-center bg-[var(--color-muted)]">
                    <div class="rounded-2xl bg-white/90 px-5 py-4 text-center text-sm font-semibold text-slate-900 ring-1 ring-[var(--color-border)]">
                        No slideshow images found in <span class="font-semibold">storage/app/public/slideshow</span>.
                    </div>
                </div>
            @else
                <div class="home-slideshow-stage" aria-label="Homepage slideshow" tabindex="0" data-home-slideshow-stage>
                    @foreach ($slides as $slide)
                        @php
                            $large = is_array($slide) ? (string) ($slide['large'] ?? '') : (string) $slide;
                            $srcset = is_array($slide) ? (string) ($slide['srcset'] ?? '') : '';
                            $sizes = is_array($slide) ? (string) ($slide['sizes'] ?? '') : '';
                        @endphp
                        <img
                            src="{{ $loop->first ? $large : '' }}"
                            data-src="{{ $large }}"
                            @if ($srcset !== '')
                                srcset="{{ $loop->first ? $srcset : '' }}"
                                data-srcset="{{ $srcset }}"
                            @endif
                            @if ($sizes !== '')
                                sizes="{{ $loop->first ? $sizes : '' }}"
                                data-sizes="{{ $sizes }}"
                            @endif
                            alt=""
                            class="home-slideshow-slide"
                            data-slide
                            data-active="{{ $loop->first ? '1' : '0' }}"
                            loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                            decoding="async"
                            fetchpriority="{{ $loop->first ? 'high' : 'auto' }}"
                        />
                    @endforeach
                    <div class="home-slideshow-overlay" aria-hidden="true"></div>
                    <div class="sr-only" data-home-slideshow-status aria-live="polite"></div>
                </div>

                @if (count($slides) > 1)
                    <div class="home-slideshow-controls" aria-label="Slideshow controls">
                        <button type="button" class="home-slideshow-btn" data-prev aria-label="Previous image" tabindex="0" disabled aria-disabled="true">
                            ‹
                        </button>
                        <button type="button" class="home-slideshow-btn" data-next aria-label="Next image" tabindex="0">
                            ›
                        </button>
                    </div>
                @endif
            @endif
        </div>
    </section>
    <section class="overflow-hidden rounded-3xl border border-[var(--color-border)] bg-[var(--color-surface)] shadow-sm">
        <div class="grid grid-cols-1 gap-10 px-6 py-10 md:grid-cols-2 md:px-10 md:py-14">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full bg-[var(--color-muted)] px-3 py-1 text-xs font-semibold text-slate-700 ring-1 ring-[var(--color-border)]">
                    <span class="h-2 w-2 rounded-full bg-[var(--color-accent)]"></span>
                    <span>Music Ministry • Community • Faith</span>
                </div>
                <h1 class="mt-5 text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl">
                    Cantores Hermanos Del Sr. Sto. Niño Choir
                </h1>
                <p class="mt-4 max-w-prose text-base text-slate-700">
                    We are a community choir dedicated to uplifting worship and celebrating the mission of Señor Santo Niño through prayerful music, service, and fellowship.
                </p>

                <div class="mt-7 flex flex-col gap-3 sm:flex-row sm:items-center">
                    <a href="{{ route('site.contact') }}" class="inline-flex items-center justify-center rounded-xl bg-[var(--color-primary)] px-5 py-3 text-sm font-semibold text-[var(--color-on-primary)] shadow-sm hover:bg-[#001a4d] focus:bg-[#001a4d]">
                        Get in touch
                    </a>
                    <a href="{{ route('site.members') }}" class="inline-flex items-center justify-center rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] px-5 py-3 text-sm font-semibold text-slate-900 hover:bg-[var(--color-muted)]">
                        Meet the members
                    </a>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div class="rounded-2xl bg-[var(--color-muted)] p-4 ring-1 ring-[var(--color-border)]">
                        <div class="text-sm font-semibold text-[var(--color-primary)]">Mission</div>
                        <div class="mt-1 text-sm text-slate-700">The mission of the choir group dedicated to Señor Sto. Niño is to glorify God through sacred music and to lead the faithful in prayerful and uplifting worship. Rooted in devotion to the Holy Child Jesus, the group commits to serving the Church with humility, discipline, and unity.

<br><br>We aim to inspire deeper faith and devotion among the community by offering songs that reflect reverence, joy, and thanksgiving. Through our voices and service, we seek to become instruments of God’s love, spreading His message of hope and salvation.

<br><br>Guided by the example of Señor Sto. Niño, we dedicate our talents to strengthening liturgical celebrations, nurturing spiritual growth, and fostering a Christ-centered community.</div>
                    </div>
                    <div class="rounded-2xl bg-[var(--color-muted)] p-4 ring-1 ring-[var(--color-border)]">
<div class="text-sm font-semibold text-[var(--color-primary)]">Purpose</div>
                        <div class="mt-1 text-sm text-slate-700">The purpose of this community is to serve God through music by leading the
congregation in song during Mass and to contribute to the community through
special projects that promote its advocacies and goodwill.</div>
                        <br><div class="text-sm font-semibold text-[var(--color-primary)]">Objective</div>
<div class="mt-2 text-sm text-slate-700 leading-relaxed space-y-2">

    <p class="font-medium text-slate-800">
        The objectives of the community are:
    </p>

    <ol class="list-decimal list-outside pl-6 space-y-1">
        <li>To foster a deeper understanding of the Word of God through the gift of song;</li>
        <li>To engage members in activities that nurture their spiritual growth;</li>
        <li>To raise the musical standard and excellence among members;</li>
        <li>To create and perform original religious compositions;</li>
        <li>To promote musical appreciation within the group and the wider community;</li>
        <li>To initiate and participate in projects that serve the community in meaningful ways;</li>
        <li>To serve God and the community through music and dedicated ministry.</li>
    </ol>

</div>                    </div>
                </div>
            </div>

            <div class="relative">
                <div class="absolute inset-0 rounded-3xl bg-[var(--color-primary)] opacity-10"></div>
                <div class="relative flex h-full flex-col overflow-hidden rounded-3xl border border-[var(--color-border)] bg-gradient-to-br from-[var(--color-primary)] via-[#0a2f88] to-[#001a4d] p-6 shadow-sm sm:p-8">
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-bold text-[var(--color-on-primary)] uppercase tracking-widest">Featured Highlights</div>
                        <div class="h-px flex-1 bg-white/10 ml-4"></div>
                    </div>

                    <div class="mt-8 flex-1 space-y-12">
                        {{-- Latest Articles Section --}}
                        <section aria-labelledby="home-featured-articles-title">
                            <div class="flex items-center justify-between">
                                <h3 id="home-featured-articles-title" class="text-xs font-bold uppercase tracking-[0.2em] text-white">Latest Articles</h3>
                                <a href="{{ route('site.articles') }}" class="text-[10px] font-bold uppercase tracking-widest text-[var(--color-accent)] hover:text-white transition-colors">See all articles</a>
                            </div>
                            
                            <div class="mt-5 space-y-4">
                                @forelse ($latestArticles as $article)
                                    <a href="{{ route('site.articles.detail', $article['slug']) }}" class="group block overflow-hidden rounded-2xl border border-white/10 bg-white/10 p-4 shadow-sm transition-all hover:border-white/30 hover:bg-white/15 hover:shadow-md">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="text-[10px] font-bold uppercase tracking-wider text-[var(--color-accent)]">{{ $article['posted_at'] }}</div>
                                            <div class="rounded-full bg-white/20 px-2 py-0.5 text-[9px] font-bold text-white uppercase tracking-tighter">{{ $article['category'] ?? 'General' }}</div>
                                        </div>
                                        <h4 class="mt-2 text-base font-bold text-white group-hover:text-[var(--color-accent)] transition-colors leading-snug">{{ $article['title'] }}</h4>
                                        <p class="mt-2 line-clamp-2 text-xs text-white leading-relaxed">{{ $article['excerpt'] }}</p>
                                        <div class="mt-3 flex items-center gap-1 text-[10px] font-bold text-[var(--color-accent)] opacity-0 group-hover:opacity-100 transition-opacity">
                                            <span>Read full article</span>
                                            <span>→</span>
                                        </div>
                                    </a>
                                @empty
                                    <div class="rounded-2xl border border-dashed border-white/20 p-6 text-center">
                                        <p class="text-xs italic text-white">No articles posted yet.</p>
                                    </div>
                                @endforelse
                            </div>
                        </section>

                        {{-- Visual Separator --}}
                        <div class="relative py-2 flex items-center justify-center">
                            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                <div class="w-full border-t border-white/20"></div>
                            </div>
                            <div class="relative bg-[#0a2f88] px-4">
                                <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                                </svg>
                            </div>
                        </div>

                        {{-- Upcoming Events Section --}}
                        <section aria-labelledby="home-featured-events-title">
                            <div class="flex items-center justify-between">
                                <h3 id="home-featured-events-title" class="text-xs font-bold uppercase tracking-[0.2em] text-white">Upcoming Events</h3>
                                <a href="{{ route('site.events') }}" class="text-[10px] font-bold tracking-widest text-[var(--color-accent)] hover:text-white transition-colors">See all events</a>
                            </div>

                            <div class="mt-5 space-y-4">
                                @forelse ($upcomingEvents as $event)
                                    <div class="group relative overflow-hidden rounded-2xl border border-white/10 bg-white/10 p-4 shadow-sm transition-all hover:border-[var(--color-accent)]/50 hover:bg-white/15">
                                        <div class="flex flex-col gap-1">
                                            <div class="text-[10px] font-black uppercase tracking-widest text-[var(--color-accent)]">{{ $event['date'] }}</div>
                                            <h4 class="text-base font-bold text-white leading-snug">{{ $event['title'] }}</h4>
                                            <div class="mt-1 flex items-center gap-2 text-[10px] font-medium text-white">
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <span>{{ $event['location'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="rounded-2xl border border-dashed border-white/20 p-6 text-center">
                                        <p class="text-xs italic text-white">No upcoming events scheduled.</p>
                                    </div>
                                @endforelse
                            </div>
                        </section>
                    </div>

                    <div class="mt-10 pt-6 border-t border-white/20">
                        <div class="flex items-center justify-center gap-4 text-[10px] text-white font-bold uppercase tracking-[0.3em]">
                            <span>Faith</span>
                            <span class="h-1 w-1 rounded-full bg-white"></span>
                            <span>Service</span>
                            <span class="h-1 w-1 rounded-full bg-white"></span>
                            <span>Ministry</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- Facebook Social Feed Section --}}
    <section class="mb-10 overflow-hidden rounded-3xl border border-[var(--color-border)] bg-[var(--color-surface)] shadow-sm">
        <div class="p-6 md:p-10">
            <div class="flex flex-col gap-8">
                {{-- Section Header --}}
                <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                    <div class="max-w-2xl">
                        <div class="inline-flex items-center gap-2 rounded-full bg-[var(--color-muted)] px-3 py-1 text-xs font-semibold text-[#1877F2] ring-1 ring-[#1877F2]/20">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            <span>Social Connect</span>
                        </div>
                        <h2 class="mt-4 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Latest from our Community</h2>
                        <p class="mt-2 text-base text-slate-600">Stay updated with our recent activities, rehearsals, and announcements directly from our Facebook page.</p>
                    </div>
                    <div class="shrink-0">
                        <a href="https://www.facebook.com/profile.php?id=100086482940323"
                        target="_blank"
                        rel="noreferrer"
                        class="inline-flex items-center gap-2 justify-center rounded-xl bg-[#1877F2] px-5 py-3 text-sm font-semibold text-black shadow-sm hover:bg-[#166fe5] focus:bg-[#166fe5]">

                            <!-- Facebook Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                                class="h-5 w-5">
                                <path d="M22 12.07C22 6.477 17.523 2 12 2S2 6.477 2 12.07c0 5.017 3.657 9.176 8.438 9.93v-7.03H7.898v-2.9h2.54V9.845c0-2.52 1.492-3.913 3.777-3.913 1.094 0 2.238.198 2.238.198v2.475h-1.26c-1.243 0-1.63.775-1.63 1.57v1.885h2.773l-.443 2.9h-2.33V22c4.78-.754 8.437-4.913 8.437-9.93z"/>
                            </svg>

                            Follow us on Facebook
                        </a>
                     </div>
                            </div>

                {{-- Side-by-Side FB & YouTube Plugins --}}
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                    {{-- Left Side: YouTube Channel --}}
                    <div class="w-full overflow-hidden rounded-2xl border border-[var(--color-border)] bg-[var(--color-muted)] shadow-inner">
                        <div class="flex flex-col h-full">
                            <div class="bg-white/80 px-4 py-3 border-b border-[var(--color-border)] flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="h-5 w-5 text-[#FF0000]" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                    </svg>
                                    <span class="text-xs font-bold uppercase tracking-widest text-slate-900">YouTube Latest Video</span>
                                </div>
                                <a href="https://www.youtube.com/@CantoresHermanos1999" target="_blank" rel="noreferrer" class="text-[10px] font-bold text-[#FF0000] hover:underline">Subscribe</a>
                            </div>
                            <div class="flex-1 flex flex-col justify-center bg-white/50 p-6">
                                <div id="youtube-video-list" class="grid grid-cols-2 gap-3 max-h-[340px] overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-slate-300 scrollbar-track-transparent">
                                    @if(!empty($youtubeVideos))
                                        @foreach($youtubeVideos as $video)
                                            <a href="{{ $video['url'] }}" target="_blank" rel="noreferrer"
                                               class="group block rounded-lg overflow-hidden bg-slate-100 hover:bg-slate-200 transition-colors">
                                                <div class="relative aspect-video">
                                                    <img src="{{ $video['thumbnail'] }}" alt="{{ $video['title'] }}" class="w-full h-full object-cover">
                                                    <div class="absolute inset-0 flex items-center justify-center bg-black/0 group-hover:bg-black/20 transition-colors">
                                                        <svg class="h-10 w-10 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M8 5v14l11-7z"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="p-2">
                                                    <div class="text-xs font-medium text-slate-900 line-clamp-2">{{ $video['title'] }}</div>
                                                    @if(!empty($video['published']))
                                                        <div class="text-[10px] text-slate-500 mt-1">{{ \Carbon\Carbon::parse($video['published'])->toDateString() }}</div>
                                                    @endif
                                                </div>
                                            </a>
                                        @endforeach
                                    @else
                                        <div class="col-span-2 aspect-video">
                                            <iframe
                                                class="h-full w-full rounded-xl"
                                                src="https://www.youtube.com/embed/videoseries?list=UU0jDnZ1GfhkQLOAmYPH0V_g&playsinline=1"
                                                title="Cantores Hermanos YouTube Videos"
                                                frameborder="0"
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                                allowfullscreen>
                                            </iframe>
                                        </div>
                                    @endif
                                </div>
                                <div class="mt-4 text-center">
                                    <p class="text-xs font-medium text-slate-600">Watch our latest performances and rehearsals.</p>
                                    <a href="https://www.youtube.com/@CantoresHermanos1999" target="_blank" rel="noreferrer" class="mt-3 inline-flex items-center gap-2 rounded-lg bg-[#FF0000] px-4 py-2 text-[10px] font-bold text-white hover:bg-[#cc0000] transition-colors">
                                        Visit YouTube Channel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Side: Full Timeline & Events --}}
                    <div class="w-full overflow-hidden rounded-2xl border border-[var(--color-border)] bg-[var(--color-muted)] shadow-inner">
                        <div class="flex flex-col">
                            <div class="bg-white/80 px-4 py-3 border-b border-[var(--color-border)] flex items-center gap-2">
                                <svg class="h-4 w-4 text-[#1877F2]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-xs font-bold uppercase tracking-widest text-slate-900">Facebook Community</span>
                            </div>
                            <div class="flex justify-center bg-white/50 py-6">
                                <div class="fb-page" 
                                    data-href="https://www.facebook.com/profile.php?id=100086482940323" 
                                    data-tabs="timeline,events" 
                                    data-width="500" 
                                    data-height="600" 
                                    data-small-header="false" 
                                    data-adapt-container-width="true" 
                                    data-hide-cover="false" 
                                    data-show-facepile="true">
                                    <blockquote cite="https://www.facebook.com/profile.php?id=100086482940323" class="fb-xfbml-parse-ignore">
                                        <a href="https://www.facebook.com/profile.php?id=100086482940323">Cantores Hermanos del Sr. Sto. Niño Choir</a>
                                    </blockquote>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>    

    {{-- Featured Videos Section --}}
    <section class="mb-10 overflow-hidden rounded-3xl border border-[var(--color-border)] bg-[var(--color-surface)] shadow-sm">
        <div class="p-6 md:p-10">
            <div class="flex flex-col gap-6">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Featured Videos</h2>
                    <p class="mt-2 text-base text-slate-600">Watch some of our most memorable performances and special celebrations.</p>
                </div>
                <div class="flex flex-col gap-12">
                    {{-- Video 1 --}}
                    <div class="flex flex-col gap-4">
                        <div class="aspect-video w-full overflow-hidden rounded-2xl bg-black shadow-lg">
                            <iframe 
                                class="h-full w-full"
                                src="https://www.youtube.com/embed/pekMbuis-AE?start=323" 
                                title="25th Anniversary Special: History and Evolution" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                allowfullscreen>
                            </iframe>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">25th Anniversary Special: History and Evolution</h3>
                            <p class="mt-2 text-sm text-slate-600">
                                A comprehensive look at the Cantores Hermanos del Sr. Sto. Niño Choir's 25-year journey, highlighting its growth, mission, and dedication to liturgical service since its founding in 1999.
                            </p>
                        </div>
                    </div>

                    {{-- Video 2 --}}
                    <div class="flex flex-col gap-4">
                        <div class="aspect-video w-full overflow-hidden rounded-2xl bg-black shadow-lg">
                            <iframe 
                                class="h-full w-full"
                                src="https://www.youtube.com/embed/9mJ-C60ncqU" 
                                title="Ama Namin (Arranged by Tercilo Lico)" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                allowfullscreen>
                            </iframe>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">Ama Namin (Arranged by Tercilo Lico)</h3>
                            <p class="mt-2 text-sm text-slate-600">
                                A powerful rendition of "Ama Namin" from the choir's 25th Anniversary album. This arrangement by Tercilo L. Lico showcases the unique strength and resonance of the male voices, blending vocal prowess with deep spiritual devotion.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
 
    <section class="mt-10 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-3xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6 shadow-sm lg:col-span-2">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-slate-900">Choir History</h2>
                    <p class="mt-2 text-sm text-slate-700">
                        Rooted in devotion and community, the choir has grown through years of shared service—supporting liturgical celebrations, feast-day traditions, and parish outreach.
                    </p>
                </div>
                <a href="{{ route('site.history') }}" class="shrink-0 rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] px-4 py-2 text-sm font-semibold text-slate-900 hover:bg-[var(--color-muted)]">
                    Read more
                </a>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="rounded-2xl bg-[var(--color-muted)] p-5 ring-1 ring-[var(--color-border)]">
                    <div class="text-sm font-semibold text-[var(--color-primary)]">Worship-first</div>
                    <div class="mt-1 text-sm text-slate-700">Reverent repertoire that supports prayer and participation.</div>
                </div>
                <div class="rounded-2xl bg-[var(--color-muted)] p-5 ring-1 ring-[var(--color-border)]">
                    <div class="text-sm font-semibold text-[var(--color-primary)]">Community-centered</div>
                    <div class="mt-1 text-sm text-slate-700">A welcoming family for singers of different voice parts.</div>
                </div>
            </div>
            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="rounded-2xl bg-[var(--color-muted)] p-5 ring-1 ring-[var(--color-border)]">
                    <div class="text-sm font-semibold text-[var(--color-primary)]">Faith-driven commitment</div>
                    <div class="mt-1 text-sm text-slate-700">Serving consistently with dedication, humility, and devotion to God.</div>
                </div>
                <div class="rounded-2xl bg-[var(--color-muted)] p-5 ring-1 ring-[var(--color-border)]">
                    <div class="text-sm font-semibold text-[var(--color-primary)]">Spirit-led service</div>
                    <div class="mt-1 text-sm text-slate-700">Guided by prayer and openness to the Holy Spirit in all rehearsals and performances.</div>
                </div>
            </div>            
        </div>

        <div class="rounded-3xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6 shadow-sm">
            <h2 class="text-xl font-semibold text-slate-900">Performances</h2>
            <p class="mt-2 text-sm text-slate-700">Highlights from recent services and celebrations.</p>
            <div class="mt-5 space-y-3">
                <a href="{{ route('site.performances') }}" class="block rounded-2xl bg-[var(--color-muted)] p-4 ring-1 ring-[var(--color-border)] hover:bg-white">
                    <div class="text-sm font-semibold text-slate-900">Audio & video library</div>
                    <div class="mt-1 text-sm text-slate-700">Recordings, choir specials, and featured hymns.</div>
                </a>
                <a href="{{ route('site.gallery') }}" class="block rounded-2xl bg-[var(--color-muted)] p-4 ring-1 ring-[var(--color-border)] hover:bg-white">
                    <div class="text-sm font-semibold text-slate-900">Photo galleries</div>
                    <div class="mt-1 text-sm text-slate-700">Events, rehearsals, and community moments.</div>
                </a>
            </div>
        </div>
    </section>

    <section class="mt-10 rounded-3xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-900">Member Profiles</h2>
                <p class="mt-2 text-sm text-slate-700">Meet the voices that serve the community.</p>
            </div>
            <a href="{{ route('site.members') }}" class="inline-flex items-center justify-center rounded-xl border border-[var(--color-border)] bg-[var(--color-surface)] px-4 py-2.5 text-sm font-semibold text-slate-900 hover:bg-[var(--color-muted)]">
                View all members
            </a>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @forelse ($members as $member)
                <div class="group overflow-hidden rounded-2xl bg-[var(--color-muted)] ring-1 ring-[var(--color-border)] hover:bg-white">
                    <div class="p-4">
                        @php($rolesLabel = $member->role?->name)
                        @php($voicesLabel = $member->voicePart?->name)
                        <div class="flex items-center gap-3">
                            <div class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-full bg-white ring-2 ring-[var(--color-accent)] shadow-sm">
                                @if ($member->photo_path)
                                    <img src="{{ asset('storage/' . $member->photo_path) }}" alt="{{ $member->name }}" class="h-full w-full rounded-full object-cover" loading="lazy" />
                                @else
                                    <div class="flex h-full w-full items-center justify-center bg-[var(--color-primary)] text-lg font-semibold text-[var(--color-on-primary)]">
                                        {{ mb_strtoupper(mb_substr($member->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <div class="truncate text-sm font-semibold text-slate-900">{{ $member->name }}</div>
                                <div class="mt-1 truncate text-sm text-slate-700">
                                    {{ $rolesLabel ?: ($voicesLabel ?: 'Member') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl bg-[var(--color-muted)] p-6 ring-1 ring-[var(--color-border)] sm:col-span-2 lg:col-span-4">
                    <div class="text-sm font-semibold text-slate-900">No members published yet</div>
                    <div class="mt-1 text-sm text-slate-700">
                        Use the admin panel to add member profiles and they will appear here automatically.
                    </div>
                </div>
            @endforelse
        </div>
    </section>

    {{-- Facebook SDK --}}
    <div id="fb-root"></div>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v17.0" nonce="home_fb_feed"></script>
@endsection
