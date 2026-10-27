@extends('layouts.admin.admin')

@section('title', 'Edit User')

@section('content')
    <section class="mz-view on requires-owner-manager" id="view-user-edit" aria-label="Edit user">
        <a href="{{ Route::has('admin.users.index') ? route('admin.users.index') : '#' }}" class="btn btn-quiet btn-sm mb-2">
            ← Back to users and roles
        </a>

        <form action="{{ Route::has('admin.users.update') ? route('admin.users.update', $user->id ?? 1) : '#' }}" method="POST">
            @csrf
            @method('PUT')

            <div class="view-head">
                <div>
                    <h1>Edit user · {{ $user->name ?? 'Sara Mohammadi' }}</h1>
                    <p class="muted small mb-0">{{ $user->email ?? 'sara@merinazdc.com' }} · role: {{ ucfirst($user->role ?? 'manager') }}</p>
                </div>
                <div class="view-actions">
                    <a href="{{ Route::has('admin.users.index') ? route('admin.users.index') : '#' }}" class="btn btn-outline btn-sm">Discard</a>
                    <button type="submit" class="btn btn-accent btn-sm">Save user record</button>
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
                                   value="{{ old('name', $user->name ?? 'Sara Mohammadi') }}"
                                   required>
                            @error('name') <div class="field-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="uEmail">Email</label>
                            <input class="form-control @error('email') input-error @enderror"
                                   id="uEmail"
                                   name="email"
                                   type="email"
                                   value="{{ old('email', $user->email ?? 'sara@merinazdc.com') }}"
                                   required>
                            @error('email') <div class="field-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <hr class="my-2">
                            <p class="small muted mb-2">Leave password fields blank if keeping the current password.</p>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="uPassNew">New password</label>
                            <input class="form-control @error('password') input-error @enderror"
                                   id="uPassNew"
                                   name="password"
                                   type="password"
                                   placeholder="Leave empty to keep">
                            @error('password') <div class="field-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="uPassConfirm">Confirm new password</label>
                            <input class="form-control"
                                   id="uPassConfirm"
                                   name="password_confirmation"
                                   type="password">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="uStaffId">Linked staff profile</label>
                            <select class="form-select" id="uStaffId" name="staff_id">
                                <option value="">None (Front desk / Management only)</option>
                                @foreach($staffMembers ?? [] as $st)
                                    <option value="{{ $st->id }}" {{ old('staff_id', $user->staff_id ?? null) == $st->id ? 'selected' : '' }}>
                                        {{ $st->name }} ({{ $st->role }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="tone section-gap">
                        <h3>Role & permissions</h3>
                        <label class="form-label" for="uRole">Role</label>
                        <select class="form-select form-select-sm mb-3" id="uRole" name="role" required>
                            <option value="staff" {{ old('role', $user->role ?? 'manager') === 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="manager" {{ old('role', $user->role ?? 'manager') === 'manager' ? 'selected' : '' }}>Manager</option>
                            <option value="owner" {{ old('role', $user->role ?? 'manager') === 'owner' ? 'selected' : '' }}>Owner</option>
                        </select>

                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input"
                                   type="checkbox"
                                   id="uActive"
                                   name="is_active"
                                   value="1"
                                {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="uActive">Login active</label>
                        </div>

                        <button type="button" class="btn btn-outline btn-sm w-100 mt-2" data-toast="Password reset sent to {{ $user->name ?? 'user' }}.">
                            Send password reset link
                        </button>
                    </div>

                    @if(($user->id ?? 2) !== auth()->id())
                        <div class="policy-box">
                            <h3>Revoke access</h3>
                            <p class="mb-2">Permanently remove this login account. Staff schedule and historical records stay intact.</p>
                            <button type="button"
                                    class="btn btn-danger-line btn-sm w-100"
                                    data-confirm="Delete user|This removes login access for this account permanently.|Delete user">
                                Delete user
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </form>
    </section>
@endsection
