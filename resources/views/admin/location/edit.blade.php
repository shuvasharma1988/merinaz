@extends('layouts.admin.admin')

@section('title', 'Edit Location')

@section('content')
    <section class="mz-view on" id="view-location-edit" aria-label="Edit location">
        <a href="{{ route('admin.locations.index') }}" class="btn btn-quiet btn-sm mb-2">
            ← Back to locations
        </a>

        @if(session('success'))
            <div class="alert alert-success py-2 px-3 small mb-3">{{ session('success') }}</div>
        @endif

        {{-- MAIN UPDATE FORM --}}
        <form action="{{ route('admin.locations.update', $location) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="view-head">
                <div>
                    <h1>Edit branch · {{ $location->name }}</h1>
                    <p class="muted small mb-0">Update branch details and modify staff assignments</p>
                </div>
                <div class="view-actions">
                    <a href="{{ route('admin.locations.index') }}" class="btn btn-outline btn-sm">Discard</a>
                    <button type="submit" class="btn btn-accent btn-sm">Save changes</button>
                </div>
            </div>

            <div class="detail-grid">
                <div>
                    <div class="tone section-gap">
                        <h2>Branch Details</h2>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" for="locName">Branch Name *</label>
                                <input class="form-control @error('name') is-invalid @enderror" id="locName" name="name" value="{{ old('name', $location->name) }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="locAddress">Full Address *</label>
                                <input class="form-control @error('address') is-invalid @enderror" id="locAddress" name="address" value="{{ old('address', $location->address) }}" required>
                                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="locPhone">Phone Number</label>
                                <input class="form-control @error('phone') is-invalid @enderror" id="locPhone" name="phone" value="{{ old('phone', $location->phone) }}">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="locEmail">Branch Email</label>
                                <input class="form-control @error('email') is-invalid @enderror" id="locEmail" name="email" type="email" value="{{ old('email', $location->email) }}">
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
                                <input class="form-check-input" type="checkbox" name="is_active" id="locActive" value="1" {{ old('is_active', $location->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label small" for="locActive">Branch is currently active and accepting bookings</label>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h3 class="h6 fw-bold mb-3">Assigned Staff</h3>
                        <p class="small text-muted mb-3">Select the staff members who work at this branch.</p>

                        @php
                            // Get array of currently assigned staff IDs to pre-check boxes
                            $assignedStaffIds = $location->staff->pluck('id')->toArray();
                        @endphp

                        <div class="staff-assignment-list border rounded p-3 bg-light" style="max-height: 300px; overflow-y: auto;">
                            @forelse($staff as $member)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="staff[]" id="staff_{{ $member->id }}" value="{{ $member->id }}"
                                        {{ in_array($member->id, old('staff', $assignedStaffIds)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="staff_{{ $member->id }}">
                                        {{ $member->name }}
                                    </label>
                                </div>
                            @empty
                                <p class="small text-muted fst-italic mb-0">No staff members found in the system.</p>
                            @endforelse
                        </div>
                        @error('staff') <div class="small text-danger mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </form> {{-- /MAIN UPDATE FORM CLOSURE --}}

        {{-- DELETE FORM --}}
        <div class="detail-grid mt-4">
            <div></div> {{-- Empty column to maintain layout alignment --}}
            <div>
                <div class="policy-box border border-danger p-3 rounded">
                    <h3 class="text-danger">Danger Zone</h3>
                    <p class="mb-2 small">Deleting a location is permanent. You cannot delete a location if it has existing appointment records tied to it.</p>
                    <form action="{{ route('admin.locations.destroy', $location) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this branch location?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                            Delete branch location
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </section>
@endsection
