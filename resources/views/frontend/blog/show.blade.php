@extends('layouts.frontend.frontend')

@php
    $metaDescription = $post->meta_description ?: $post->excerpt;
    $ogDescription = $post->og_description ?: $post->excerpt;
    $ogImage = $post->ogImageUrl();
@endphp

@section('title', $post->meta_title ?: $post->title.' | The Smooth Journal')

@if ($metaDescription)
    @section('meta_description', $metaDescription)
@endif

@section('canonical', $post->canonical_url ?: route('blog.show', $post))

@section('og_type', 'article')
@section('og_title', $post->og_title ?: $post->title)

@if ($ogDescription)
    @section('og_description', $ogDescription)
@endif

@if ($ogImage)
    @section('og_image', $ogImage)
@endif

@section('content')
    <article class="container section-pad">
        <nav class="breadcrumb-mer mb-3" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Home</a><span aria-hidden="true">/</span>
            <a href="{{ route('blog.index') }}">Journal</a><span aria-hidden="true">/</span>
            <span aria-current="page">{{ $post->title }}</span>
        </nav>

        <div class="row g-4">
            <div class="col-lg-8 mx-auto">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    @if ($post->category)
                        <a href="{{ route('blog.index', ['category' => $post->category->slug]) }}" class="badge-sage">{{ $post->category->name }}</a>
                    @endif
                    <small class="text-muted">
                        {{ $post->published_at?->format('F j, Y') }}
                        @if ($post->author) · by {{ $post->author->name }} @endif
                    </small>
                </div>

                <h1 class="display-6 fw-bold mt-3">{{ $post->title }}</h1>

                @if ($post->excerpt)
                    <p class="lead-soft">{{ $post->excerpt }}</p>
                @endif

                @if ($post->featuredImageUrl())
                    <img src="{{ $post->featuredImageUrl() }}" alt="{{ $post->featured_image_alt ?: $post->title }}"
                         class="w-100 my-4" style="border-radius:24px;box-shadow:var(--shadow-lg)" width="1200" height="700">
                @endif

                <div class="article-body">
                    {!! $post->content !!}
                </div>

                <hr class="my-5">

                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
                    <a href="{{ route('blog.index') }}" class="btn btn-light-mer btn-merinaz btn-sm-mer">
                        <i class="bi bi-arrow-left"></i> All articles
                    </a>
                    <a href="{{ route('contact') }}" class="btn btn-primary-mer btn-merinaz btn-sm-mer">Book a visit</a>
                </div>
            </div>
        </div>

        @if ($related->isNotEmpty())
            <div class="mt-5">
                <h2 class="h5 fw-bold mb-3">Keep reading</h2>
                <div class="row g-3">
                    @foreach ($related as $relatedPost)
                        @include('frontend.partials.blog-card', ['post' => $relatedPost])
                    @endforeach
                </div>
            </div>
        @endif
    </article>
@endsection
