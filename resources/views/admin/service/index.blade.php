@extends('layouts.admin.admin')

@section('title', 'Services')

@section('content')
    <section class="mz-view on requires-owner-manager" id="view-services" aria-label="Services">
        <div class="view-head">
            <div>
                <h1>Services</h1>
                <p class="muted small mb-0">Grouped by category · drag order with up and down</p>
            </div>
            <div class="view-actions">
                <a href="{{ Route::has('admin.categories.index') ? route('admin.categories.index') : '#' }}" class="btn btn-outline btn-sm" data-view="categories">
                    Manage categories
                </a>
                <a href="{{ Route::has('admin.services.create') ? route('admin.services.create') : '#' }}" class="btn btn-accent btn-sm" data-view="service-create">
                    Add service
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success py-2 px-3 small mb-3">{{ session('success') }}</div>
        @endif

        @php
            $displayGroups = isset($categories) && count($categories) ? $categories : null;
        @endphp

        @if($displayGroups)
            @foreach($displayGroups as $category)
                <h2 class="mt-2">{{ $category->name }} · {{ $category->services->count() }} services</h2>
                <div class="mz-table-wrap section-gap">
                    <table class="mz">
                        <thead>
                        <tr>
                            <th style="width: 70px;">Order</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Duration</th>
                            <th>Staff</th>
                            <th>Active</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($category->services as $service)
                            <tr>
                                <td>
                                    <span class="reorder-btns">
                                        <button type="button" class="icon-btn" aria-label="Move up">↑</button>
                                        <button type="button" class="icon-btn" aria-label="Move down">↓</button>
                                    </span>
                                </td>
                                <td>
                                    @if($service->image)
                                        <img src="{{ Storage::url($service->image) }}" alt="{{ $service->name }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                    @else
                                        <div style="width: 40px; height: 40px; background: #eee; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #aaa; font-size: 10px;">None</div>
                                    @endif
                                </td>
                                <td>
                                    <strong>
                                        <a href="{{ Route::has('admin.services.show') ? route('admin.services.show', $service) : '#' }}" class="text-decoration-none text-dark">
                                            {{ $service->name }}
                                        </a>
                                    </strong>
                                </td>
                                <td>${{ $service->price }}</td>
                                <td>{{ $service->duration_minutes }} min</td>
                                <td>{{ $service->staff->pluck('name')->implode(', ') ?: 'Unassigned' }}</td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" {{ $service->is_active ? 'checked' : '' }} disabled>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ Route::has('admin.services.show') ? route('admin.services.show', $service) : '#' }}" class="btn btn-quiet btn-sm">View</a>
                                        <a href="{{ Route::has('admin.services.edit') ? route('admin.services.edit', $service) : '#' }}" class="btn btn-quiet btn-sm">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-muted text-center py-3">No services in this category yet.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            @endforeach
        @endif
    </section>
@endsection
