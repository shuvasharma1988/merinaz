@extends('layouts.admin.admin')

@section('title', 'Category Details')

@section('content')
    <section class="mz-view on requires-owner-manager" id="view-category-show" aria-label="Category detail view">
        <a href="{{ Route::has('admin.categories.index') ? route('admin.categories.index') : '#' }}" class="btn btn-quiet btn-sm mb-2">
            ← Back to categories
        </a>

        <div class="view-head">
            <div>
                <h1>{{ $category->name }}</h1>
                <p class="muted small mb-0">Slug: /{{ $category->slug }} · Contains {{ $category->services->count() }} services</p>
            </div>
            <div class="view-actions">
                <a href="{{ Route::has('admin.categories.edit') ? route('admin.categories.edit', $category->id) : '#' }}" class="btn btn-accent btn-sm">
                    Edit category
                </a>
            </div>
        </div>

        <div class="detail-grid">
            <div>
                <div class="tone section-gap">
                    <h2>Category overview</h2>
                    @if($category->image)
                        <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" class="img-fluid rounded mb-3" style="max-height: 200px;">
                    @endif
                    <p class="mb-3">{{ $category->description ?? 'No description provided.' }}</p>
                </div>

                <div class="tone section-gap">
                    <h2>Services in this category</h2>
                    <div class="mz-table-wrap mt-3">
                        <table class="mz">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Duration</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($category->services as $service)
                                <tr>
                                    <td>
                                        <strong>
                                            <a href="{{ Route::has('admin.services.show') ? route('admin.services.show', $service) : '#' }}" class="text-decoration-none text-dark">
                                                {{ $service->name }}
                                            </a>
                                        </strong>
                                    </td>
                                    <td>${{ $service->price }}</td>
                                    <td>{{ $service->duration_minutes }} min</td>
                                    <td>
                                        @if($service->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Hidden</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-muted text-center py-3">No services found in this category.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div>
                <div class="tone section-gap">
                    <h3>Visibility</h3>
                    <p class="small mb-1">
                        <strong>Status:</strong>
                        @if($category->is_active)
                            <span class="st st-confirmed"><i></i><span>Active on booking page</span></span>
                        @else
                            <span class="st st-cancelled"><i></i><span>Hidden</span></span>
                        @endif
                    </p>
                </div>

                <div class="tone section-gap mt-3">
                    <h3>Sorting</h3>
                    <p class="small mb-1">
                        <strong>Sort Order:</strong> {{ $category->sort_order }}
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection
