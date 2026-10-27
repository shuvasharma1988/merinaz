@extends('layouts.admin.admin')

@section('title', 'Add Service')

@section('content')
    <section class="mz-view on requires-owner-manager" id="view-service-create" aria-label="Add service">
        <a href="{{ Route::has('admin.services.index') ? route('admin.services.index') : '#' }}" class="btn btn-quiet btn-sm mb-2" data-view="services">
            ← Back to services
        </a>

        <form action="{{ Route::has('admin.services.store') ? route('admin.services.store') : '#' }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="view-head">
                <div>
                    <h1>Add service</h1>
                    <p class="muted small mb-0">Create treatments, pricing, duration, and staff assignments</p>
                </div>
                <div class="view-actions">
                    <a href="{{ Route::has('admin.services.index') ? route('admin.services.index') : '#' }}" class="btn btn-outline btn-sm">Discard</a>
                    <button type="submit" class="btn btn-accent btn-sm">Save service</button>
                </div>
            </div>

            {{-- Catch-all for any hidden validation errors --}}
            @if ($errors->any())
                <div class="alert alert-danger py-2 px-3 small mb-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="detail-grid">
                <div class="tone">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="esName">Name</label>
                            <input class="form-control @error('name') input-error @enderror" id="esName" name="name" value="{{ old('name') }}" required>
                            @error('name') <div class="field-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="esSlug">Slug</label>
                            <input class="form-control @error('slug') input-error @enderror" id="esSlug" name="slug" value="{{ old('slug') }}" required>
                            @error('slug') <div class="field-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="esCat">Category</label>
                            <select class="form-select @error('category_id') input-error @enderror" id="esCat" name="category_id" required>
                                <option value="">Select Category</option>
                                @foreach($categories ?? [] as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <div class="field-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="esImage">Service Image</label>
                            <input type="file" class="form-control @error('image') input-error @enderror" id="esImage" name="image" accept="image/*">
                            @error('image') <div class="field-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="esDesc">Description</label>
                            <textarea class="form-control @error('description') input-error @enderror" id="esDesc" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description') <div class="field-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="esPrice">Price ($)</label>
                            <input class="form-control @error('price') input-error @enderror" id="esPrice" name="price" type="number" step="0.01" value="{{ old('price') }}" required>
                            @error('price') <div class="field-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="esDur">Duration</label>
                            <select class="form-select @error('duration_minutes') input-error @enderror" id="esDur" name="duration_minutes" required>
                                @foreach([15, 20, 30, 45, 60, 75, 90] as $dur)
                                    <option value="{{ $dur }}" {{ old('duration_minutes', 45) == $dur ? 'selected' : '' }}>{{ $dur }} min</option>
                                @endforeach
                            </select>
                            @error('duration_minutes') <div class="field-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <span class="form-label">Staff who perform it</span>
                            <div class="d-flex flex-wrap gap-3 small mt-1">
                                @foreach($staffList ?? [] as $st)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="staff_ids[]" value="{{ $st->id }}" id="staff_{{ $st->id }}" {{ in_array($st->id, old('staff_ids', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="staff_{{ $st->id }}">{{ $st->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                            @error('staff_ids') <div class="field-error text-danger small mt-1">{{ $message }}</div> @enderror
                            @error('staff_ids.*') <div class="field-error text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <div class="tone section-gap">
                        <h3>Visibility</h3>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" id="esActive" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="esActive">Active on booking page</label>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
@endsection
