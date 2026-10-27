@extends('layouts.admin.admin')

@section('title', 'Review Details')

@section('content')
    <section class="mz-view on requires-owner-manager" id="view-review-show" aria-label="Review detail view">
        <a href="{{ Route::has('admin.reviews.index') ? route('admin.reviews.index') : '#' }}" class="btn btn-quiet btn-sm mb-2">
            ← Back to reviews
        </a>

        <div class="view-head">
            <div>
                <h1>Review from {{ $review->client_name ?? 'Sofia M.' }}</h1>
                <p class="muted small mb-0">{{ $review->source ?? 'Google' }} · {{ $review->created_at?->format('M j, Y') ?? 'Oct 2, 2026' }}</p>
            </div>
            <div class="view-actions">
                <a href="{{ Route::has('admin.reviews.edit') ? route('admin.reviews.edit', $review->id ?? 1) : '#' }}" class="btn btn-accent btn-sm">
                    Edit review
                </a>
            </div>
        </div>

        <div class="detail-grid">
            <div>
                {{-- Review Body & Stars --}}
                <div class="tone section-gap">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="stars" style="font-size:16px;">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= ($review->rating ?? 5)) ★ @else <span class="off">★</span> @endif
                        @endfor
                        <span class="muted ms-1" style="font-size:13px; font-weight:600;">{{ $review->rating ?? 5 }} / 5</span>
                    </span>
                        <span class="badge bg-white text-dark border px-2 py-1 small">{{ $review->source ?? 'Google' }}</span>
                    </div>

                    <blockquote class="blockquote small mt-3 mb-0" style="font-size:14px; line-height:1.6;">
                        "{{ $review->review_text ?? 'Lila shaped my brows exactly how I asked. Quick, gentle, no redness after. Front desk texted me when they were ready.' }}"
                    </blockquote>

                    @if(!empty($review->owner_reply))
                        <div class="tone-2 mt-3 p-3">
                            <strong class="d-block small mb-1">Owner Reply:</strong>
                            <p class="small text-muted mb-0">{{ $review->owner_reply }}</p>
                        </div>
                    @endif
                </div>

                {{-- Linked Appointment Details --}}
                <div class="tone">
                    <h2>Associated visit</h2>
                    <div class="row g-2 small">
                        <div class="col-sm-6"><strong>Client:</strong> {{ $review->client_name ?? 'Sofia Mendez' }}</div>
                        <div class="col-sm-6"><strong>Staff:</strong> {{ $review->staff_name ?? 'Lila Karki' }}</div>
                        <div class="col-sm-6"><strong>Service:</strong> {{ $review->service_name ?? 'Eyebrow threading' }}</div>
                        <div class="col-sm-6"><strong>Visit date:</strong> {{ $review->appointment_date ?? 'Oct 2, 2026' }}</div>
                    </div>
                </div>
            </div>

            <div>
                {{-- Moderation Controls --}}
                <div class="tone section-gap">
                    <h3>Moderation status</h3>
                    <p class="small mb-2">
                        <strong>Status:</strong>
                        @php $st = $review->status ?? 'approved'; @endphp
                        @if($st === 'approved')
                            <span class="st st-confirmed"><i></i><span>Approved</span></span>
                        @elseif($st === 'pending')
                            <span class="st st-pending"><i></i><span>Pending moderation</span></span>
                        @else
                            <span class="st st-cancelled"><i></i><span>Hidden</span></span>
                        @endif
                    </p>

                    <div class="d-flex flex-column gap-2 mt-3">
                        @if(($review->status ?? 'approved') !== 'approved')
                            <form action="{{ Route::has('admin.reviews.approve') ? route('admin.reviews.approve', $review->id ?? 1) : '#' }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-ink btn-sm w-100">Approve & Publish</button>
                            </form>
                        @endif

                        @if(($review->status ?? 'approved') !== 'hidden')
                            <form action="{{ Route::has('admin.reviews.hide') ? route('admin.reviews.hide', $review->id ?? 1) : '#' }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline btn-sm w-100">Hide from website</button>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Request Log --}}
                <div class="policy-box">
                    <h3>Review request log</h3>
                    <p class="small mb-1"><strong>Trigger:</strong> Completed appointment</p>
                    <p class="small mb-1"><strong>Sent:</strong> {{ $review->request_sent_at ?? 'Oct 2, 2:30p' }}</p>
                    <p class="small mb-0"><strong>Status:</strong> <span class="st st-reviewed"><i></i><span>reviewed</span></span></p>
                </div>
            </div>
        </div>
    </section>
@endsection
