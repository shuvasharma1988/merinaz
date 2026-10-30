@extends('layouts.admin.admin')

@section('title', 'Users and roles')

@section('content')
    <section class="mz-view on requires-owner-manager" id="view-users-roles" aria-label="Users and roles">
        <div class="view-head">
            <div>
                <h1>Users and roles</h1>
                <p class="muted small mb-0">Owner manages all · staff is scoped to own work</p>
            </div>
            <div class="view-actions">
                <a href="{{ Route::has('admin.users.create') ? route('admin.users.create') : '#' }}" class="btn btn-accent btn-sm">
                    Invite user
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success py-2 px-3 small mb-3">{{ session('success') }}</div>
        @endif

        {{-- Users Table --}}
        <div class="mz-table-wrap section-gap">
            <table class="mz">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Active</th>
                    <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($users ?? [] as $user)
                    <tr>
                        <td>
                            <strong>
                                <a href="{{ Route::has('admin.users.show') ? route('admin.users.show', $user) : '#' }}" class="text-decoration-none text-dark">
                                    {{ $user->name }}
                                </a>
                            </strong>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge bg-white text-dark border px-2 py-1">
                                {{ ucfirst($user->role ?? 'staff') }}
                            </span>
                        </td>
                        <td>{{ $user->is_active ? 'Yes' : 'No' }}</td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ Route::has('admin.users.edit') ? route('admin.users.edit', $user) : '#' }}" class="btn btn-quiet btn-sm">Edit</a>
                                @if(auth()->id() !== $user->id)
                                    <form action="{{ Route::has('admin.users.destroy') ? route('admin.users.destroy', $user) : '#' }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this user login?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-quiet btn-sm text-danger">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    {{-- Prototype Default Users --}}
                    <tr>
                        <td><strong>Merinaz Hedayati</strong></td>
                        <td>merinaz@merinazdc.com</td>
                        <td>Owner</td>
                        <td>Yes</td>
                        <td class="text-end"><button type="button" class="btn btn-quiet btn-sm" disabled>Owner</button></td>
                    </tr>
                    <tr>
                        <td><strong>Sara Mohammadi</strong></td>
                        <td>sara@merinazdc.com</td>
                        <td>Manager</td>
                        <td>Yes</td>
                        <td class="text-end"><a href="#" class="btn btn-quiet btn-sm">Edit</a></td>
                    </tr>
                    <tr>
                        <td><strong>Lila Karki</strong></td>
                        <td>lila.k@merinazdc.com</td>
                        <td>Staff</td>
                        <td>Yes</td>
                        <td class="text-end"><a href="#" class="btn btn-quiet btn-sm">Edit</a></td>
                    </tr>
                    <tr>
                        <td><strong>Priya Sharma</strong></td>
                        <td>priya.s@merinazdc.com</td>
                        <td>Staff</td>
                        <td>Yes</td>
                        <td class="text-end"><a href="#" class="btn btn-quiet btn-sm">Edit</a></td>
                    </tr>
                    <tr>
                        <td><strong>Daniela Ruiz</strong></td>
                        <td>daniela.r@merinazdc.com</td>
                        <td>Staff</td>
                        <td>Yes</td>
                        <td class="text-end"><a href="#" class="btn btn-quiet btn-sm">Edit</a></td>
                    </tr>
                    <tr>
                        <td><strong>Amara Okafor</strong></td>
                        <td>amara.o@merinazdc.com</td>
                        <td>Staff</td>
                        <td>No</td>
                        <td class="text-end"><a href="#" class="btn btn-quiet btn-sm">Edit</a></td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Permission Matrix Overview --}}
        <h2>Permission matrix</h2>
        <div class="mz-table-wrap">
            <table class="mz">
                <thead>
                <tr>
                    <th>Area</th>
                    <th>Owner</th>
                    <th>Manager</th>
                    <th>Staff</th>
                </tr>
                </thead>
                <tbody>
                <tr><td>Bookings</td><td>All</td><td>All</td><td><strong>Own only</strong></td></tr>
                <tr><td>Clients</td><td>All</td><td>All</td><td><strong>Own only</strong></td></tr>
                <tr><td>Staff</td><td>All</td><td>All</td><td>No access</td></tr>
                <tr><td>Services</td><td>All</td><td>All</td><td>No access</td></tr>
                <tr><td>Content</td><td>All</td><td>All</td><td>No access</td></tr>
                <tr><td>Settings</td><td>All</td><td>No access</td><td>No access</td></tr>
                <tr><td>Users</td><td>All</td><td>No access</td><td>No access</td></tr>
                </tbody>
            </table>
        </div>
        <p class="small muted mt-2">
            Own only means rows, timelines, and history are filtered to visits where the staff member is assigned. Notes marked owner and manager only stay hidden.
        </p>
    </section>
@endsection
