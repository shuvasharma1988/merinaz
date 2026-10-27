@extends('layouts.admin.admin')

@section('title', 'User Profile')

@section('content')
    <section class="mz-view on requires-owner-manager" id="view-user-show" aria-label="User details view">
        <a href="{{ Route::has('admin.users.index') ? route('admin.users.index') : '#' }}" class="btn btn-quiet btn-sm mb-2">
            ← Back to users and roles
        </a>

        <div class="view-head">
            <div>
                <h1>{{ $user->name ?? 'Sara Mohammadi' }}</h1>
                <p class="muted small mb-0">{{ $user->email ?? 'sara@merinazdc.com' }} · role: {{ ucfirst($user->role ?? 'manager') }}</p>
            </div>
            <div class="view-actions">
                <a href="{{ Route::has('admin.users.edit') ? route('admin.users.edit', $user->id ?? 1) : '#' }}" class="btn btn-accent btn-sm">
                    Edit user
                </a>
            </div>
        </div>

        <div class="detail-grid">
            <div>
                {{-- Account Information --}}
                <div class="tone section-gap">
                    <h2>Account details</h2>
                    <div class="row g-2 small">
                        <div class="col-sm-6"><strong>Name:</strong> {{ $user->name ?? 'Sara Mohammadi' }}</div>
                        <div class="col-sm-6"><strong>Email:</strong> {{ $user->email ?? 'sara@merinazdc.com' }}</div>
                        <div class="col-sm-6"><strong>Role:</strong> {{ ucfirst($user->role ?? 'manager') }}</div>
                        <div class="col-sm-6"><strong>Account status:</strong> {{ ($user->is_active ?? true) ? 'Active' : 'Disabled' }}</div>
                        <div class="col-sm-6"><strong>Linked staff profile:</strong> {{ $user->staff->name ?? 'None' }}</div>
                        <div class="col-sm-6"><strong>Created at:</strong> {{ $user->created_at?->format('M j, Y') ?? 'Jan 10, 2026' }}</div>
                    </div>
                </div>

                {{-- Role Scope Overview --}}
                <div class="tone">
                    <h2>Role privilege summary</h2>
                    <p class="small text-muted mb-2">
                        @if(($user->role ?? 'manager') === 'owner')
                            Full unrestricted access across all business settings, financial logs, and staff assignments[cite: 1, 2].
                        @elseif(($user->role ?? 'manager') === 'manager')
                            Complete access to bookings, client records, and service catalogues[cite: 1, 2]. Restricted from global site settings and user invitations[cite: 1, 2].
                        @else
                            Scoped access: can only view own bookings, assigned schedule, and clients served[cite: 1, 2].
                        @endif
                    </p>
                    <div class="plain-divide">
                        <a href="{{ Route::has('admin.roles.compare') ? route('admin.roles.compare') : '#' }}" class="small fw-bold">
                            View complete role comparison table →
                        </a>
                    </div>
                </div>
            </div>

            <div>
                <div class="tone section-gap">
                    <h3>Status & security</h3>
                    <p class="small mb-2">
                        <strong>State:</strong>
                        @if($user->is_active ?? true)
                            <span class="st st-confirmed"><i></i><span>Active</span></span>
                        @else
                            <span class="st st-cancelled"><i></i><span>Suspended</span></span>
                        @endif
                    </p>
                    <button type="button" class="btn btn-outline btn-sm w-100" data-toast="Password reset email queued.">
                        Send password reset
                    </button>
                </div>
            </div>
        </div>
    </section>
@endsection
