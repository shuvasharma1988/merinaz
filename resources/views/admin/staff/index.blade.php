@extends('layouts.admin.admin')

@section('title', 'Staff')

@section('content')
    <section class="mz-view on requires-owner-manager" id="view-staff" aria-label="Staff list">
        <div class="view-head">
            <div>
                <h1>Staff</h1>
                <p class="muted small mb-0">{{ $users->count() ?? '0' }} people · hours and blackouts live on booking page</p>
            </div>
            <div class="view-actions">
                <a href="{{ route('admin.staff.create') }}" class="btn btn-accent btn-sm">
                    Add staff member
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success py-2 px-3 small mb-3" style="background:#eaf7e3; border-color:#c8e6c9; color:#2e7d32;">
                {{ session('success') }}
            </div>
        @endif

        <div class="mz-table-wrap section-gap">
            <table class="mz">
                <thead>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Job Title</th>
                    <th scope="col">System Role</th>
                    <th scope="col">Active</th>
                    <th scope="col" class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <strong>
                                <a href="{{ route('admin.staff.show', $user) }}" class="text-decoration-none text-dark">
                                    {{ $user->name }}
                                </a>
                            </strong><br>
                            <span class="small muted">{{ $user->email }}</span>
                        </td>
                        <td>{{ $user->staff->job_title ?? 'Unassigned' }}</td>
                        <td>
                            @foreach($user->roles as $role)
                                <span class="badge bg-secondary">{{ $role->name }}</span>
                            @endforeach
                            <br><span class="tiny muted">{{ $user->permissions->count() }} direct permissions</span>
                        </td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" {{ optional($user->staff)->is_active ? 'checked' : '' }} disabled aria-label="Active status">
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('admin.staff.show', $user) }}" class="btn btn-quiet btn-sm">View</a>
                                @can('update', $user)
                                    <a href="{{ route('admin.staff.edit', $user) }}" class="btn btn-quiet btn-sm">Edit</a>
                                @endcan
                                @can('delete', $user)
                                    <form action="{{ route('admin.staff.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this user? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-quiet btn-sm text-danger" style="border:1px solid transparent; background:transparent;">Delete</button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 muted small">No staff members found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <p class="small muted mt-2">Own only means rows, timelines and history are filtered to visits where the staff member is assigned.</p>
    </section>
@endsection
