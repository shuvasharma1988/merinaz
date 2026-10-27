@php
    /** @var \App\Models\Post $post */
    $image = $post->featuredImageUrl();
@endphp
<div class="col-md-6 col-lg-4">
    <article class="m-card blog-card h-100">
        @if ($image)
            <img src="{{ $image }}" alt="{{ $post->featured_image_alt ?: $post->title }}" class="w-100" loading="lazy" width="800" height="500">
        @endif
        <div class="p-3">
            @if ($post->category)
                <span class="badge-sage">{{ $post->category->name }}</span>
            @endif
            <h3 class="h6 fw-bold mt-2">
                <a href="{{ route('blog.show', $post) }}" class="text-decoration-none text-reset">{{ $post->title }}</a>
            </h3>
            <p class="small text-muted">{{ $post->excerpt }}</p>
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">{{ $post->published_at?->format('M j, Y') }}</small>
                <a href="{{ route('blog.show', $post) }}" class="btn btn-light-mer btn-merinaz btn-sm-mer">Read</a>
            </div>
        </div>
    </article>
</div>
