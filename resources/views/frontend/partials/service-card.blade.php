@php
    /** @var \App\Models\Service $service */
    $image = $service->imageUrl();
@endphp
<div class="col-md-6 col-lg-4">
    <article class="m-card h-100">
        @if ($image)
            <img src="{{ $image }}" alt="{{ $service->name }}" class="card-img" loading="lazy" width="800" height="600">
        @endif
        <div class="p-3 p-md-4">
            <div class="d-flex gap-2 mb-2">
                @if ($service->category)
                    <span class="badge-soft">{{ $service->category->name }}</span>
                @endif
                <span class="badge-sand">{{ $service->duration_minutes }} min</span>
            </div>
            <h3 class="h5 fw-bold mb-1">{{ $service->name }}</h3>
            <p class="small text-muted mb-2">{{ $service->short_description }}</p>
            <div class="d-flex justify-content-between align-items-center mt-2">
                <strong class="serif fs-4">${{ number_format((float) $service->price, 2) }}</strong>
                <div class="d-flex gap-1">
                    <a href="{{ route('services.show', $service) }}" class="btn btn-light-mer btn-merinaz btn-sm-mer">Details</a>
                    <a href="{{ route('contact') }}" class="btn btn-primary-mer btn-merinaz btn-sm-mer">
                        <i class="bi bi-calendar-plus"></i> Book
                    </a>
                </div>
            </div>
        </div>
    </article>
</div>
