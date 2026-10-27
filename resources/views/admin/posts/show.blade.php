@extends('layouts.admin.admin')

@section('title', 'Article Preview')

@section('content')
    <section class="mz-view on" id="view-posts-show" aria-label="Article details">
        <a href="{{ route('admin.posts.index') }}" class="btn btn-quiet btn-sm mb-2">
            ← Back to articles
        </a>

        <div class="view-head">
            <div>
                <h1>{{ $post->title }}</h1>
                <p class="muted small mb-0">
                    {{ $post->category?->name ?? 'Uncategorised' }}
                    · by {{ $post->author?->name ?? 'Unknown author' }}
                    · updated {{ $post->updated_at?->diffForHumans() }}
                </p>
            </div>
            <div class="view-actions">
                @if($post->isPubliclyVisible())
                    <a href="{{ route('blog.show', $post) }}" class="btn btn-ink btn-sm" target="_blank" rel="noopener">View on site</a>
                @endif
                <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-accent btn-sm">Edit article</a>
            </div>
        </div>

        <div class="detail-grid">
            <div class="tone">
                @if($post->featuredImageUrl())
                    <img src="{{ $post->featuredImageUrl() }}" alt="{{ $post->featured_image_alt ?: $post->title }}" class="mb-3" style="max-width:100%;border-radius:16px">
                @endif

                @if($post->excerpt)
                    <p class="lead-soft">{{ $post->excerpt }}</p>
                @endif

                <div class="article-body">{!! $post->content !!}</div>
            </div>

            <div>
                <div class="tone section-gap">
                    <h3>Status</h3>
                    <span class="st st-{{ $post->status?->value }}"><i></i><span>{{ $post->status?->label() ?? 'Draft' }}</span></span>
                    <dl class="row small mt-3 mb-0">
                        <dt class="col-5 text-muted fw-normal">Slug</dt>
                        <dd class="col-7">{{ $post->slug }}</dd>
                        <dt class="col-5 text-muted fw-normal">Publish date</dt>
                        <dd class="col-7">{{ $post->published_at?->format('M j, Y g:i A') ?? '—' }}</dd>
                        <dt class="col-5 text-muted fw-normal">Created</dt>
                        <dd class="col-7">{{ $post->created_at?->format('M j, Y') }}</dd>
                    </dl>
                </div>

                <div class="tone section-gap">
                    <h3>Search &amp; social</h3>
                    <dl class="row small mb-0">
                        <dt class="col-5 text-muted fw-normal">Meta title</dt>
                        <dd class="col-7">{{ $post->meta_title ?: '—' }}</dd>
                        <dt class="col-5 text-muted fw-normal">Meta description</dt>
                        <dd class="col-7">{{ $post->meta_description ?: '—' }}</dd>
                        <dt class="col-5 text-muted fw-normal">Canonical</dt>
                        <dd class="col-7">{{ $post->canonical_url ?: '—' }}</dd>
                        <dt class="col-5 text-muted fw-normal">OG title</dt>
                        <dd class="col-7">{{ $post->og_title ?: '—' }}</dd>
                        <dt class="col-5 text-muted fw-normal">Twitter card</dt>
                        <dd class="col-7">{{ $post->twitter_card ?: '—' }}</dd>
                    </dl>
                </div>

                <div class="policy-box">
                    <h3>Delete article</h3>
                    <p class="mb-2">Removes the article and its uploaded images permanently.</p>
                    <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Delete this article?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger-line btn-sm w-100">Delete article</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
