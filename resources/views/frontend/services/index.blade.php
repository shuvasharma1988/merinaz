@extends('layouts.frontend.frontend')

@section('title', 'Services & Prices | Merinaz Adams Morgan')
@section('meta_description', 'Transparent pricing, realistic durations, and no surprise add-ons. Browse every waxing, threading, sugaring and skincare service at Merinaz in Adams Morgan, DC.')
@section('canonical', route('services.index'))

@section('content')
    <section class="container section-pad">
        <nav class="breadcrumb-mer mb-3" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Home</a><span aria-hidden="true">/</span><span aria-current="page">Services</span>
        </nav>

        <span class="eyebrow">Menu &amp; prices</span>
        <h1 class="display-5 fw-bold mt-2">Everything we do, done gently</h1>
        <p class="lead-soft col-lg-8">Transparent pricing, realistic durations, and no surprise add-ons. Every service includes consultation + aftercare balm.</p>

        @if ($categories->isNotEmpty())
            <div class="d-flex gap-2 flex-wrap my-4" role="group" aria-label="Service categories">
                <span class="filter-chip active">{{ $categories->count() }} categories</span>
                @foreach ($categories as $category)
                    <span class="filter-chip">{{ $category->name }}</span>
                @endforeach
            </div>
        @endif

        <div class="row g-3">
            @forelse ($services as $service)
                @include('frontend.partials.service-card', ['service' => $service])
            @empty
                <p class="text-muted">No services are published yet.</p>
            @endforelse
        </div>

        <div class="row g-3 mt-2">
            <div class="col-lg-4">
                <div class="p-4 rounded-4 h-100" style="background:var(--ink);color:#EDE2D3">
                    <h3 class="text-white">First visit?</h3>
                    <p class="small opacity-75">Get 15% off any threading or $10 off any Brazilian. Patch test always free.</p>
                    <a href="{{ route('contact') }}" class="btn btn-primary-mer btn-merinaz btn-sm-mer">Claim &amp; book</a>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="hygiene-strip p-4 h-100">
                    <h3 class="h5 fw-bold"><i class="bi bi-shield-check me-2"></i>What “hygienic” actually means here</h3>
                    <div class="row small text-muted">
                        <div class="col-md-4">✓ Fresh gloves + fresh stick, every dip</div>
                        <div class="col-md-4">✓ Hospital-grade autoclave for tools</div>
                        <div class="col-md-4">✓ Fresh linens + barrier film per guest</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
