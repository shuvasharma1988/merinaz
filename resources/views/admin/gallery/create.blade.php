@extends('layouts.admin.admin')

@section('title', 'Add Gallery Media')

@section('content')
    <section class="mz-view on" id="view-gallery-create" aria-label="Add gallery media">
        <a href="{{ route('admin.gallery.index') }}" class="btn btn-quiet btn-sm mb-2">
            ← Back to gallery
        </a>

        <form action="{{ route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="view-head">
                <div>
                    <h1>Add gallery media</h1>
                    <p class="muted small mb-0">Upload a photo or a short video from the studio</p>
                </div>
                <div class="view-actions">
                    <a href="{{ route('admin.gallery.index') }}" class="btn btn-outline btn-sm">Discard</a>
                    <button type="submit" class="btn btn-accent btn-sm">Save item</button>
                </div>
            </div>

            <div class="detail-grid">
                @include('admin.gallery.partials._form', ['galleryItem' => null, 'categories' => $categories])
            </div>
        </form>
    </section>
@endsection
