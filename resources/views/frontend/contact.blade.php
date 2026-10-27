@extends('layouts.frontend.frontend')

@section('title', 'Book a Visit | Merinaz Adams Morgan')
@section('meta_description', 'Book waxing, threading, sugaring or skincare at Merinaz in Adams Morgan, DC. Same-day appointments, transparent prices, no double-dipping.')
@section('canonical', route('contact'))

@section('content')
    <section class="container section-pad">
        <nav class="breadcrumb-mer mb-3" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Home</a><span aria-hidden="true">/</span><span aria-current="page">Contact</span>
        </nav>

        <div class="row g-4">
            <div class="col-lg-6">
                <span class="eyebrow">Come by soon</span>
                <h1 class="display-5 fw-bold mt-2">Book your visit</h1>
                <p class="lead-soft">Tell us what you're after and when suits you. We reply within one business day — and same-day slots are often available.</p>

                <div class="m-card p-4 mt-3">
                    <h2 class="h6 fw-bold mb-3">Studio</h2>
                    <ul class="list-unstyled small text-muted mb-0 d-grid gap-2">
                        <li><i class="bi bi-geo-alt me-2"></i>{{ \App\Models\SiteSetting::get('address', '2436 18th St NW, Washington, DC 20009') }}</li>
                        <li><i class="bi bi-telephone me-2"></i><a href="tel:{{ \App\Models\SiteSetting::get('phone', '(202) 555-0148') }}">{{ \App\Models\SiteSetting::get('phone', '(202) 555-0148') }}</a></li>
                        <li><i class="bi bi-envelope me-2"></i><a href="mailto:{{ \App\Models\SiteSetting::get('email', 'hello@merinaz.com') }}">{{ \App\Models\SiteSetting::get('email', 'hello@merinaz.com') }}</a></li>
                        <li><i class="bi bi-clock me-2"></i>Mon–Sat 10:00 AM – 8:00 PM Sun 10:00 AM – 7:00 PM</li>
                    </ul>
                </div>

                <div class="hygiene-strip p-3 mt-3 small">
                    <i class="bi bi-shield-check me-1"></i>
                    <strong>First time?</strong> Patch test is always free, and we'll walk you through prep before you arrive.
                </div>
            </div>

            <div class="col-lg-6">
                <div class="m-card p-4">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <strong>We couldn't send that.</strong>
                            <ul class="mb-0 mt-1 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('contact.store') }}" class="d-grid gap-3" novalidate>
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label small fw-bold">Your name</label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}"
                                       class="form-control @error('name') is-invalid @enderror" required autocomplete="name">
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label small fw-bold">Email</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}"
                                       class="form-control @error('email') is-invalid @enderror" required autocomplete="email">
                                @error('email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="phone" class="form-label small fw-bold">Phone <span class="text-muted fw-normal">(optional)</span></label>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                                       class="form-control @error('phone') is-invalid @enderror" autocomplete="tel">
                                @error('phone')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="topic" class="form-label small fw-bold">What do you need?</label>
                                <select id="topic" name="topic" class="form-select">
                                    <option value="">Choose a service</option>
                                    @foreach (\App\Models\Service::query()->where('is_active', true)->orderBy('name')->get() as $option)
                                        <option value="{{ $option->name }}" @selected(old('topic') === $option->name)>{{ $option->name }}</option>
                                    @endforeach
                                    <option value="Other" @selected(old('topic') === 'Other')>Something else</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="message" class="form-label small fw-bold">Your message</label>
                            <textarea id="message" name="message" rows="5" required
                                      class="form-control @error('message') is-invalid @enderror"
                                      placeholder="Preferred days/times, anything we should know about your skin…">{{ old('message') }}</textarea>
                            @error('message')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary-mer btn-merinaz">
                            <i class="bi bi-send"></i> Send request
                        </button>

                        <p class="small text-muted mb-0">
                            We only use your details to reply to this request. See our <a href="{{ route('privacy') }}">privacy policy</a>.
                        </p>
                    </form>
                </div>
            </div>
        </div>

        <div class="m-card overflow-hidden mt-4">
            <iframe title="Map showing Merinaz location in Adams Morgan DC" class="map-frame rounded-0 w-100" style="min-height:340px"
                    src="https://www.google.com/maps?q=1768 Columbia Rd NW, Washington DC, 20009&output=embed" loading="lazy"></iframe>
        </div>
    </section>
@endsection
