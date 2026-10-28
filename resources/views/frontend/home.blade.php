@extends('layouts.frontend.frontend')

@section('title', 'Merinaz — Waxing, Threading, Sugaring & Skincare | Adams Morgan, DC')
@section('meta_description', 'Merinaz is Adams Morgan\'s calm, hygienic studio for waxing, threading, sugaring and skincare. Same-day appointments. Licensed estheticians.')
@section('canonical', route('home'))

@section('content')
    {{-- HERO --}}
    <section class="hero-wrap" aria-labelledby="heroTitle">
        <div class="container py-5 py-lg-5">
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <span class="badge-sage mb-3"><i class="bi bi-patch-check-fill me-1"></i> Licensed • Hygienic • Adams Morgan since 2017</span>
                    <h1 id="heroTitle" class="display-4 fw-bold mt-2">
                        Smooth skin, calm mind.
                        <span style="color:var(--accent-dark);font-style:italic">Expert care</span> without the rush.
                    </h1>
                    <p class="lead-soft mt-3">Merinaz is Washington DC's quiet sanctuary for waxing, threading, sugaring &amp; skincare. Single-use applicators, gentle technique, honest pricing — and same-day bookings that respect your time.</p>

                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <a href="{{ route('booking.index') }}" class="btn btn-primary-mer btn-merinaz">
                            <i class="bi bi-calendar-check"></i> Book Same-Day
                        </a>
                        <a href="{{ route('services.index') }}" class="btn btn-outline-mer btn-merinaz">
                            <i class="bi bi-grid"></i> View Services &amp; Prices
                        </a>
                    </div>

                    <div class="d-flex align-items-center gap-3 mt-3">
                        <div>
                            <span class="stars" aria-label="4.6 out of 5 stars">★★★★★</span><br>
                            <small class="text-muted"><strong>4.6</strong> · 248 verified reviews</small>
                        </div>
                        <div class="vr"></div>
                        <small class="text-muted">
                            <i class="bi bi-people"></i> All skin tones &amp; hair types welcomed
                        </small>
                    </div>
                </div>

                <div class="col-lg-6 position-relative">
                    <img src="{{ asset('images/banner.avif') }}"
                         class="hero-img-main"
                         alt="Calm, bright interior of Merinaz salon with treatment chair"
                         width="1000" height="520" fetchpriority="high">
                    <div class="float-card d-none d-md-flex" style="bottom:24px;right:12px">
                        <span class="icon-bubble icon-sage"><i class="bi bi-droplet-half"></i></span>
                        <div>
                            <strong class="d-block" style="font-size:.9rem">Hygiene Pledge</strong>
                            <small class="text-muted">Never double-dip · Single-use only</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- TRUST BAR --}}
    <section class="container" aria-label="Trust highlights">
        <div class="trust-bar p-3 p-md-4">
            <div class="row g-3 text-center">
                <div class="col-6 col-lg-3">
                    <i class="bi bi-award fs-3" style="color:var(--accent-dark)"></i>
                    <div class="fw-bold">8+ Years</div>
                    <small class="text-muted">Serving Adams Morgan</small>
                </div>
                <div class="col-6 col-lg-3">
                    <i class="bi bi-stars fs-3" style="color:var(--accent-dark)"></i>
                    <div class="fw-bold">4.6 / 5 Rating</div>
                    <small class="text-muted">248+ Google &amp; Yelp reviews</small>
                </div>
                <div class="col-6 col-lg-3">
                    <i class="bi bi-shield-plus fs-3" style="color:var(--sage)"></i>
                    <div class="fw-bold">Single-Use Only</div>
                    <small class="text-muted">Never double-dip, ever</small>
                </div>
                <div class="col-6 col-lg-3">
                    <i class="bi bi-heart-pulse fs-3" style="color:var(--sage)"></i>
                    <div class="fw-bold">All Skin Types</div>
                    <small class="text-muted">Trained in textured &amp; sensitive skin</small>
                </div>
            </div>
        </div>
    </section>

    {{-- SERVICES PREVIEW --}}
    <section class="section-pad container" aria-labelledby="svcPrev">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
            <div>
                <span class="eyebrow">Our specialties</span>
                <h2 id="svcPrev" class="display-6 fw-bold mt-2">Gentle services, honest prices</h2>
                <p class="lead-soft mb-0">Four crafts, one standard: meticulous, hygienic, unhurried.</p>
            </div>
            <a href="{{ route('services.index') }}" class="btn btn-outline-mer btn-merinaz">
                All services <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="row g-3">
            @forelse ($services as $service)
                @include('frontend.partials.service-card', ['service' => $service])
            @empty
                <p class="text-muted">Services are being updated — please check back shortly.</p>
            @endforelse
        </div>

        <div class="hygiene-strip p-3 p-md-4 mt-4 d-flex flex-wrap gap-3 align-items-center">
            <span class="icon-bubble icon-sage"><i class="bi bi-clipboard2-check"></i></span>
            <div class="flex-grow-1">
                <strong>Every service includes our Calm-Comfort protocol:</strong>
                <span class="text-muted">patch test on request, hypoallergenic wax &amp; sugar paste, post-care balm, and aftercare text check-in.</span>
            </div>
            <a href="{{ route('faq.index') }}" class="btn btn-light-mer btn-merinaz btn-sm-mer">How we keep it hygienic</a>
        </div>
    </section>

    {{-- FEATURED REVIEWS --}}
    @if ($reviews->isNotEmpty())
        <section class="py-5" style="background:var(--cream-2);border-top:1px solid var(--line);border-bottom:1px solid var(--line)" aria-labelledby="featRev">
            <div class="container">
                <div class="text-center mb-4">
                    <span class="eyebrow justify-content-center">Loved by neighbors</span>
                    <h2 id="featRev" class="display-6 fw-bold mt-2">Featured reviews</h2>
                </div>

                <div id="homeCarousel" class="carousel slide" data-bs-ride="carousel" aria-label="Featured reviews carousel">
                    <div class="carousel-inner">
                        @foreach ($reviews as $index => $review)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <div class="mx-auto review-card text-center" style="max-width:720px">
                                    <div class="stars" aria-label="{{ $review->rating }} out of 5 stars">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</div>
                                    <p class="serif fs-5 mt-2">“{{ $review->body }}”</p>
                                    <div class="small text-muted">
                                        — {{ $review->reviewer_name }}@if ($review->service) · {{ $review->service->name }}@endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="d-flex justify-content-center gap-2 mt-4">
                        <button class="btn btn-light-mer btn-merinaz btn-sm-mer" data-bs-target="#homeCarousel" data-bs-slide="prev" aria-label="Previous review">
                            <i class="bi bi-arrow-left"></i>
                        </button>
                        <button class="btn btn-light-mer btn-merinaz btn-sm-mer" data-bs-target="#homeCarousel" data-bs-slide="next" aria-label="Next review">
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- ABOUT TEASER --}}
    <section class="section-pad container" aria-labelledby="aboutTeaser">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6">
                <img src="https://images.unsplash.com/photo-1600948836101-f9ffda59d250?q=80&w=1000&auto=format&fit=crop"
                     class="w-100"
                     style="border-radius:24px;height:440px;object-fit:cover;box-shadow:var(--shadow-lg)"
                     alt="Warm beige treatment room with eucalyptus and soft towels"
                     width="1000" height="440" loading="lazy">
            </div>
            <div class="col-lg-6">
                <span class="eyebrow">Our story</span>
                <h2 id="aboutTeaser" class="display-6 fw-bold mt-2">A family studio built on trust, not trends</h2>
                <p class="lead-soft">Founded by Lipika Chhetri in 2017, our Columbia road studio was designed to feel like a deep breath — quiet rooms, warm light, no upsells, no judgment. Just precise work and honest advice.</p>
                <div class="row g-3 my-3 text-center">
                    <div class="col-4"><div class="m-card p-3"><div class="serif fs-2 fw-bold">28k+</div><small class="text-muted">Happy Visits</small></div></div>
                    <div class="col-4"><div class="m-card p-3"><div class="serif fs-2 fw-bold">8+</div><small class="text-muted">Years on Columbia Road</small></div></div>
                    <div class="col-4"><div class="m-card p-3"><div class="serif fs-2 fw-bold">98%</div><small class="text-muted">Rebook Again</small></div></div>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('about') }}" class="btn btn-dark-mer btn-merinaz">Meet the team</a>
                    <a href="{{ route('gallery.index') }}" class="btn btn-light-mer btn-merinaz">See the space</a>
                </div>
            </div>
        </div>
    </section>

    {{-- LOCATION / HOURS SNAPSHOT --}}
    <section class="container pb-5" aria-labelledby="locSnap">
        <div class="m-card overflow-hidden">
            <div class="row g-0">
                <div class="col-lg-6 p-4 p-md-5">
                    <span class="badge-soft"><i class="bi bi-geo-alt"></i> Our Studios</span>
                    <h2 id="locSnap" class="fw-bold mt-3">Two sanctuaries in DC. Worlds calmer inside.</h2>

                    {{-- Branch Tabs with data-map-url --}}
                    <ul class="nav nav-pills gap-2 mt-3 mb-4" id="locationTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="filter-chip active" id="columbia-tab" data-bs-toggle="pill" data-bs-target="#columbia" type="button" role="tab" aria-controls="columbia" aria-selected="true" data-map-url="https://www.google.com/maps?q=1768+Columbia+Rd+NW,+Washington,+DC+20009&output=embed">Columbia Road</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="filter-chip" id="wisconsin-tab" data-bs-toggle="pill" data-bs-target="#wisconsin" type="button" role="tab" aria-controls="wisconsin" aria-selected="false" data-map-url="https://www.google.com/maps?q=3500+Wisconsin+Ave+NW,+Washington,+DC+20016&output=embed">Wisconsin Ave</button>
                        </li>
                    </ul>

                    {{-- Tab Content --}}
                    <div class="tab-content" id="locationTabsContent">
                        {{-- Location 1: Columbia Road --}}
                        <div class="tab-pane fade show active" id="columbia" role="tabpanel" aria-labelledby="columbia-tab">
                            <p class="mb-1 fw-bold">1768 Columbia Rd NW, Floor 2, Washington DC, 20009</p>
                            <p class="text-muted small"><i class="bi bi-telephone"></i> (202) 469-2869 · Street parking + garage at 1800 Columbia</p>
                            <div class="row g-2 mt-2">
                                <div class="col-6">
                                    <div class="p-3 rounded-4" style="background:var(--cream-2);border:1px solid var(--line)">
                                        <small class="text-muted d-block">Mon – Sat</small><strong>10 AM – 8 PM</strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 rounded-4" style="background:var(--cream-2);border:1px solid var(--line)">
                                        <small class="text-muted d-block">Sun</small><strong>10 AM – 7 PM</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Location 2: Wisconsin Avenue --}}
                        <div class="tab-pane fade" id="wisconsin" role="tabpanel" aria-labelledby="wisconsin-tab">
                            <p class="mb-1 fw-bold">3500 Wisconsin Avenue NW, Suite A, Washington, DC 20016</p>
                            <p class="text-muted small"><i class="bi bi-telephone"></i> (202) 804-4185 · Tenleytown Metro (Red) · Street parking</p>
                            <div class="row g-2 mt-2">
                                <div class="col-6">
                                    <div class="p-3 rounded-4" style="background:var(--cream-2);border:1px solid var(--line)">
                                        <small class="text-muted d-block">Mon – Sat</small><strong>10 AM – 8 PM</strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 rounded-4" style="background:var(--cream-2);border:1px solid var(--line)">
                                        <small class="text-muted d-block">Sun</small><strong>10 AM – 7 PM</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4 flex-wrap">
                        <a href="{{ route('booking.index') }}" class="btn btn-primary-mer btn-merinaz">Book a visit</a>
                        <a href="{{ route('contact') }}" class="btn btn-outline-mer btn-merinaz">Directions &amp; contact</a>
                    </div>
                </div>

                <div class="col-lg-6">
                    {{-- Added id="dynamicMap" --}}
                    <iframe id="dynamicMap" title="Map showing Merinaz location" class="map-frame rounded-0 h-100 w-100" style="min-height:340px"
                            src="https://www.google.com/maps?q=1768+Columbia+Rd+NW,+Washington,+DC+20009&output=embed" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </section>

    {{-- BLOG PREVIEW --}}
    <section class="container pb-5" aria-labelledby="blogPrev">
        <div class="d-flex justify-content-between align-items-end mb-3 flex-wrap gap-2">
            <div>
                <span class="eyebrow">The Smooth Journal</span>
                <h2 id="blogPrev" class="fw-bold mt-2">Aftercare wisdom, no fluff</h2>
            </div>
            <a href="{{ route('blog.index') }}" class="btn btn-light-mer btn-merinaz btn-sm-mer">All articles</a>
        </div>

        <div class="row g-3">
            @forelse ($posts as $post)
                @include('frontend.partials.blog-card', ['post' => $post])
            @empty
                <p class="text-muted">No articles published yet.</p>
            @endforelse
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mapIframe = document.getElementById('dynamicMap');
            const tabs = document.querySelectorAll('button[data-bs-toggle="pill"]');

            tabs.forEach(tab => {
                tab.addEventListener('shown.bs.tab', function (event) {
                    const mapUrl = event.target.getAttribute('data-map-url');
                    if (mapUrl && mapIframe) {
                        mapIframe.src = mapUrl;
                    }
                });
            });
        });
    </script>
@endpush
