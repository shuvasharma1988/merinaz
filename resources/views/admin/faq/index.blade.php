@extends('layouts.admin.admin')

@section('title', 'FAQ')

@section('content')
    <section class="mz-view on" id="view-faq" aria-label="FAQ">
        <div class="view-head">
            <div>
                <h1>FAQ</h1>
                <p class="muted small mb-0">Grouped and reorderable · schema output updates automatically</p>
            </div>
            <div class="view-actions">
                <a href="{{ route('admin.faq.create') }}" class="btn btn-accent btn-sm">
                    Add question
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success py-2 px-3 small mb-3">{{ session('success') }}</div>
        @endif

        <form method="GET" action="{{ route('admin.faq.index') }}" class="row g-2 mb-3">
            <div class="col-md-4">
                <input class="form-control form-control-sm" type="search" name="search" value="{{ request('search') }}" placeholder="Search questions…">
            </div>
            <div class="col-md-3">
                <select class="form-select form-select-sm" name="category_id">
                    <option value="">All groups</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-start">
                <div class="form-check mt-1">
                    <input class="form-check-input" type="checkbox" id="faqUnpublished" name="unpublished" value="1" @checked(request()->boolean('unpublished'))>
                    <label class="form-check-label tiny" for="faqUnpublished">Hidden only</label>
                </div>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button class="btn btn-outline btn-sm" type="submit">Filter</button>
                <a href="{{ route('admin.faq.index') }}" class="btn btn-quiet btn-sm">Reset</a>
            </div>
        </form>

        <div class="detail-grid" style="grid-template-columns: 1fr 320px;">
            {{-- Left Column: FAQ groups and entries --}}
            <div class="tone">
                @forelse($faqs as $faq)
                    <div class="line-item">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <strong>{{ $faq->question }}</strong>
                            <span class="st st-{{ $faq->is_published ? 'published' : 'draft' }}"><i></i><span>{{ $faq->is_published ? 'Published' : 'Hidden' }}</span></span>
                        </div>
                        <div class="tiny muted mt-1">
                            {{ $faq->category?->name ?? 'Ungrouped' }}
                            · order {{ $faq->sort_order }}
                            @if($faq->services->isNotEmpty())
                                · {{ $faq->services->pluck('name')->join(', ') }}
                            @endif
                        </div>
                        <div class="small muted mt-2 faq-answer">{!! $faq->answer !!}</div>

                        <div class="d-flex gap-2 flex-wrap mt-2">
                            <a href="{{ route('admin.faq.show', $faq) }}" class="btn btn-quiet btn-sm">View</a>
                            <a href="{{ route('admin.faq.edit', $faq) }}" class="btn btn-outline btn-sm">Edit answer</a>
                            <form action="{{ route('admin.faq.destroy', $faq) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this FAQ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-quiet btn-sm text-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="small muted mb-0">No FAQ entries yet. Add the first question to publish it on the site.</p>
                @endforelse

                @if($faqs->hasPages())
                    <div class="mt-3">{{ $faqs->links() }}</div>
                @endif
            </div>

            {{-- Right Column: Structured Schema Preview --}}
            <div class="tone-2">
                @php
                    $schema = [
                        '@context' => 'https://schema.org',
                        '@type' => 'FAQPage',
                        'mainEntity' => $faqs->getCollection()
                            ->where('is_published', true)
                            ->map(fn ($faq) => [
                                '@type' => 'Question',
                                'name' => $faq->question,
                                'acceptedAnswer' => [
                                    '@type' => 'Answer',
                                    'text' => trim(strip_tags((string) $faq->answer)),
                                ],
                            ])
                            ->values()
                            ->all(),
                    ];
                @endphp
                <h3>Structured data</h3>
                <p class="small mb-2">FAQ schema is generated from the published entries. Search engines read this block for rich snippets.</p>
                <pre class="schema" id="faqSchema">{{ json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
                <button type="button" class="btn btn-outline btn-sm w-100" id="copySchemaBtn" data-toast="Schema copied to clipboard.">
                    Copy schema output
                </button>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.getElementById('copySchemaBtn')?.addEventListener('click', function() {
            const schema = document.getElementById('faqSchema')?.innerText;
            if (schema && navigator.clipboard) {
                navigator.clipboard.writeText(schema);
            }
        });
    </script>
@endpush
