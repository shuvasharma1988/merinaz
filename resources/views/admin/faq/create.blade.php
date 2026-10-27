@extends('layouts.admin.admin')

@section('title', 'Add FAQ')

@section('content')
    <section class="mz-view on" id="view-faq-create" aria-label="Add question">
        <a href="{{ route('admin.faq.index') }}" class="btn btn-quiet btn-sm mb-2">
            ← Back to FAQ
        </a>

        <form action="{{ route('admin.faq.store') }}" method="POST">
            @csrf

            <div class="view-head">
                <div>
                    <h1>Add question</h1>
                    <p class="muted small mb-0">Create new public question, answer, and category group</p>
                </div>
                <div class="view-actions">
                    <a href="{{ route('admin.faq.index') }}" class="btn btn-outline btn-sm">Discard</a>
                    <button type="submit" class="btn btn-accent btn-sm">Save question</button>
                </div>
            </div>

            <div class="detail-grid">
                @include('admin.faq.partials._form', ['faq' => null, 'categories' => $categories, 'services' => $services])
            </div>
        </form>
    </section>
@endsection
