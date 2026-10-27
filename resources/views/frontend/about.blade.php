@extends('layouts.frontend.frontend')

@section('title', 'About Merinaz | Adams Morgan, DC')
@section('meta_description', 'Merinaz is a family-run waxing, threading and skincare studio on 18th Street NW in Adams Morgan, Washington DC — calm, hygienic and unhurried since 2012.')
@section('canonical', route('about'))

@section('content')
    <section class="container section-pad">
        <nav class="breadcrumb-mer mb-3" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Home</a><span aria-hidden="true">/</span><span aria-current="page">About</span>
        </nav>

        <span class="eyebrow">Our story</span>
        <h1 class="display-5 fw-bold mt-2">A family studio built on trust, not trends</h1>

        @if ($page && $page->content)
            <div class="article-body mt-4">
                {!! $page->content !!}
            </div>
        @else
            <div class="article-body mt-4">
                <p class="lead-soft">At Merinaz, we are a passionate team of skilled professionals dedicated to offering superior hair removal services. With a rich background in various hair removal techniques, we specialize in providing personalized care that meets the unique needs of every client. Our goal is to help you feel confident, comfortable, and beautiful in your own skin, ensuring a seamless and effective treatment experience.
                    We believe in using gentle, yet precise methods that are especially ideal for sensitive skin. At Merinaz, each service is crafted with your well-being in mind, from the moment you walk in to the results you leave with. With attention to detail and a commitment to quality, we provide a welcoming space where your beauty is our priority.</p>
                <h2 class="h4 fw-bold mt-4">What we promise</h2>
                <ul>
                    <li>Single-use applicators and sticks — no double-dipping, ever.</li>
                    <li>Hospital-grade sterilization for every tool that touches skin.</li>
                    <li>Real ingredient lists on every aftercare product we hand you.</li>
                    <li>Prices posted up front, with no surprise add-ons at checkout.</li>
                </ul>
            </div>
        @endif

        <div class="row g-3 mt-4">
            <div class="col-6 col-lg-3">
                <div class="m-card p-4 text-center h-100">
                    <div class="serif fs-1 fw-bold">12+</div>
                    <small class="text-muted">years on 18th St</small>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="m-card p-4 text-center h-100">
                    <div class="serif fs-1 fw-bold">28k+</div>
                    <small class="text-muted">happy visits</small>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="m-card p-4 text-center h-100">
                    <div class="serif fs-1 fw-bold">4.6</div>
                    <small class="text-muted">average rating</small>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="m-card p-4 text-center h-100">
                    <div class="serif fs-1 fw-bold">98%</div>
                    <small class="text-muted">rebook again</small>
                </div>
            </div>
        </div>

        <div class="hygiene-strip p-4 mt-4 d-flex flex-wrap gap-3 align-items-center justify-content-between">
            <div>
                <h2 class="h5 fw-bold mb-1">Come see for yourself</h2>
                <p class="small text-muted mb-0">Same-day appointments are usually available — bring your questions, we'll answer them honestly.</p>
            </div>
            <a href="{{ route('booking.index') }}" class="btn btn-primary-mer btn-merinaz">Book a visit</a>
        </div>
    </section>
@endsection
