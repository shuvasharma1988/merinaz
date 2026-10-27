@extends('layouts.admin.admin')

@section('title', 'Invite User')

@section('content')
    <section class="mz-view on requires-owner-manager" id="view-user-create" aria-label="Add user">
        <a href="{{ Route::has('admin.users.index') ? route('admin.users.index') : '#' }}" class="btn btn-quiet btn-sm mb-2">
            ← Back to users and roles
        </a>

        <form action="{{ Route::has('admin.users.store') ? route('admin.users.store') : '#' }}" method="POST">
            @csrf

            <div class="view-head">
                <div>
                    <h1>Invite user</h1>
                    <p class="muted small mb-0">Create new login credentials, set role permissions, and link staff records</p>
                </div>
                <div class="view-actions">
                    <a href="{{ Route::has('admin.users.index') ? route('admin.users.index') : '#' }}" class="btn btn-outline btn-sm">Discard</a>
                    <button type="submit" class="btn btn-accent btn-sm">Send invite / save</button>
                </div>
            </div>

            <div class="detail-grid">
                <div class="tone">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="uName">Full name</label>
                            <input class="form-control @error('name') input-error @enderror"
                                   id="uName"
                                   name="name"
                                   value="{{ old('name') }}"
                                   placeholder="e.g. Lila Karki"
                                   required>
                            @error('name') <div class="field-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="uEmail">Work email</label>
                            <input class="form-control @error('email') input-error @enderror"
                                   id="uEmail"
                                   name="email"
                                   type="email"
                                   value="{{ old('email') }}"
                                   placeholder="user@merinazdc.com"
                                   required>
                            @error('email') <div class="field-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="uPassword">Temporary password</label>
                            <input class="form-control @error('password') input-error @enderror"
                                   id="uPassword"
                                   name="password"
                                   type="password"
                                   placeholder="Min 8 characters"
                                   required>
                            @error('password') <div class="field-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="uPasswordConfirm">Confirm password</label>
                            <input class="form-control"
                                   id="uPasswordConfirm"
                                   name="password_confirmation"
                                   type="password"
                                   required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="uStaffId">Link to staff profile (Optional)</label>
                            <select class="form-select" id="uStaffId" name="staff_id">
                                <option value="">None (Desk / Admin only)</option>
                                @foreach($staffMembers ?? [] as $st)
                                    <option value="{{ $st->id }}" {{ old('staff_id') == $st->id ? 'selected' : '' }}>
                                        {{ $st->name }} ({{ $st->role }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="tiny muted mt-1 mb-0">Required for Staff role so their bookings and timeline scope correctly[cite: 1, 2].</p>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="tone section-gap">
                        <h3>Role & permissions</h3>
                        <div class="mb-3">
                            <label class="form-label" for="uRole">Assigned role</label>
                            <select class="form-select form-select-sm" id="uRole" name="role" required>
                                <option value="staff" {{ old('role', 'staff') === 'staff' ? 'selected' : '' }}>Staff (Scoped own work only)</option>
                                <option value="manager" {{ old('role') === 'manager' ? 'selected' : '' }}>Manager (All bookings/clients/catalogue)</option>
                                <option value="owner" {{ old('role') === 'owner' ? 'selected' : '' }}>Owner (Full access)</option>
                            </select>
                        </div>

                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="uActive" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="uActive">Account active</label>
                        </div>

                        <p class="tiny muted mb-0">Deactivated users cannot log into the admin dashboard.</p>
                    </div>
                </div>
            </div>
        </form>
    </section>
@endsection
