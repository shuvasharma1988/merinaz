@extends('layouts.admin.admin')

@section('title', 'Edit Gallery Media')

@section('content')
    <section class="mz-view on" id="view-gallery-edit" aria-label="Edit gallery media">
        <a href="{{ route('admin.gallery.index') }}" class="btn btn-quiet btn-sm mb-2">
            ← Back to gallery
        </a>

        <form action="{{ route('admin.gallery.update', $galleryItem) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="view-head">
                <div>
                    <h1>Edit gallery media</h1>
                    <p class="muted small mb-0">{{ $galleryItem->title }}</p>
                </div>
                <div class="view-actions">
                    <a href="{{ route('admin.gallery.index') }}" class="btn btn-outline btn-sm">Discard</a>
                    <button type="submit" class="btn btn-accent btn-sm">Save changes</button>
                </div>
            </div>

            <div class="detail-grid">
                @include('admin.gallery.partials._form', ['galleryItem' => $galleryItem, 'categories' => $categories])
            </div>
        </form>
    </section>
@endsection
