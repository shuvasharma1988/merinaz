@extends('layouts.admin.admin')

@section('title', 'Service Categories')

@section('content')
    <section class="mz-view on requires-owner-manager" id="view-categories" aria-label="Categories">
        <div class="view-head">
            <div>
                <h1>Categories</h1>
                <p class="muted small mb-0">Manage service groupings · drag to reorder</p>
            </div>
            <div class="view-actions">
                <a href="{{ Route::has('admin.services.index') ? route('admin.services.index') : '#' }}" class="btn btn-outline btn-sm">
                    Back to services
                </a>
                <a href="{{ Route::has('admin.categories.create') ? route('admin.categories.create') : '#' }}" class="btn btn-accent btn-sm">
                    Add category
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success py-2 px-3 small mb-3">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger py-2 px-3 small mb-3">{{ session('error') }}</div>
        @endif

        <div class="mz-table-wrap section-gap">
            <table class="mz">
                <thead>
                <tr>
                    <th style="width: 70px;">Order</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Services</th>
                    <th>Active</th>
                    <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td>
                            <span class="reorder-btns">
                                <button type="button" class="icon-btn" aria-label="Move up">↑</button>
                                <button type="button" class="icon-btn" aria-label="Move down">↓</button>
                            </span>
                        </td>
                        <td>
                            @if($category->image)
                                <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                            @else
                                <div style="width: 40px; height: 40px; background: #eee; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #aaa; font-size: 10px;">None</div>
                            @endif
                        </td>
                        <td>
                            <strong>
                                <a href="{{ Route::has('admin.categories.show') ? route('admin.categories.show', $category) : '#' }}" class="text-decoration-none text-dark">
                                    {{ $category->name }}
                                </a>
                            </strong>
                        </td>
                        <td class="text-muted small">{{ $category->slug }}</td>
                        <td>{{ $category->services_count ?? 0 }}</td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" {{ $category->is_active ? 'checked' : '' }} disabled>
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ Route::has('admin.categories.show') ? route('admin.categories.show', $category) : '#' }}" class="btn btn-quiet btn-sm">View</a>
                                <a href="{{ Route::has('admin.categories.edit') ? route('admin.categories.edit', $category) : '#' }}" class="btn btn-quiet btn-sm">Edit</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-muted text-center py-4">No categories created yet.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
