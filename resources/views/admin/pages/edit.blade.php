@extends('layouts.admin.admin')

@section('title', 'Edit Page - Merinaz Admin')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
@endpush

@section('content')
    <section class="mz-view on requires-owner-manager" id="view-page-edit" aria-label="Edit page">
        <a href="{{ route('admin.pages.index') }}" class="btn btn-quiet btn-sm mb-2">
            ← Back to pages
        </a>
        @if ($errors->any())
            <div class="alert alert-danger py-2 px-3 small mb-3" style="background: #fee2e2; color: #991b1b;">
                <strong>Validation Failed:</strong>
                <ul class="mb-0 ps-3">
                        <?php foreach ($errors->all() as $error): ?>
                    <li>{{ $error }}</li>
                    <?php endforeach; ?>
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.pages.update', $page) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="view-head">
                <div>
                    <h1>Edit Page · {{ $page->title }}</h1>
                    <p class="muted small mb-0">
                        Slug: <code>{{ $page->slug }}</code> · {{ $page->isProtectedSlug() ? 'Protected system page' : 'Custom page' }}
                    </p>
                </div>
                <div class="view-actions">
                    <a href="{{ route('admin.pages.index') }}" class="btn btn-outline btn-sm">Discard</a>
                    <button type="submit" class="btn btn-accent btn-sm">Update Page</button>
                </div>
            </div>

            <div class="tone section-gap">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="title">Title *</label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $page->title) }}" required>
                        @error('title') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="slug">Slug *</label>
                        <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $page->slug) }}" {{ $page->isProtectedSlug() ? 'readonly' : 'required' }}>
                        @error('slug') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        @if($page->isProtectedSlug())
                            <small class="form-text text-muted">This is a protected system slug and cannot be modified.</small>
                        @else
                            <small class="form-text text-muted">Use a URL-friendly slug.</small>
                        @endif
                    </div>

                    <div class="col-12">
                        <label class="form-label" for="body">Body *</label>
                        <textarea name="body" id="body" class="form-control summernote @error('body') is-invalid @enderror" rows="10" required>{{ old('body', $page->body) }}</textarea>
                        @error('body') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </form>
    </section>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.jQuery && $('.summernote').length) {
                $('.summernote').summernote({
                    height: 300,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'italic', 'underline', 'clear']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['insert', ['link', 'picture']],
                        ['view', ['fullscreen', 'codeview']]
                    ]
                });
            }
        });
    </script>
@endpush
