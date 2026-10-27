@extends('layouts.admin.admin')

@section('title', 'Service Details')

@section('content')
    <section class="mz-view on requires-owner-manager" id="view-service-show" aria-label="Service detail view">
        <a href="{{ Route::has('admin.services.index') ? route('admin.services.index') : '#' }}" class="btn btn-quiet btn-sm mb-2">
            ← Back to services
        </a>

        <div class="view-head">
            <div>
                <h1>{{ $service->name }}</h1>
                <p class="muted small mb-0">{{ $service->category->name ?? 'Uncategorized' }} · ${{ $service->price }} · {{ $service->duration_minutes }} min</p>
            </div>
            <div class="view-actions">
                <a href="{{ Route::has('admin.services.edit') ? route('admin.services.edit', $service->id) : '#' }}" class="btn btn-accent btn-sm">
                    Edit service
                </a>
            </div>
        </div>

        <div class="detail-grid">
            <div>
                <div class="tone section-gap">
                    <h2>Treatment overview</h2>
                    @if($service->image)
                        <img src="{{ Storage::url($service->image) }}" alt="{{ $service->name }}" class="img-fluid rounded mb-3" style="max-height: 200px;">
                    @endif
                    <p class="mb-3">{{ $service->description }}</p>
                    <div class="row g-2 small">
                        <div class="col-sm-4"><strong>Price:</strong> ${{ $service->price }}</div>
                        <div class="col-sm-4"><strong>Duration:</strong> {{ $service->duration_minutes }} min</div>
                        <div class="col-sm-4"><strong>Category:</strong> {{ $service->category->name ?? 'N/A' }}</div>
                    </div>
                </div>

                <div class="tone section-gap">
                    <h2>Assigned staff</h2>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        @forelse($service->staff ?? [] as $st)
                            <span class="badge bg-white text-dark border px-2 py-1">{{ $st->name }}</span>
                        @empty
                            <span class="text-muted small">No staff assigned to this service.</span>
                        @endforelse
                    </div>
                </div>
            </div>

            <div>
                <div class="tone section-gap">
                    <h3>Visibility</h3>
                    <p class="small mb-1">
                        <strong>Status:</strong>
                        @if($service->is_active)
                            <span class="st st-confirmed"><i></i><span>Active on booking page</span></span>
                        @else
                            <span class="st st-cancelled"><i></i><span>Hidden</span></span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection
