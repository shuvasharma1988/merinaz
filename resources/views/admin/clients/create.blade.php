@extends('layouts.admin.admin')

@section('title', 'Add Client')

@section('content')
    <section class="mz-view on" id="view-client-create" aria-label="Add client">
        <a href="{{ route('admin.clients.index') }}" class="btn btn-quiet btn-sm mb-2">
            ← Back to clients
        </a>

        <form action="{{ route('admin.clients.store') }}" method="POST">
            @csrf

            <div class="view-head">
                <div>
                    <h1>Add client profile</h1>
                    <p class="muted small mb-0">Record contact details, demographics, and preferences</p>
                </div>
                <div class="view-actions">
                    <a href="{{ route('admin.clients.index') }}" class="btn btn-outline btn-sm">Discard</a>
                    <button type="submit" class="btn btn-accent btn-sm">Save client</button>
                </div>
            </div>

            <div class="detail-grid">
                <div>
                    <div class="tone section-gap">
                        <h2>Personal & Contact Details</h2>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="clFirst">First name *</label>
                                <input class="form-control @error('first_name') is-invalid @enderror" id="clFirst" name="first_name" value="{{ old('first_name') }}" required>
                                @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="clLast">Last name *</label>
                                <input class="form-control @error('last_name') is-invalid @enderror" id="clLast" name="last_name" value="{{ old('last_name') }}" required>
                                @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="clPhone">Phone number *</label>
                                <input class="form-control @error('phone') is-invalid @enderror" id="clPhone" name="phone" value="{{ old('phone') }}" required>
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="clEmail">Email address</label>
                                <input class="form-control @error('email') is-invalid @enderror" id="clEmail" name="email" type="email" value="{{ old('email') }}">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="clDob">Date of Birth</label>
                                <input class="form-control @error('date_of_birth') is-invalid @enderror" id="clDob" name="date_of_birth" type="date" value="{{ old('date_of_birth') }}">
                                @error('date_of_birth') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="clGender">Gender</label>
                                <select class="form-select @error('gender') is-invalid @enderror" id="clGender" name="gender">
                                    <option value="">Select...</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                    <option value="prefer_not_to_say" {{ old('gender') == 'prefer_not_to_say' ? 'selected' : '' }}>Prefer not to say</option>
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
                            <label class="form-label" for="clSource">Acquisition Source</label>
                            <select class="form-select" id="clSource" name="source">
                                <option value="admin" {{ old('source') == 'admin' ? 'selected' : '' }}>Admin Entry</option>
                                <option value="walk_in" {{ old('source') == 'walk_in' ? 'selected' : '' }}>Walk-in</option>
                                <option value="referral" {{ old('source') == 'referral' ? 'selected' : '' }}>Referral</option>
                                <option value="online" {{ old('source') == 'online' ? 'selected' : '' }}>Online</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="clTag">Client Tag</label>
                            <input class="form-control" id="clTag" name="tag" value="{{ old('tag') }}" placeholder="e.g. VIP, Influencer, New">
                        </div>

                        <hr class="my-4">

                        <h3 class="h6 fw-bold">Flags & Consents</h3>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="has_medical_issue" id="clMedical" value="1" {{ old('has_medical_issue') ? 'checked' : '' }}>
                            <label class="form-check-label text-danger fw-medium" for="clMedical">Has medical issues or skin allergies (Requires physical documentation)</label>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="sms_consent" id="clSmsConsent" value="1" {{ old('sms_consent', true) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="clSmsConsent">Opt-in for SMS reminders</label>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="email_consent" id="clEmailConsent" value="1" {{ old('email_consent', true) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="clEmailConsent">Opt-in for Email reminders</label>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="marketing_consent" id="clConsent" value="1" {{ old('marketing_consent') ? 'checked' : '' }}>
                            <label class="form-check-label small" for="clConsent">Opt-in for Marketing communications</label>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
@endsection
