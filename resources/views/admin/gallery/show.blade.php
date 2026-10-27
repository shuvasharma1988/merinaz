@extends('layouts.admin.admin')

@section('title', 'Gallery Item')

@section('content')
    <section class="mz-view on" id="view-gallery-show" aria-label="Gallery item details">
        <a href="{{ route('admin.gallery.index') }}" class="btn btn-quiet btn-sm mb-2">
            ← Back to gallery
        </a>

        <div class="view-head">
            <div>
                <h1>{{ $galleryItem->title }}</h1>
                <p class="muted small mb-0">
                    {{ $galleryItem->category?->name ?? 'Uncategorised' }}
                    · {{ $galleryItem->media_type?->label() }}
                </p>
            </div>
            <div class="view-actions">
                @if($galleryItem->is_published)
                    <a href="{{ route('gallery.show', $galleryItem) }}" class="btn btn-ink btn-sm" target="_blank" rel="noopener">View on site</a>
                @endif
                <a href="{{ route('admin.gallery.edit', $galleryItem) }}" class="btn btn-accent btn-sm">Edit item</a>
            </div>
        </div>

        <div class="detail-grid">
            <div class="tone">
                @if($galleryItem->isVideo() && $galleryItem->videoUrl())
                    <video src="{{ $galleryItem->videoUrl() }}" controls
                           @if($galleryItem->thumbnailUrl()) poster="{{ $galleryItem->thumbnailUrl() }}" @endif
                           class="w-100" style="border-radius:16px"></video>
                @elseif($galleryItem->imageUrl())
                    <img src="{{ $galleryItem->imageUrl() }}" alt="{{ $galleryItem->alt_text }}" class="w-100" style="border-radius:16px">
                @endif

                @if($galleryItem->caption)
                    <p class="small muted mt-2 mb-0">{{ $galleryItem->caption }}</p>
                @endif

                @if($galleryItem->description)
                    <div class="article-body mt-3">{!! $galleryItem->description !!}</div>
                @endif
            </div>

            <div>
                <div class="tone section-gap">
                    <h3>Details</h3>
                    <dl class="row small mb-0">
                        <dt class="col-5 text-muted fw-normal">Status</dt>
                        <dd class="col-7">{{ $galleryItem->is_published ? 'Published' : 'Hidden' }}</dd>
                        <dt class="col-5 text-muted fw-normal">Slug</dt>
                        <dd class="col-7">{{ $galleryItem->slug }}</dd>
                        <dt class="col-5 text-muted fw-normal">Alt text</dt>
                        <dd class="col-7">{{ $galleryItem->alt_text }}</dd>
                        <dt class="col-5 text-muted fw-normal">Duration</dt>
                        <dd class="col-7">{{ $galleryItem->duration_seconds ? $galleryItem->duration_seconds . 's' : '—' }}</dd>
                        <dt class="col-5 text-muted fw-normal">Display order</dt>
                        <dd class="col-7">{{ $galleryItem->sort_order }}</dd>
                        <dt class="col-5 text-muted fw-normal">Created</dt>
                        <dd class="col-7">{{ $galleryItem->created_at?->format('M j, Y') }}</dd>
                    </dl>
                </div>

                <div class="tone section-gap">
                    <h3>Files</h3>
                    <dl class="row small mb-0">
                        <dt class="col-5 text-muted fw-normal">Image</dt>
                        <dd class="col-7 text-break">{{ $galleryItem->image ?: '—' }}</dd>
                        <dt class="col-5 text-muted fw-normal">Video</dt>
                        <dd class="col-7 text-break">{{ $galleryItem->video ?: '—' }}</dd>
                        <dt class="col-5 text-muted fw-normal">Thumbnail</dt>
                        <dd class="col-7 text-break">{{ $galleryItem->thumbnail ?: '—' }}</dd>
                    </dl>
                </div>

                <div class="policy-box">
                    <h3>Delete item</h3>
                    <p class="mb-2">Removes the gallery entry and its uploaded files permanently.</p>
                    <form action="{{ route('admin.gallery.destroy', $galleryItem) }}" method="POST" onsubmit="return confirm('Delete this gallery item?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger-line btn-sm w-100">Delete item</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
