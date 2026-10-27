@extends('layouts.admin.admin')

@section('title', 'Gallery')

@section('content')
    <section class="mz-view on" id="view-gallery" aria-label="Gallery">
        <div class="view-head">
            <div>
                <h1>Gallery</h1>
                <p class="muted small mb-0">Photos and short videos from the studio</p>
            </div>
            <div class="view-actions">
                <a href="{{ route('admin.gallery.create') }}" class="btn btn-accent btn-sm">
                    Add media
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success py-2 px-3 small mb-3">{{ session('success') }}</div>
        @endif

        <form method="GET" action="{{ route('admin.gallery.index') }}" class="row g-2 mb-3">
            <div class="col-md-4">
                <input class="form-control form-control-sm" type="search" name="search" value="{{ request('search') }}" placeholder="Search titles…">
            </div>
            <div class="col-md-3">
                <select class="form-select form-select-sm" name="media_type">
                    <option value="">All media types</option>
                    @foreach(\App\Enums\GalleryMediaType::cases() as $type)
                        <option value="{{ $type->value }}" @selected(request('media_type') === $type->value)>{{ $type->label() }}</option>
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
            <div class="col-md-2 d-flex gap-2 align-items-start">
                <div class="form-check mt-1">
                    <input class="form-check-input" type="checkbox" id="galleryUnpublished" name="unpublished" value="1" @checked(request()->boolean('unpublished'))>
                    <label class="form-check-label tiny" for="galleryUnpublished">Hidden only</label>
                </div>
                <button class="btn btn-outline btn-sm" type="submit">Filter</button>
            </div>
        </form>

        <div class="tone">
            @forelse($items as $item)
                <div class="line-item">
                    <div class="d-flex gap-3 align-items-start">
                        <div style="width:84px;flex:0 0 84px;">
                            @if($item->thumbnailUrl())
                                <img src="{{ $item->thumbnailUrl() }}" alt="{{ $item->alt_text }}" style="width:84px;height:84px;object-fit:cover;border-radius:12px">
                            @else
                                <div style="width:84px;height:84px;border-radius:12px;background:var(--mz-border);display:flex;align-items:center;justify-content:center;">
                                    <i class="bi bi-image"></i>
                                </div>
                            @endif
                        </div>

                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <strong>{{ $item->title }}</strong>
                                <span class="st st-{{ $item->is_published ? 'published' : 'draft' }}"><i></i><span>{{ $item->is_published ? 'Published' : 'Hidden' }}</span></span>
                            </div>
                            <div class="tiny muted mt-1">
                                {{ $item->category?->name ?? 'Uncategorised' }}
                                · {{ $item->media_type?->label() }}
                                @if($item->duration_seconds)
                                    · {{ $item->duration_seconds }}s
                                @endif
                                · order {{ $item->sort_order }}
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 flex-wrap mt-2">
                        <a href="{{ route('admin.gallery.show', $item) }}" class="btn btn-quiet btn-sm">View</a>
                        <a href="{{ route('admin.gallery.edit', $item) }}" class="btn btn-outline btn-sm">Edit</a>
                        @if($item->is_published)
                            <a href="{{ route('gallery.show', $item) }}" class="btn btn-ink btn-sm" target="_blank" rel="noopener">View on site</a>
                        @endif
                        <form action="{{ route('admin.gallery.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this gallery item and its files?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-quiet btn-sm text-danger">Delete</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="small muted mb-0">No gallery items yet. Add your first photo or video.</p>
            @endforelse
        </div>

        @if($items->hasPages())
            <div class="mt-3">{{ $items->links() }}</div>
        @endif
    </section>
@endsection
