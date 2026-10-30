@extends('layouts.admin.admin')

@section('title', 'Reviews')

@section('content')
    <section class="mz-view on requires-owner-manager" id="view-reviews" aria-label="Reviews">
        <div class="view-head">
            <div>
                <h1>Reviews</h1>
                <p class="muted small mb-0">Moderation plus review request tracking · numbers match rows on this page</p>
            </div>
            <div class="view-actions">
                <a href="{{ Route::has('admin.reviews.create') ? route('admin.reviews.create') : '#' }}" class="btn btn-accent btn-sm">
                    Add manual review
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success py-2 px-3 small mb-3">{{ session('success') }}</div>
        @endif

        <div class="detail-grid" style="grid-template-columns: 300px 1fr;">
            {{-- Left Column: Summary & Rating Distribution --}}
            <div class="tone-2">
                <h2>Summary</h2>
                <div style="font-size:32px; font-weight:800;" id="revAvg">{{ $averageRating ?? '4.5' }}</div>
                <div class="small muted" id="revCount">{{ $totalReviews ?? '8' }} reviews shown</div>

                <div class="mt-3" id="revDist">
                    @php
                        $breakdown = $ratingBreakdown ?? [
                            5 => ['count' => 5, 'pct' => 62],
                            4 => ['count' => 2, 'pct' => 25],
                            3 => ['count' => 1, 'pct' => 12],
                            2 => ['count' => 0, 'pct' => 0],
                            1 => ['count' => 0, 'pct' => 0],
                        ];
                    @endphp
                    @foreach([5, 4, 3, 2, 1] as $r)
                        <div class="dist-row">
                            <span style="width:28px;">{{ $r }}</span>
                            <div class="dist-bar">
                                <div class="dist-fill" style="width:{{ $breakdown[$r]['pct'] }}%;"></div>
                            </div>
                            <span>{{ $breakdown[$r]['count'] }}</span>
                        </div>
                    @endforeach
                </div>

                <p class="tiny muted mt-2 mb-0">Average and count are computed from the list. Hiding or deleting updates them.</p>

               {{-- <div class="plain-divide">
                    <h3>Review requests</h3>
                    <p class="small mb-1">Sent after completed visits · 2 hours later</p>
                    <p class="small muted mb-0">Last 7 days: 22 sent · 9 opened · 6 reviewed</p>
                </div>--}}
            </div>

            {{-- Right Column: Filter Tabs & Review Cards --}}
            <div>
                <div class="mz-tabs">
                    <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="mz-tab {{ !request('status') ? 'on' : '' }}">
                        All · {{ $totalReviews ?? '8' }}
                    </a>
                    {{--<a href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}" class="mz-tab {{ request('status') === 'pending' ? 'on' : '' }}">
                        Pending · {{ $pendingCount ?? '2' }}
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'approved']) }}" class="mz-tab {{ request('status') === 'approved' ? 'on' : '' }}">
                        Approved · {{ $approvedCount ?? '5' }}
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['status' => 'hidden']) }}" class="mz-tab {{ request('status') === 'hidden' ? 'on' : '' }}">
                        Hidden · {{ $hiddenCount ?? '1' }}
                    </a>--}}
                </div>

                <div id="reviewsList">
                    @forelse($reviews ?? [] as $review)
                        <div class="line-item" data-rating="{{ $review->rating }}">
                            <div class="d-flex justify-content-between align-items-start">
                            <span class="stars">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating) ★ @else <span class="off">★</span> @endif
                                @endfor
                                <span class="muted" style="font-weight:500;">{{ $review->rating }} of 5</span>
                            </span>
                                <span class="small muted">{{ $review->source ?? 'Google' }} · {{ $review->created_at?->format('M j') }} · {{ $review->client_name }}</span>
                            </div>

                            <p class="small mt-2 mb-1">{{ $review->review_text }}</p>

                            <p class="tiny muted mb-2">
                                Review request:
                                <span class="st st-{{ $review->request_status ?? 'reviewed' }}">
                                <i></i><span>{{ $review->request_status ?? 'reviewed' }}</span>
                            </span>
                            </p>

                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ Route::has('admin.reviews.show') ? route('admin.reviews.show', $review) : '#' }}" class="btn btn-quiet btn-sm">View details</a>

                                @if(($review->status ?? 'pending') !== 'approved')
                                    <form action="{{ Route::has('admin.reviews.approve') ? route('admin.reviews.approve', $review) : '#' }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-ink btn-sm">Approve review</button>
                                    </form>
                                @endif

                                @if(($review->status ?? 'pending') !== 'hidden')
                                    <form action="{{ Route::has('admin.reviews.hide') ? route('admin.reviews.hide', $review) : '#' }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline btn-sm">Hide review</button>
                                    </form>
                                @endif

                                <a href="{{ Route::has('admin.reviews.edit') ? route('admin.reviews.edit', $review) : '#' }}" class="btn btn-outline btn-sm">Edit</a>

                                <form action="{{ Route::has('admin.reviews.destroy') ? route('admin.reviews.destroy', $review) : '#' }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this review completely?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-quiet btn-sm text-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        {{-- Prototype Static Fallbacks --}}
                        <div class="line-item" data-rating="5">
                            <div class="d-flex justify-content-between">
                                <span class="stars">★★★★★ <span class="muted" style="font-weight:500;">5 of 5</span></span>
                                <span class="small muted">Google · Oct 2 · Sofia M.</span>
                            </div>
                            <p class="small mt-1 mb-1">Lila shaped my brows exactly how I asked. Quick, gentle, no redness after. Front desk texted me when they were ready.</p>
                            <p class="tiny muted mb-2">Review request: <span class="st st-reviewed"><i></i><span>reviewed</span></span></p>
                            <div class="d-flex gap-2">
                                <button class="btn btn-ink btn-sm" data-toast="Review approved.">Approve review</button>
                                <button class="btn btn-outline btn-sm" data-toast="Review hidden from site.">Hide review</button>
                                <button class="btn btn-quiet btn-sm">Delete</button>
                            </div>
                        </div>
                        <div class="line-item" data-rating="3" style="border-color:var(--mz-border-strong);">
                            <div class="d-flex justify-content-between">
                                <span class="stars">★★★<span class="off">★★</span> <span class="muted" style="font-weight:500;">3 of 5 · pending</span></span>
                                <span class="small muted">Yelp · Sep 18 · Mei L.</span>
                            </div>
                            <p class="small mt-1 mb-1">Threading was good but room felt rushed near close. Would like more aftercare advice for redness.</p>
                            <p class="tiny muted mb-2">Review request: <span class="st st-opened"><i></i><span>opened</span></span></p>
                            <div class="d-flex gap-2">
                                <button class="btn btn-ink btn-sm" data-toast="Review approved.">Approve review</button>
                                <button class="btn btn-outline btn-sm" data-toast="Review hidden from site.">Hide review</button>
                                <button class="btn btn-quiet btn-sm">Delete</button>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@endsection
