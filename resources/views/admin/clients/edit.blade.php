@extends('layouts.admin.admin')

@section('title', 'Edit Client')

@section('content')
    <section class="mz-view on" id="view-client-edit" aria-label="Edit client">
        <a href="{{ route('admin.clients.show', $client) }}" class="btn btn-quiet btn-sm mb-2">
            ← Back to client record
        </a>

        @if(session('success'))
            <div class="alert alert-success py-2 px-3 small mb-3">{{ session('success') }}</div>
        @endif

        {{-- MAIN UPDATE FORM --}}
        <form action="{{ route('admin.clients.update', $client) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="view-head">
                <div>
                    <h1>Edit client · {{ $client->name }}</h1>
                    <p class="muted small mb-0">Client ID #{{ $client->id }} · Member since {{ $client->created_at->format('M Y') }}</p>
                </div>
                <div class="view-actions">
                    <a href="{{ route('admin.clients.show', $client) }}" class="btn btn-outline btn-sm">Discard</a>
                    <button type="submit" class="btn btn-accent btn-sm">Save changes</button>
                </div>
            </div>

            @php
                $profile = $client->clientProfile;
                $parts = explode(' ', $client->name);
                $lastName = array_pop($parts);
                $firstName = implode(' ', $parts);
            @endphp

            <div class="detail-grid">
                <div>
                    <div class="tone section-gap">
                        <h2>Personal & Contact Details</h2>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="clFirst">First name *</label>
                                <input class="form-control @error('first_name') is-invalid @enderror" id="clFirst" name="first_name" value="{{ old('first_name', $firstName) }}" required>
                                @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="clLast">Last name *</label>
                                <input class="form-control @error('last_name') is-invalid @enderror" id="clLast" name="last_name" value="{{ old('last_name', $lastName) }}" required>
                                @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="clPhone">Phone *</label>
                                <input class="form-control @error('phone') is-invalid @enderror" id="clPhone" name="phone" value="{{ old('phone', $profile?->phone ?? $client->phone) }}" required>
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="clEmail">Email</label>
                                <input class="form-control @error('email') is-invalid @enderror" id="clEmail" name="email" type="email" value="{{ old('email', $client->email) }}">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="clDob">Date of Birth</label>
                                <input class="form-control @error('date_of_birth') is-invalid @enderror" id="clDob" name="date_of_birth" type="date" value="{{ old('date_of_birth', $profile?->date_of_birth?->format('Y-m-d')) }}">
                                @error('date_of_birth') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="clGender">Gender</label>
                                <select class="form-select @error('gender') is-invalid @enderror" id="clGender" name="gender">
                                    <option value="">Select...</option>
                                    <option value="female" {{ old('gender', $profile?->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="male" {{ old('gender', $profile?->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="other" {{ old('gender', $profile?->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                    <option value="prefer_not_to_say" {{ old('gender', $profile?->gender) == 'prefer_not_to_say' ? 'selected' : '' }}>Prefer not to say</option>
                                </select>
                                @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="tone section-gap">
                        <h2>Client Profile Details</h2>

                        <div class="mb-3">
                            <label class="form-label" for="clStatus">Account Status</label>
                            <select class="form-select" id="clStatus" name="status">
                                <option value="active" {{ old('status', $profile?->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $profile?->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="flagged" {{ old('status', $profile?->status) == 'flagged' ? 'selected' : '' }}>Flagged</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="clSource">Acquisition Source</label>
                            <input class="form-control" id="clSource" name="source" value="{{ old('source', $profile?->source) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="clTag">Client Tag</label>
                            <input class="form-control" id="clTag" name="tag" value="{{ old('tag', $profile?->tag) }}" placeholder="e.g. VIP, Influencer, New">
                        </div>

                        <hr class="my-4">

                        <h3 class="h6 fw-bold">Flags & Consents</h3>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="has_medical_issue" id="clMedical" value="1" {{ old('has_medical_issue', $profile?->has_medical_issue) ? 'checked' : '' }}>
                            <label class="form-check-label text-danger fw-medium" for="clMedical">Has medical issues or skin allergies (Requires physical documentation)</label>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="sms_consent" id="clSmsConsent" value="1" {{ old('sms_consent', $profile?->sms_consent) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="clSmsConsent">Opt-in for SMS reminders</label>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="email_consent" id="clEmailConsent" value="1" {{ old('email_consent', $profile?->email_consent) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="clEmailConsent">Opt-in for Email reminders</label>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="marketing_consent" id="clConsent" value="1" {{ old('marketing_consent', $profile?->marketing_consent) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="clConsent">Opt-in for Marketing communications</label>
                        </div>
                    </div>
                </div>
            </div>
        </form> {{-- /MAIN UPDATE FORM CLOSURE --}}

        {{-- DELETE FORM (Outside the main form) --}}
        @if(auth()->user()->can('delete', $profile))
            <div class="detail-grid mt-4">
                <div></div> {{-- Empty column to maintain grid alignment --}}
                <div>
                    <div class="policy-box border border-danger p-3 rounded">
                        <h3 class="text-danger">Danger Zone</h3>
                        <p class="mb-2 small">Deleting this profile archives the contact record. Past visit logs will be preserved.</p>
                        <form action="{{ route('admin.clients.destroy', $client) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this client?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                Delete client profile
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

    </section>
@endsection
