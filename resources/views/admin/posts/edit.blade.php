@extends('layouts.admin.admin')

@section('title', 'Edit Article')

@section('content')
    <section class="mz-view on" id="view-posts-edit" aria-label="Edit article">
        <a href="{{ route('admin.posts.index') }}" class="btn btn-quiet btn-sm mb-2">
            ← Back to articles
        </a>

        <form action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="view-head">
                <div>
                    <h1>Edit article</h1>
                    <p class="muted small mb-0">{{ $post->title }}</p>
                </div>
                <div class="view-actions">
                    <a href="{{ route('admin.posts.index') }}" class="btn btn-outline btn-sm">Discard</a>
                    <button type="submit" class="btn btn-accent btn-sm">Save changes</button>
                </div>
            </div>

            <div class="detail-grid">
                @include('admin.posts.partials._form', ['post' => $post, 'categories' => $categories])
            </div>
        </form>
    </section>
@endsection
