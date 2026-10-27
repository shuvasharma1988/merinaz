@extends('layouts.admin.admin')

@section('title', 'New Article')

@section('content')
    <section class="mz-view on" id="view-posts-create" aria-label="New article">
        <a href="{{ route('admin.posts.index') }}" class="btn btn-quiet btn-sm mb-2">
            ← Back to articles
        </a>

        <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="view-head">
                <div>
                    <h1>New article</h1>
                    <p class="muted small mb-0">Write once, publish to the Journal with full SEO control</p>
                </div>
                <div class="view-actions">
                    <a href="{{ route('admin.posts.index') }}" class="btn btn-outline btn-sm">Discard</a>
                    <button type="submit" class="btn btn-accent btn-sm">Save article</button>
                </div>
            </div>

            <div class="detail-grid">
                @include('admin.posts.partials._form', ['post' => null, 'categories' => $categories])
            </div>
        </form>
    </section>
@endsection
