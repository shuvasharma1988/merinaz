@extends('layouts.admin.admin')

@section('title', 'Roles & Permissions')

@section('content')
    <section class="mz-view on requires-owner-manager" id="view-roles" aria-label="Roles list">
        <div class="view-head">
            <div>
                <h1>Roles & Permissions</h1>
                <p class="muted small mb-0">{{ $roles->count() }} system roles · Manage access levels</p>
            </div>
            <div class="view-actions">
                <a href="{{ route('admin.roles.create') }}" class="btn btn-accent btn-sm">
                    Create new role
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
                    <th scope="col">Role Name</th>
                    <th scope="col">System Slug</th>
                    <th scope="col">Description</th>
                    <th scope="col">Permissions</th>
                    <th scope="col">Users</th>
                    <th scope="col" class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($roles as $role)
                    <tr>
                        <td><strong>{{ $role->name }}</strong></td>
                        <td><span class="badge bg-light text-dark border">{{ $role->slug }}</span></td>
                        <td class="small muted">{{ Str::limit($role->description, 50) ?? 'No description' }}</td>
                        <td>{{ $role->permissions_count }} permissions</td>
                        <td>{{ $role->users_count }} assigned</td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                @can('update', $role)
                                    <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-quiet btn-sm">Edit</a>
                                @endcan
                                @can('delete', $role)
                                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete this role? Any users assigned to it will lose these permissions.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-quiet btn-sm text-danger" style="border:1px solid transparent; background:transparent;">Delete</button>
                                    </form>
                                @endcan
                                @if($role->slug === 'owner')
                                    <span class="small muted ms-2 text-danger" title="The Owner role is immutable.">Protected</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
