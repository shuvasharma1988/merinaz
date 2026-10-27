@extends('layouts.admin.admin')

@section('title', 'Add Review')

@section('content')
    <section class="mz-view on requires-owner-manager" id="view-review-create" aria-label="Add review">
        <a href="{{ Route::has('admin.reviews.index') ? route('admin.reviews.index') : '#' }}" class="btn btn-quiet btn-sm mb-2">
            ← Back to reviews
        </a>

        <form action="{{ Route::has('admin.reviews.store') ? route('admin.reviews.store') : '#' }}" method="POST">
            @csrf

            <div class="view-head">
                <div>
                    <h1>Add manual review</h1>
                    <p class="muted small mb-0">Record feedback from in-studio cards, phone calls, or imported platforms</p>
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
                            <label class="form-label" for="revAuthor">Client / Reviewer name</label>
                            <input class="form-control @error('client_name') input-error @enderror"
                                   id="revAuthor"
                                   name="client_name"
                                   value="{{ old('client_name') }}"
                                   placeholder="e.g. Sofia M."
                                   required>
                            @error('client_name') <div class="field-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="revSource">Platform source</label>
                            <select class="form-select @error('source') input-error @enderror" id="revSource" name="source" required>
                                <option value="Direct" {{ old('source') == 'Direct' ? 'selected' : '' }}>Direct (In-studio)</option>
                                <option value="Google" {{ old('source') == 'Google' ? 'selected' : '' }}>Google</option>
                                <option value="Yelp" {{ old('source') == 'Yelp' ? 'selected' : '' }}>Yelp</option>
                                <option value="Booksy" {{ old('source') == 'Booksy' ? 'selected' : '' }}>Booksy</option>
                            </select>
                            @error('source') <div class="field-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="revRating">Rating</label>
                            <select class="form-select @error('rating') input-error @enderror" id="revRating" name="rating" required>
                                <option value="5" {{ old('rating', '5') == '5' ? 'selected' : '' }}>★★★★★ (5 of 5)</option>
                                <option value="4" {{ old('rating') == '4' ? 'selected' : '' }}>★★★★☆ (4 of 5)</option>
                                <option value="3" {{ old('rating') == '3' ? 'selected' : '' }}>★★★☆☆ (3 of 5)</option>
                                <option value="2" {{ old('rating') == '2' ? 'selected' : '' }}>★★☆☆☆ (2 of 5)</option>
                                <option value="1" {{ old('rating') == '1' ? 'selected' : '' }}>★☆☆☆☆ (1 of 5)</option>
                            </select>
                            @error('rating') <div class="field-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="revDate">Review date</label>
                            <input class="form-control @error('reviewed_at') input-error @enderror"
                                   id="revDate"
                                   name="reviewed_at"
                                   type="date"
                                   value="{{ old('reviewed_at', now()->format('Y-m-d')) }}">
                            @error('reviewed_at') <div class="field-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="revText">Review content</label>
                            <textarea class="form-control @error('review_text') input-error @enderror"
                                      id="revText"
                                      name="review_text"
                                      rows="4"
                                      placeholder="What did the client say about their visit..."
                                      required>{{ old('review_text') }}</textarea>
                            @error('review_text') <div class="field-error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <div class="tone section-gap">
                        <h3>Moderation</h3>
                        <div class="mb-3">
                            <label class="form-label" for="revStatus">Initial status</label>
                            <select class="form-select form-select-sm" id="revStatus" name="status">
                                <option value="approved" {{ old('status', 'approved') == 'approved' ? 'selected' : '' }}>Approved (Visible)</option>
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending moderation</option>
                                <option value="hidden" {{ old('status') == 'hidden' ? 'selected' : '' }}>Hidden from site</option>
                            </select>
                        </div>
                        <p class="tiny muted mb-0">Approved reviews can be displayed immediately in the website review widget.</p>
                    </div>
                </div>
            </div>
        </form>
    </section>
@endsection
