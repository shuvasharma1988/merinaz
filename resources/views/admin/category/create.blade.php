@extends('layouts.admin.admin')

@section('title', 'Add Category')

@section('content')
    <section class="mz-view on requires-owner-manager" id="view-category-create" aria-label="Add category">
        <a href="{{ Route::has('admin.categories.index') ? route('admin.categories.index') : '#' }}" class="btn btn-quiet btn-sm mb-2">
            ← Back to categories
        </a>

        <form action="{{ Route::has('admin.categories.store') ? route('admin.categories.store') : '#' }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="view-head">
                <div>
                    <h1>Add category</h1>
                    <p class="muted small mb-0">Group similar services together for the booking page</p>
                </div>
                <div class="view-actions">
                    <a href="{{ Route::has('admin.categories.index') ? route('admin.categories.index') : '#' }}" class="btn btn-outline btn-sm">Discard</a>
                    <button type="submit" class="btn btn-accent btn-sm">Save category</button>
                </div>
            </div>

            <div class="detail-grid">
                <div class="tone">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="ecName">Name</label>
                            <input class="form-control @error('name') input-error @enderror" id="ecName" name="name" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="ecSlug">Slug (URL)</label>
                            <input class="form-control @error('slug') input-error @enderror" id="ecSlug" name="slug" value="{{ old('slug') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="ecDesc">Description</label>
                            <textarea class="form-control @error('description') input-error @enderror" id="ecDesc" name="description" rows="3">{{ old('description') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="ecSortOrder">Sort Order</label>
                            <input class="form-control @error('sort_order') input-error @enderror" id="ecSortOrder" name="sort_order" type="number" value="{{ old('sort_order', 0) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="ecImage">Category Image</label>
                            <input type="file" class="form-control @error('image') input-error @enderror" id="ecImage" name="image" accept="image/*">
                        </div>
                    </div>
                </div>
                <div>
                    <div class="tone section-gap">
                        <h3>Visibility</h3>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" id="ecActive" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="ecActive">Active on booking page</label>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
@endsection
