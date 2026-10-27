@php
    /** @var \App\Models\GalleryItem $item */
    $preview = $item->thumbnailUrl() ?? $item->imageUrl();
    $isVideo = $item->isVideo();
@endphp
<div class="col-6 col-md-4">
    <div class="g-img" role="button" tabindex="0"
         data-lightbox-index="{{ $loop->index ?? 0 }}"
         data-lightbox-src="{{ $preview }}"
         data-lightbox-alt="{{ $item->alt_text }}"
         data-lightbox-caption="{{ $item->caption ?: $item->title }}"
         aria-label="Enlarge: {{ $item->title }}">
        @if ($preview)
            <img src="{{ $preview }}" alt="{{ $item->alt_text }}" loading="lazy" width="800" height="600">
        @endif
        @if ($item->category)
            <span class="g-tag">{{ $item->category->name }}</span>
        @endif
        @if ($isVideo)
            <span class="g-video-badge"><i class="bi bi-play-circle"></i> Video</span>
        @endif
    </div>
</div>
