@extends('layouts.frontend.frontend')

@php
    $metaDescription = $galleryItem->meta_description ?: $galleryItem->caption ?: $galleryItem->description;
    $ogImage = $galleryItem->thumbnailUrl();
@endphp

@section('title', $galleryItem->meta_title ?: $galleryItem->title.' | Merinaz Gallery')

@if ($metaDescription)
    @section('meta_description', $metaDescription)
@endif

@section('canonical', $galleryItem->canonical_url ?: route('gallery.show', $galleryItem))

@section('og_type', 'article')

@if ($ogImage)
    @section('og_image', $ogImage)
@endif

@section('content')
    <section class="container section-pad">
        <nav class="breadcrumb-mer mb-3" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Home</a><span aria-hidden="true">/</span>
            <a href="{{ route('gallery.index') }}">Gallery</a><span aria-hidden="true">/</span>
            <span aria-current="page">{{ $galleryItem->title }}</span>
        </nav>

        <div class="row g-4">
            <div class="col-lg-8">
                @if ($galleryItem->isVideo() && $galleryItem->videoUrl())
                    <video class="w-100 rounded-4" controls preload="metadata"
                           @if ($galleryItem->thumbnailUrl()) poster="{{ $galleryItem->thumbnailUrl() }}" @endif>
                        <source src="{{ $galleryItem->videoUrl() }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                @elseif ($galleryItem->imageUrl())
                    <img src="{{ $galleryItem->imageUrl() }}" alt="{{ $galleryItem->alt_text }}" class="w-100"
                         style="border-radius:24px;box-shadow:var(--shadow-lg)" width="1200" height="800">
                @endif

                @if ($galleryItem->description)
                    <p class="lead-soft mt-4">{!! $galleryItem->description !!}</p>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="m-card p-4">
                    @if ($galleryItem->category)
                        <span class="badge-soft">{{ $galleryItem->category->name }}</span>
                    @endif
                    <h1 class="h4 fw-bold mt-2">{{ $galleryItem->title }}</h1>
                    @if ($galleryItem->caption)
                        <p class="text-muted">{{ $galleryItem->caption }}</p>
                    @endif

                    <dl class="row small mb-0 mt-3">
                        <dt class="col-6 text-muted fw-normal">Type</dt>
                        <dd class="col-6">{{ $galleryItem->media_type->value }}</dd>
                        @if ($galleryItem->duration_seconds)
                            <dt class="col-6 text-muted fw-normal">Duration</dt>
                            <dd class="col-6">{{ $galleryItem->duration_seconds }}s</dd>
                        @endif
                    </dl>

                    <a href="{{ route('contact') }}" class="btn btn-primary-mer btn-merinaz w-100 mt-3">
                        <i class="bi bi-calendar-check"></i> Book a visit
                    </a>
                </div>
            </div>
        </div>

        @if ($related->isNotEmpty())
            <div class="mt-5">
                <h2 class="h5 fw-bold mb-3">More from the gallery</h2>
                <div class="row g-2 g-md-3">
                    @foreach ($related as $item)
                        @include('frontend.partials.gallery-item', ['item' => $item])
                    @endforeach
                </div>
            </div>
        @endif
    </section>
@endsection
