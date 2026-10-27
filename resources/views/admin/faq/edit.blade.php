@extends('layouts.admin.admin')

@section('title', 'Edit FAQ')

@section('content')
    <section class="mz-view on" id="view-faq-edit" aria-label="Edit question">
        <a href="{{ route('admin.faq.index') }}" class="btn btn-quiet btn-sm mb-2">
            ← Back to FAQ
        </a>

        <form action="{{ route('admin.faq.update', $faq) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="view-head">
                <div>
                    <h1>Edit question</h1>
                    <p class="muted small mb-0">{{ $faq->question }}</p>
                </div>
                <div class="view-actions">
                    <a href="{{ route('admin.faq.index') }}" class="btn btn-outline btn-sm">Discard</a>
                    <button type="submit" class="btn btn-accent btn-sm">Save changes</button>
                </div>
            </div>

            <div class="detail-grid">
                @include('admin.faq.partials._form', ['faq' => $faq, 'categories' => $categories, 'services' => $services])
            </div>
        </form>
    </section>
@endsection
