@extends('layouts.admin.admin')

@section('title', 'Edit Review')

@section('content')
    <section class="mz-view on requires-owner-manager" id="view-review-edit" aria-label="Edit review">
        <a href="{{ Route::has('admin.reviews.index') ? route('admin.reviews.index') : '#' }}" class="btn btn-quiet btn-sm mb-2">
            ← Back to reviews
        </a>

        <form action="{{ Route::has('admin.reviews.update') ? route('admin.reviews.update', $review->id ?? 1) : '#' }}" method="POST">
            @csrf
            @method('PUT')

            <div class="view-head">
                <div>
                    <h1>Edit review · {{ $review->client_name ?? 'Sofia M.' }}</h1>
                    <p class="muted small mb-0">{{ $review->source ?? 'Google' }} · {{ $review->rating ?? 5 }} of 5 stars</p>
                </div>
                <div class="view-actions">
                    <a href="{{ Route::has('admin.reviews.index') ? route('admin.reviews.index') : '#' }}" class="btn btn-outline btn-sm">Discard</a>
                    <button type="submit" class="btn btn-accent btn-sm">Save review</button>
                </div>
            </div>

            <div class="detail-grid">
                <div class="tone">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="revAuthor">Client name</label>
                            <input class="form-control @error('client_name') input-error @enderror"
                                   id="revAuthor"
                                   name="client_name"
                                   value="{{ old('client_name', $review->client_name ?? 'Sofia M.') }}"
                                   required>
                            @error('client_name') <div class="field-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="revSource">Platform</label>
                            <select class="form-select @error('source') input-error @enderror" id="revSource" name="source" required>
                                @foreach(['Google', 'Yelp', 'Direct', 'Booksy'] as $src)
                                    <option value="{{ $src }}" {{ old('source', $review->source ?? 'Google') === $src ? 'selected' : '' }}>{{ $src }}</option>
                                @endforeach
                            </select>
                            @error('source') <div class="field-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="revRating">Rating</label>
                            <select class="form-select @error('rating') input-error @enderror" id="revRating" name="rating" required>
                                @for($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}" {{ old('rating', $review->rating ?? 5) == $i ? 'selected' : '' }}>
                                        {{ str_repeat('★', $i) . str_repeat('☆', 5 - $i) }} ({{ $i }} of 5)
                                    </option>
                                @endfor
                            </select>
                            @error('rating') <div class="field-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="revDate">Review date</label>
                            <input class="form-control"
                                   id="revDate"
                                   name="reviewed_at"
                                   type="date"
                                   value="{{ old('reviewed_at', isset($review->reviewed_at) ? $review->reviewed_at->format('Y-m-d') : '2026-10-02') }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="revText">Review text</label>
                            <textarea class="form-control @error('review_text') input-error @enderror"
                                      id="revText"
                                      name="review_text"
                                      rows="4"
                                      required>{{ old('review_text', $review->review_text ?? 'Lila shaped my brows exactly how I asked. Quick, gentle, no redness after. Front desk texted me when they were ready.') }}</textarea>
                            @error('review_text') <div class="field-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="revReply">Public owner reply (Optional)</label>
                            <textarea class="form-control"
                                      id="revReply"
                                      name="owner_reply"
                                      rows="3"
                                      placeholder="Write a response to be published beneath the review...">{{ old('owner_reply', $review->owner_reply ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="tone section-gap">
                        <h3>Moderation status</h3>
                        <label class="form-label" for="revStatus">Visibility</label>
                        <select class="form-select form-select-sm mb-3" id="revStatus" name="status">
                            <option value="approved" {{ old('status', $review->status ?? 'approved') === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="pending" {{ old('status', $review->status ?? '') === 'pending' ? 'selected' : '' }}>Pending review</option>
                            <option value="hidden" {{ old('status', $review->status ?? '') === 'hidden' ? 'selected' : '' }}>Hidden from site</option>
                        </select>
                        <p class="tiny muted mb-0">Hiding a review recalculates the public rating average without permanently losing the entry.</p>
                    </div>

                    <div class="policy-box">
                        <h3>Delete review</h3>
                        <p class="mb-2">Permanently remove this review from the admin panel and public widgets.</p>
                        <button type="button"
                                class="btn btn-danger-line btn-sm w-100"
                                data-confirm="Delete review|This removes the review permanently from the database. This cannot be undone.|Delete review">
                            Delete review
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </section>
@endsection
