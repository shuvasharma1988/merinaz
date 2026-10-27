@extends('layouts.admin.admin')

@section('title', 'Articles')

@section('content')
    <section class="mz-view on" id="view-posts" aria-label="Articles">
        <div class="view-head">
            <div>
                <h1>Articles</h1>
                <p class="muted small mb-0">The Smooth Journal · drafts, scheduling and SEO</p>
            </div>
            <div class="view-actions">
                <a href="{{ route('admin.posts.create') }}" class="btn btn-accent btn-sm">
                    New article
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success py-2 px-3 small mb-3">{{ session('success') }}</div>
        @endif

        <form method="GET" action="{{ route('admin.posts.index') }}" class="row g-2 mb-3">
            <div class="col-md-4">
                <input class="form-control form-control-sm" type="search" name="search" value="{{ request('search') }}" placeholder="Search titles…">
            </div>
            <div class="col-md-3">
                <select class="form-select form-select-sm" name="status">
                    <option value="">All statuses</option>
                    @foreach(\App\Enums\PostStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected(request('status') === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select form-select-sm" name="category_id">
                    <option value="">All categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-outline btn-sm" type="submit">Filter</button>
                <a href="{{ route('admin.posts.index') }}" class="btn btn-quiet btn-sm">Reset</a>
            </div>
        </form>

        <div class="tone">
            @forelse($posts as $post)
                <div class="line-item">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <strong>{{ $post->title }}</strong>
                            <div class="tiny muted mt-1">
                                {{ $post->category?->name ?? 'Uncategorised' }}
                                · {{ $post->author?->name ?? 'Unknown author' }}
                                · updated {{ $post->updated_at?->diffForHumans() }}
                            </div>
                        </div>
                        <span class="st st-{{ $post->status?->value }}"><i></i><span>{{ $post->status?->label() ?? 'Draft' }}</span></span>
                    </div>

                    <div class="tiny muted mt-1">
                        @if($post->published_at)
                            Publishes {{ $post->published_at->format('M j, Y g:i A') }}
                        @else
                            No publish date set
                        @endif
                        · {{ $post->slug }}
                    </div>

                    <div class="d-flex gap-2 flex-wrap mt-2">
                        <a href="{{ route('admin.posts.show', $post) }}" class="btn btn-quiet btn-sm">View</a>
                        <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-outline btn-sm">Edit</a>
                        @if($post->isPubliclyVisible())
                            <a href="{{ route('blog.show', $post) }}" class="btn btn-ink btn-sm" target="_blank" rel="noopener">View on site</a>
                        @endif
                        <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this article?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-quiet btn-sm text-danger">Delete</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="small muted mb-0">No articles yet. Create the first one to get the Journal started.</p>
            @endforelse
        </div>

        @if($posts->hasPages())
            <div class="mt-3">{{ $posts->links() }}</div>
        @endif
    </section>
@endsection
