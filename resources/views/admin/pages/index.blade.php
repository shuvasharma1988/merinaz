@extends('layouts.admin.admin')

@section('title', 'Pages - Merinaz Admin')

@section('content')
    <section class="mz-view on requires-owner-manager" id="view-pages" aria-label="Static pages">
        <div class="view-head">
            <div>
                <h1>Pages</h1>
                <p class="muted small mb-0">Manage static content for About Us, Terms of Service, Privacy Policy, and Cookie Policy</p>
            </div>
            <div class="view-actions">
                @can('create', App\Models\Page::class)
                    <a href="{{ route('admin.pages.create') }}" class="btn btn-accent btn-sm">New Page</a>
                @endcan
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success py-2 px-3 small mb-3" style="background:#eaf7e3; border-color:#c8e6c9; color:#2e7d32;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger py-2 px-3 small mb-3" style="background:#fee2e2; color:#991b1b;">
                {{ session('error') }}
            </div>
        @endif

        <div class="mz-table-wrap section-gap">
            <table class="mz">
                <thead>
                <tr>
                    <th scope="col">Title</th>
                    <th scope="col">Slug</th>
                    <th scope="col">Protected</th>
                    <th scope="col" class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($pages as $page)
                    <tr>
                        <td><strong>{{ $page->title }}</strong></td>
                        <td><code>{{ $page->slug }}</code></td>
                        <td>
                            @if ($page->isProtectedSlug())
                                <span class="badge bg-info text-dark">Yes</span>
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('admin.pages.show', $page) }}" class="btn btn-quiet btn-sm">View</a>
                                @can('update', $page)
                                    <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-quiet btn-sm">Edit</a>
                                @endcan
                                @can('delete', $page)
                                    @if (!$page->isProtectedSlug())
                                        <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this page?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-quiet btn-sm text-danger">Delete</button>
                                        </form>
                                    @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">No pages found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($pages, 'links'))
            <div class="mt-3">
                {{ $pages->links() }}
            </div>
        @endif
    </section>
@endsection
