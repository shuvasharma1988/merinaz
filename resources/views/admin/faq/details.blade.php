@extends('layouts.admin.admin')

@section('title', 'FAQ Details')

@section('content')
    <section class="mz-view on" id="view-faq-show" aria-label="FAQ details">
        <a href="{{ route('admin.faq.index') }}" class="btn btn-quiet btn-sm mb-2">
            ← Back to FAQ
        </a>

        <div class="view-head">
            <div>
                <h1>{{ $faq->question }}</h1>
                <p class="muted small mb-0">
                    {{ $faq->category?->name ?? 'Ungrouped' }}
                    · order {{ $faq->sort_order }}
                </p>
            </div>
            <div class="view-actions">
                <a href="{{ route('admin.faq.edit', $faq) }}" class="btn btn-accent btn-sm">Edit answer</a>
            </div>
        </div>

        <div class="detail-grid">
            <div class="tone">
                <h3>Answer</h3>
                <div class="article-body faq-answer">{!! $faq->answer !!}</div>
            </div>

            <div>
                <div class="tone section-gap">
                    <h3>Details</h3>
                    <dl class="row small mb-0">
                        <dt class="col-5 text-muted fw-normal">Status</dt>
                        <dd class="col-7">{{ $faq->is_published ? 'Published' : 'Hidden' }}</dd>
                        <dt class="col-5 text-muted fw-normal">Group</dt>
                        <dd class="col-7">{{ $faq->category?->name ?? '—' }}</dd>
                        <dt class="col-5 text-muted fw-normal">Services</dt>
                        <dd class="col-7">{{ $faq->services->isNotEmpty() ? $faq->services->pluck('name')->join(', ') : '—' }}</dd>
                        <dt class="col-5 text-muted fw-normal">Created</dt>
                        <dd class="col-7">{{ $faq->created_at?->format('M j, Y') }}</dd>
                        <dt class="col-5 text-muted fw-normal">Updated</dt>
                        <dd class="col-7">{{ $faq->updated_at?->diffForHumans() }}</dd>
                    </dl>
                </div>

                <div class="policy-box">
                    <h3>Delete question</h3>
                    <p class="mb-2">Removes the entry from the public FAQ page and the structured data block.</p>
                    <form action="{{ route('admin.faq.destroy', $faq) }}" method="POST" onsubmit="return confirm('Delete this FAQ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger-line btn-sm w-100">Delete question</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
