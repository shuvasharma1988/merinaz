@extends('layouts.frontend.frontend')

@section('title', 'Gallery | Merinaz Adams Morgan')
@section('meta_description', 'A look inside Merinaz in Adams Morgan, DC — our treatment rooms, our work on real skin, and the calm, hygienic studio our guests love.')
@section('canonical', route('gallery.index'))

@section('content')
    <section class="container section-pad">
        <nav class="breadcrumb-mer mb-3" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Home</a><span aria-hidden="true">/</span><span aria-current="page">Gallery</span>
        </nav>

        <span class="eyebrow">The space &amp; the work</span>
        <h1 class="display-5 fw-bold mt-2">Real rooms, real results, real skin</h1>
        <p class="lead-soft col-lg-8">Every image here was taken in our 18th Street studio — no stock photography, no filters that hide what we actually do.</p>

        @if ($categories->isNotEmpty())
            <div class="d-flex gap-2 flex-wrap my-4">
                <span class="filter-chip active">{{ $items->count() }} items</span>
                @foreach ($categories as $category)
                    <span class="filter-chip">{{ $category->name }}</span>
                @endforeach
            </div>
        @endif

        @if ($items->isEmpty())
            <div class="m-card p-5 text-center">
                <i class="bi bi-images fs-1 text-muted"></i>
                <p class="text-muted mt-2 mb-0">Our gallery is being curated — check back soon.</p>
            </div>
        @else
            <div class="row g-2 g-md-3">
                @foreach ($items as $item)
                    @include('frontend.partials.gallery-item', ['item' => $item])
                @endforeach
            </div>
        @endif
    </section>
@endsection
