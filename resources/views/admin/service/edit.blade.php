@extends('layouts.admin.admin')

@section('title', 'Edit Service')

@section('content')
    <section class="mz-view on requires-owner-manager" id="view-service-edit" aria-label="Edit service">
        <a href="{{ Route::has('admin.services.index') ? route('admin.services.index') : '#' }}" class="btn btn-quiet btn-sm mb-2" data-view="services">
            ← Back to services
        </a>

        <form action="{{ Route::has('admin.services.update') ? route('admin.services.update', $service->id) : '#' }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="view-head">
                <div>
                    <h1>Edit service · {{ $service->name }}</h1>
                    <p class="muted small mb-0">{{ $service->category->name ?? 'Waxing' }} · {{ $service->is_active ? 'live on booking page' : 'hidden' }}</p>
                </div>
                <div class="view-actions">
                    <a href="{{ Route::has('admin.services.index') ? route('admin.services.index') : '#' }}" class="btn btn-outline btn-sm">Discard changes</a>
                    <button type="submit" class="btn btn-accent btn-sm">Save service</button>
                </div>
            </div>

            <div class="detail-grid">
                <div class="tone">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="esName">Name</label>
                            <input class="form-control @error('name') input-error @enderror" id="esName" name="name" value="{{ old('name', $service->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="esSlug">Slug</label>
                            <input class="form-control @error('slug') input-error @enderror" id="esSlug" name="slug" value="{{ old('slug', $service->slug) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="esCat">Category</label>
                            <select class="form-select @error('category_id') input-error @enderror" id="esCat" name="category_id" required>
                                @foreach($categories ?? [] as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $service->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="esImage">Service Image</label>
                            <input type="file" class="form-control @error('image') input-error @enderror" id="esImage" name="image" accept="image/*">
                            @if($service->image)
                                <div class="mt-2">
                                    <span class="small text-muted d-block mb-1">Current image:</span>
                                    <img src="{{ Storage::url($service->image) }}" alt="{{ $service->name }}" style="max-height: 80px; border-radius: 4px;">
                                </div>
                            @endif
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="esDesc">Description</label>
                            <textarea class="form-control @error('description') input-error @enderror" id="esDesc" name="description" rows="3">{{ old('description', $service->description) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="esPrice">Price ($)</label>
                            <input class="form-control @error('price') input-error @enderror" id="esPrice" name="price" type="number" step="0.01" value="{{ old('price', $service->price) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="esDur">Duration</label>
                            <select class="form-select @error('duration_minutes') input-error @enderror" id="esDur" name="duration_minutes" required>
                                @foreach([15, 20, 30, 45, 60, 75, 90] as $dur)
                                    <option value="{{ $dur }}" {{ old('duration_minutes', $service->duration_minutes) == $dur ? 'selected' : '' }}>{{ $dur }} min</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <span class="form-label">Staff who perform it</span>
                            <div class="d-flex flex-wrap gap-3 small mt-1">
                                @php
                                    $assignedStaffIds = isset($service->staff) ? $service->staff->pluck('id')->toArray() : [];
                                @endphp
                                @foreach($staffList ?? [] as $st)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="staff_ids[]" value="{{ $st->id }}" id="esp_{{ $st->id }}" {{ in_array($st->id, old('staff_ids', $assignedStaffIds)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="esp_{{ $st->id }}">{{ $st->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="tone section-gap">
                        <h3>Visibility</h3>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" id="esActive" name="is_active" value="1" {{ old('is_active', $service->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="esActive">Active on booking page</label>
                        </div>
                    </div>
                    <div class="policy-box">
                        <h3>Delete service</h3>
                        <p class="mb-2">This removes it from the booking page. Past visits keep the name for records.</p>
                        <button type="button" class="btn btn-danger-line btn-sm w-100" onclick="event.preventDefault(); if(confirm('Delete this service?')) document.getElementById('delete-form').submit();">
                            Delete service
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <form id="delete-form" action="{{ route('admin.services.destroy', $service->id) }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    </section>
@endsection
