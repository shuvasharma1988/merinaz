@extends('layouts.admin.admin')

@section('title', 'Page - Merinaz Admin')

@section('content')
    <section class="mz-view on requires-owner-manager" id="view-page-show" aria-label="View page">
        <a href="{{ route('admin.pages.index') }}" class="btn btn-quiet btn-sm mb-2">
            ← Back to pages
        </a>

        <div class="view-head">
            <div>
                <h1>{{ $page->title }}</h1>
                <p class="muted small mb-0">Slug: <code>{{ $page->slug }}</code> · {{ $page->isProtectedSlug() ? 'Protected system page' : 'Standard page' }}</p>
            </div>
            <div class="view-actions">
                @can('update', $page)
                    <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-accent btn-sm">Edit Page</a>
                @endcan
            </div>
        </div>

        <div class="tone section-gap">
            <div class="mz-table-wrap">
                <table class="mz">
                    <tbody>
                    <tr>
                        <th style="width: 180px;">Title</th>
                        <td>{{ $page->title }}</td>
                    </tr>
                    <tr>
                        <th>Slug</th>
                        <td><code>{{ $page->slug }}</code></td>
                    </tr>
                    <tr>
                        <th>Protected</th>
                        <td>
                            @if ($page->isProtectedSlug())
                                <span class="badge bg-info text-dark">Yes</span>
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Body</th>
                        <td>
                            <div class="py-2">
                                {!! $page->body !!}
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
