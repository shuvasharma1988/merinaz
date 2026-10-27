@extends('layouts.frontend.frontend')

@section('title', 'The Smooth Journal | Merinaz Adams Morgan')
@section('meta_description', 'Practical aftercare, honest product notes and calm beauty advice from the Merinaz team in Adams Morgan, DC.')
@section('canonical', route('blog.index'))

@section('content')
    <section class="container section-pad">
        <nav class="breadcrumb-mer mb-3" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Home</a><span aria-hidden="true">/</span><span aria-current="page">Journal</span>
        </nav>

        <span class="eyebrow">The smooth journal</span>
        <h1 class="display-5 fw-bold mt-2">Aftercare advice, minus the hype</h1>
        <p class="lead-soft col-lg-8">Short, useful reads on prep, aftercare and skin health — written by the people doing the work.</p>

        <div class="row g-4 mt-1">
            <div class="col-lg-3">
                <form method="GET" action="{{ route('blog.index') }}" class="m-card p-3">
                    <label for="blogSearch" class="form-label small fw-bold">Search articles</label>
                    <div class="input-group input-group-sm">
                        <input type="search" id="blogSearch" name="search" class="form-control" value="{{ request('search') }}"
                               placeholder="e.g. ingrown hairs">
                        <button class="btn btn-dark-mer" type="submit" aria-label="Search"><i class="bi bi-search"></i></button>
                    </div>

                    <hr class="my-3">

                    <span class="small fw-bold d-block mb-2">Topics</span>
                    <div class="d-grid gap-1">
                        <a href="{{ route('blog.index') }}" class="small {{ request('category') ? 'text-muted' : 'fw-bold' }}">All articles</a>
                        @foreach ($categories as $category)
                            <a href="{{ route('blog.index', ['category' => $category->slug]) }}"
                               class="small {{ request('category') === $category->slug ? 'fw-bold' : 'text-muted' }}">
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                </form>
            </div>

            <div class="col-lg-9">
                @if ($posts->isEmpty())
                    <div class="m-card p-5 text-center">
                        <i class="bi bi-journal-text fs-1 text-muted"></i>
                        <p class="text-muted mt-2 mb-0">No articles match that search yet.</p>
                    </div>
                @else
                    <div class="row g-3">
                        @foreach ($posts as $post)
                            @include('frontend.partials.blog-card', ['post' => $post])
                        @endforeach
                    </div>

                    <div class="mt-4">
                        {{ $posts->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
