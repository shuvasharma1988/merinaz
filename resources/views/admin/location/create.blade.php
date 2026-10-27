@extends('layouts.admin.admin')

@section('title', 'Add Location')

@section('content')
    <section class="mz-view on" id="view-location-create" aria-label="Add location">
        <a href="{{ route('admin.locations.index') }}" class="btn btn-quiet btn-sm mb-2">
            ← Back to locations
        </a>

        <form action="{{ route('admin.locations.store') }}" method="POST">
            @csrf

            <div class="view-head">
                <div>
                    <h1>Add new location</h1>
                    <p class="muted small mb-0">Create a new salon branch and assign staff members</p>
                </div>
                <div class="view-actions">
                    <a href="{{ route('admin.locations.index') }}" class="btn btn-outline btn-sm">Discard</a>
                    <button type="submit" class="btn btn-accent btn-sm">Save location</button>
                </div>
            </div>

            <div class="detail-grid">
                <div>
                    <div class="tone section-gap">
                        <h2>Branch Details</h2>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" for="locName">Branch Name *</label>
                                <input class="form-control @error('name') is-invalid @enderror" id="locName" name="name" value="{{ old('name') }}" placeholder="e.g. Downtown Studio" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="locAddress">Full Address *</label>
                                <input class="form-control @error('address') is-invalid @enderror" id="locAddress" name="address" value="{{ old('address') }}" placeholder="e.g. 1724 Lanier Pl NW, Washington DC" required>
                                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="locPhone">Phone Number</label>
                                <input class="form-control @error('phone') is-invalid @enderror" id="locPhone" name="phone" value="{{ old('phone') }}">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="locEmail">Branch Email</label>
                                <input class="form-control @error('email') is-invalid @enderror" id="locEmail" name="email" type="email" value="{{ old('email') }}">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="tone section-gap">
                        <h2>Settings & Staff</h2>

                        <div class="mb-4">
                            <h3 class="h6 fw-bold">Operational Status</h3>
                            <div class="form-check">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" name="is_active" id="locActive" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="locActive">Branch is currently active and accepting bookings</label>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h3 class="h6 fw-bold mb-3">Assign Staff to this Location</h3>
                        <p class="small text-muted mb-3">Select the staff members who work at this branch. Staff working hours must still be configured per location.</p>

                        <div class="staff-assignment-list" style="max-height: 300px; overflow-y: auto;">
                            @forelse($staff as $member)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="staff[]" id="staff_{{ $member->id }}" value="{{ $member->id }}"
                                        {{ in_array($member->id, old('staff', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="staff_{{ $member->id }}">
                                        {{ $member->name }}
                                    </label>
                                </div>
                            @empty
                                <p class="small text-muted fst-italic">No staff members found in the system.</p>
                            @endforelse
                        </div>
                        @error('staff') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </form>
    </section>
@endsection
