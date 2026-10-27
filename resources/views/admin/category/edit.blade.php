@extends('layouts.admin.admin')

@section('title', 'Edit Category')

@section('content')
    <section class="mz-view on requires-owner-manager" id="view-category-edit" aria-label="Edit category">
        <a href="{{ Route::has('admin.categories.index') ? route('admin.categories.index') : '#' }}" class="btn btn-quiet btn-sm mb-2">
            ← Back to categories
        </a>

        <form action="{{ Route::has('admin.categories.update') ? route('admin.categories.update', $category->id) : '#' }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="view-head">
                <div>
                    <h1>Edit category · {{ $category->name }}</h1>
                    <p class="muted small mb-0">Update grouping details and visibility</p>
                </div>
                <div class="view-actions">
                    <a href="{{ Route::has('admin.categories.index') ? route('admin.categories.index') : '#' }}" class="btn btn-outline btn-sm">Discard changes</a>
                    <button type="submit" class="btn btn-accent btn-sm">Save category</button>
                </div>
            </div>

            <div class="detail-grid">
                <div class="tone">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="ecName">Name</label>
                            <input class="form-control @error('name') input-error @enderror" id="ecName" name="name" value="{{ old('name', $category->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="ecSlug">Slug (URL)</label>
                            <input class="form-control @error('slug') input-error @enderror" id="ecSlug" name="slug" value="{{ old('slug', $category->slug) }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="ecDesc">Description</label>
                            <textarea class="form-control @error('description') input-error @enderror" id="ecDesc" name="description" rows="3">{{ old('description', $category->description) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="ecSortOrder">Sort Order</label>
                            <input class="form-control @error('sort_order') input-error @enderror" id="ecSortOrder" name="sort_order" type="number" value="{{ old('sort_order', $category->sort_order) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="ecImage">Category Image</label>
                            <input type="file" class="form-control @error('image') input-error @enderror" id="ecImage" name="image" accept="image/*">
                            @if($category->image)
                                <div class="mt-2">
                                    <span class="small text-muted d-block mb-1">Current image:</span>
                                    <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" style="max-height: 80px; border-radius: 4px;">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div>
                    <div class="tone section-gap">
                        <h3>Visibility</h3>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" id="ecActive" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="ecActive">Active on booking page</label>
                        </div>
                    </div>
                    <div class="policy-box">
                        <h3>Delete category</h3>
                        <p class="mb-2">Removing a category requires deleting or reassigning all services inside it first.</p>
                        <button type="button" class="btn btn-danger-line btn-sm w-100" onclick="event.preventDefault(); if(confirm('Delete this category?')) document.getElementById('delete-form').submit();">
                            Delete category
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <form id="delete-form" action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    </section>
@endsection
