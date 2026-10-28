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
                <button class="filter-chip category-chip active" data-filter="All">All Categories</button>
                @foreach ($categories as $category)
                    <button class="filter-chip category-chip" data-filter="{{ $category->name }}">{{ $category->name }}</button>
                @endforeach
            </div>
        @endif

        <div class="row g-3" id="servicesGrid">
            @forelse ($services as $service)
                <div class="col-md-6 col-lg-4 service-item" data-category="{{ $service->category?->name ?? 'Uncategorized' }}">
                    {{-- ADDED: d-flex flex-column to force proper vertical stacking within the fixed card --}}
                    <article class="m-card h-100 d-flex flex-column">
                        @if ($service->imageUrl())
                            <img src="{{ $service->imageUrl() }}" alt="{{ $service->name }}" class="card-img flex-shrink-0" loading="lazy">
                        @endif

                        {{-- FIXED: Changed from h-100 to flex-grow-1 so it absorbs remaining space without overflowing --}}
                        <div class="p-3 p-md-4 d-flex flex-column flex-grow-1">
                            <div class="d-flex gap-2 mb-2">
                                <span class="badge-soft">{{ $service->category?->name }}</span>
                                <span class="badge-sand">{{ $service->duration_minutes }} min</span>
                            </div>
                            <h3 class="h5 fw-bold mb-1">{{ $service->name }}</h3>
                            <p class="small text-muted mb-3 flex-grow-1">{{ $service->short_description }}</p>

                            {{-- mt-auto ensures the price and buttons are always locked to the absolute bottom --}}
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <strong class="serif fs-4">${{ number_format((float) $service->price, 2) }}</strong>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('services.show', $service) }}" class="btn btn-light-mer btn-merinaz btn-sm-mer">Details</a>
                                    <a href="{{ route('booking.index') }}" class="btn btn-primary-mer btn-merinaz btn-sm-mer">
                                        <i class="bi bi-calendar-plus"></i> Book
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <p class="text-muted">No services are published yet.</p>
                </div>
            @endforelse
        </div>
    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const chips = document.querySelectorAll('.category-chip');
                const items = document.querySelectorAll('.service-item');
                const allChip = document.querySelector('[data-filter="All"]');

                chips.forEach(chip => {
                    chip.addEventListener('click', function() {
                        const isAll = this.dataset.filter === 'All';

                        if (isAll) {
                            chips.forEach(c => c.classList.remove('active'));
                            this.classList.add('active');
                            items.forEach(item => item.classList.remove('d-none'));
                        } else {
                            if (allChip) allChip.classList.remove('active');
                            this.classList.toggle('active');

                            const activeCategories = Array.from(document.querySelectorAll('.category-chip.active')).map(c => c.dataset.filter);

                            if (activeCategories.length === 0 && allChip) {
                                allChip.classList.add('active');
                                items.forEach(item => item.classList.remove('d-none'));
                            } else {
                                items.forEach(item => {
                                    if (activeCategories.includes(item.dataset.category)) {
                                        item.classList.remove('d-none');
                                    } else {
                                        item.classList.add('d-none');
                                    }
                                });
                            }
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
