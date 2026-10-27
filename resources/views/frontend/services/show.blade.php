@extends('layouts.frontend.frontend')

@php
    $metaDescription = $service->meta_description ?: $service->short_description;
@endphp

@section('title', $service->meta_title ?: $service->name.' | Merinaz')

@if ($metaDescription)
    @section('meta_description', $metaDescription)
@endif

@section('canonical', route('services.show', $service))

@section('og_type', 'product')
@section('og_title', $service->name)

@if ($service->short_description)
    @section('og_description', $service->short_description)
@endif

@push('jsonld')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $service->name,
            'description' => $service->short_description ?: strip_tags((string) $service->description),
            'serviceType' => $service->category?->name,
            'offers' => [
                '@type' => 'Offer',
                'price' => number_format((float) $service->price, 2, '.', ''),
                'priceCurrency' => 'USD',
                'availability' => 'https://schema.org/InStock',
            ],
            'provider' => [
                '@type' => 'BeautySalon',
                'name' => 'Merinaz',
                'telephone' => \App\Models\SiteSetting::get('phone', '(202) 555-0148'),
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => '2436 18th St NW',
                    'addressLocality' => 'Washington',
                    'addressRegion' => 'DC',
                    'postalCode' => '20009',
                    'addressCountry' => 'US',
                ],
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endpush

@section('content')
    <section class="container section-pad px-3 overflow-x-hidden" style="max-width: 100vw;">

        {{-- Breadcrumb --}}
        <nav class="breadcrumb-mer mb-4 d-flex align-items-center flex-nowrap overflow-hidden rounded-pill px-3 py-2 border bg-white" style="max-width: 100%;" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="text-decoration-none flex-shrink-0 text-dark small fw-bold">Home</a>
            <span aria-hidden="true" class="mx-2 text-muted flex-shrink-0">/</span>
            <a href="{{ route('services.index') }}" class="text-decoration-none flex-shrink-0 text-dark small fw-bold">Services</a>
            <span aria-hidden="true" class="mx-2 text-muted flex-shrink-0">/</span>
            <span aria-current="page" class="text-muted small text-truncate flex-grow-1 min-w-0">
                {{ $service->name }}
            </span>
        </nav>

        <div class="row g-4 align-items-start">
            {{-- Left: Image --}}
            <div class="col-lg-6">
                @if ($service->imageUrl())
                    <div class="rounded-4 overflow-hidden shadow-sm bg-white" style="width: 100%; max-width: 100%;">
                        <img src="{{ $service->imageUrl() }}" alt="{{ $service->name }}" class="img-fluid w-100" style="height: 440px; object-fit: cover; display: block;">
                    </div>
                @endif
            </div>

            {{-- Right: Details --}}
            <div class="col-lg-6">
                <span class="badge-soft d-inline-block">{{ $service->category?->name }} · {{ $service->duration_minutes }} min</span>

                <h1 class="display-5 fw-bold mt-3 mb-3 text-break" style="line-height: 1.1; word-break: break-word;">{{ $service->name }}</h1>

                {{-- Safe Rich Text Wrapper --}}
                <div class="lead-soft mb-4 text-break" style="max-width: 100%; word-break: break-word; overflow-wrap: break-word;">
                    {!! $service->description !!}
                </div>

                {{-- PRICE CARD: Fixed Desktop Overflow --}}
                <div class="m-card p-3 p-md-4 mb-4 d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3 overflow-hidden">

                    <div class="min-w-0 flex-grow-1">
                        <small class="text-muted d-block text-uppercase fw-bold mb-1" style="letter-spacing: 0.05em;">Transparent price</small>
                        <div class="d-flex flex-wrap align-items-baseline gap-2">
                            <span class="serif fs-2 fw-bold mb-0">${{ number_format((float) $service->price, 2) }}</span>
                        </div>
                    </div>

                    {{-- Buttons: Natural size on desktop, stacked flex on mobile --}}
                    <div class="d-flex flex-column flex-sm-row gap-2 flex-shrink-0 mt-2 mt-lg-0 w-100 w-lg-auto">
                        <a href="{{ route('contact') }}" class="btn btn-primary-mer btn-merinaz text-center px-4">
                            <i class="bi bi-calendar-check me-2"></i> Book this
                        </a>
                        <a href="{{ route('faq.index') }}" class="btn btn-light-mer btn-merinaz text-center px-4">
                            Questions?
                        </a>
                    </div>

                </div>

                {{-- Hygiene Strip --}}
                <div class="hygiene-strip p-3 small d-flex flex-column flex-sm-row gap-3 align-items-sm-center overflow-hidden w-100" style="max-width: 100%;">
                    <div class="icon-bubble icon-sage flex-shrink-0 align-self-start align-self-sm-center" style="width: 44px; height: 44px;">
                        <i class="bi bi-shield-check fs-5"></i>
                    </div>
                    <div class="min-w-0 flex-grow-1">
                        <strong class="d-block mb-1 text-dark">Included with your visit:</strong>
                        <span class="text-muted d-block">Consultation, patch test on request, soothing balm, and 48-hr check-in text.</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- FAQs and Related --}}
        @if ($service->faqs->isNotEmpty() || $related->isNotEmpty())
            <div class="row g-4 mt-4 mt-lg-5">

                {{-- FAQs Section --}}
                <div class="col-lg-7">
                    @if ($service->faqs->isNotEmpty())
                        <h2 class="fw-bold h3 mb-4">Service FAQs</h2>
                        <div class="accordion w-100" id="faqAccordion" style="max-width: 100%;">
                            @foreach ($service->faqs as $index => $faq)
                                <div class="accordion-item border rounded-4 overflow-hidden mb-3 shadow-sm bg-white">
                                    <h3 class="accordion-header">
                                        <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }} fw-bold text-start" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#service-faq-{{ $faq->id }}"
                                                aria-expanded="{{ $index === 0 ? 'true' : 'false' }}">
                                            {{ $faq->question }}
                                        </button>
                                    </h3>
                                    <div id="service-faq-{{ $faq->id }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body small text-muted border-top text-break">
                                            {!! $faq->answer !!}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Related Services Section --}}
                <div class="col-lg-5">
                    @if ($related->isNotEmpty())
                        <h2 class="fw-bold h3 mb-4">Pairs well with</h2>
                        <div class="d-grid gap-3 w-100" style="max-width: 100%;">
                            @foreach ($related as $relatedService)
                                <div class="m-card p-3 d-flex flex-column flex-sm-row gap-3 align-items-start align-items-sm-center overflow-hidden">

                                    <div class="d-flex gap-3 align-items-center flex-grow-1 min-w-0 w-100">
                                        @if ($relatedService->imageUrl())
                                            <img src="{{ $relatedService->imageUrl() }}" alt="{{ $relatedService->name }}"
                                                 class="rounded-3 flex-shrink-0" style="width: 64px; height: 64px; object-fit: cover;" loading="lazy">
                                        @endif

                                        <div class="flex-grow-1 min-w-0">
                                            <strong class="d-block text-truncate text-dark mb-1">{{ $relatedService->name }}</strong>
                                            <div class="d-flex align-items-center gap-1 small text-muted">
                                                <span class="fw-semibold text-dark">${{ number_format((float) $relatedService->price, 2) }}</span>
                                                <span>·</span>
                                                <span class="text-nowrap"><i class="bi bi-clock"></i> {{ $relatedService->duration_minutes }} min</span>
                                            </div>
                                        </div>
                                    </div>

                                    <a href="{{ route('services.show', $relatedService) }}" class="btn btn-light-mer btn-sm-mer rounded-pill px-4 flex-shrink-0 align-self-start align-self-sm-center w-100 w-sm-auto text-center">
                                        View
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>
        @endif
    </section>
@endsection
