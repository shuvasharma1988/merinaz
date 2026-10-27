@extends('layouts.frontend.frontend')

@section('title', 'FAQ | Merinaz Adams Morgan')
@section('meta_description', 'Answers about waxing, threading, sugaring, pricing, aftercare and hygiene at Merinaz in Adams Morgan, DC.')
@section('canonical', route('faq.index'))

@section('content')
    <section class="container section-pad">
        <nav class="breadcrumb-mer mb-3" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Home</a><span aria-hidden="true">/</span><span aria-current="page">FAQ</span>
        </nav>

        <span class="eyebrow">Good questions welcome</span>
        <h1 class="display-5 fw-bold mt-2">Frequently asked, honestly answered</h1>
        <p class="lead-soft col-lg-8">Prep, timing, pricing, aftercare. If it isn't here, text or call us — we'll tell you the truth, even if that means recommending a different service.</p>

        <div class="row g-4 mt-1">
            <div class="col-lg-4">
                <div class="m-card p-4">
                    <h2 class="h6 fw-bold mb-3">Browse by topic</h2>
                    <div class="d-grid gap-2">
                        @forelse ($groups as $group)
                            <a href="#faq-{{ $group['slug'] }}" class="small d-flex justify-content-between align-items-center">
                                <span>{{ $group['name'] }}</span>
                                <span class="badge-soft">{{ $group['faqs']->count() }}</span>
                            </a>
                        @empty
                            <span class="small text-muted">Topics coming soon.</span>
                        @endforelse
                    </div>

                    <hr>

                    <h2 class="h6 fw-bold mb-2">Still unsure?</h2>
                    <p class="small text-muted">Call <a href="tel:{{ \App\Models\SiteSetting::get('phone', '(202) 555-0148') }}">{{ \App\Models\SiteSetting::get('phone', '(202) 555-0148') }}</a> or send a note — we reply within a business day.</p>
                    <a href="{{ route('contact') }}" class="btn btn-outline-mer btn-merinaz btn-sm-mer w-100">Ask a question</a>
                </div>
            </div>

            <div class="col-lg-8">
                @forelse ($groups as $group)
                    <div class="mb-4" id="faq-{{ $group['slug'] }}">
                        <h2 class="h5 fw-bold mb-2">{{ $group['name'] }}</h2>
                        <div class="accordion">
                            @foreach ($group['faqs'] as $index => $faq)
                                <div class="accordion-item">
                                    <h3 class="accordion-header">
                                        <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }}" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#faq-{{ $faq->id }}"
                                                aria-expanded="{{ $index === 0 ? 'true' : 'false' }}">
                                            {{ $faq->question }}
                                        </button>
                                    </h3>
                                    <div id="faq-{{ $faq->id }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}">
                                        <div class="accordion-body small text-muted faq-answer">{!! $faq->answer !!}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="m-card p-5 text-center">
                        <i class="bi bi-question-circle fs-1 text-muted"></i>
                        <p class="text-muted mt-2 mb-0">FAQs are on the way — meanwhile, ask us anything.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
