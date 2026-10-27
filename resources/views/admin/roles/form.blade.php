@extends('layouts.admin.admin')

@section('title', isset($role) ? 'Edit Role' : 'Create Role')

@section('content')
    <section class="mz-view on requires-owner-manager" id="view-role-form" aria-label="Role form">
        <a href="{{ route('admin.roles.index') }}" class="btn btn-quiet btn-sm mb-2">
            ← Back to roles
        </a>

        <form action="{{ isset($role) ? route('admin.roles.update', $role) : route('admin.roles.store') }}" method="POST">
            @csrf
            @if(isset($role)) @method('PUT') @endif

            <div class="view-head">
                <div>
                    <h1>{{ isset($role) ? 'Edit role · ' . $role->name : 'Create new role' }}</h1>
                    <p class="muted small mb-0">Define role identity and assign specific system permissions</p>
                </div>
                <div class="view-actions">
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline btn-sm">Discard</a>
                    @if(!isset($role) || $role->slug !== 'owner')
                        <button type="submit" class="btn btn-accent btn-sm">Save role</button>
                    @endif
                </div>
            </div>

            @if(isset($role) && $role->slug === 'owner')
                <div class="alert alert-warning py-2 px-3 small mb-3">
                    <strong>Notice:</strong> The core 'owner' role is protected. You cannot modify its foundational access.
                </div>
            @endif

            {{-- Role Identity Details --}}
            <div class="tone section-gap">
                <h2>Role Identity</h2>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" for="roleName">Display Name</label>
                        <input class="form-control @error('name') input-error @enderror" id="roleName" name="name" value="{{ old('name', $role->name ?? '') }}" placeholder="e.g. Content Editor" required {{ (isset($role) && $role->slug === 'owner') ? 'disabled' : '' }}>
                        @error('name') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="roleSlug">System Slug</label>
                        <input class="form-control @error('slug') input-error @enderror" id="roleSlug" name="slug" value="{{ old('slug', $role->slug ?? '') }}" placeholder="e.g. content-editor" required {{ (isset($role) && $role->slug === 'owner') ? 'disabled' : '' }}>
                        @error('slug') <div class="field-error">{{ $message }}</div> @enderror
                        <div class="form-text small muted">Lowercase letters, numbers, and dashes only.</div>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label" for="roleDesc">Description</label>
                        <textarea class="form-control @error('description') input-error @enderror" id="roleDesc" name="description" rows="2" placeholder="Briefly describe what users with this role can do..." {{ (isset($role) && $role->slug === 'owner') ? 'disabled' : '' }}>{{ old('description', $role->description ?? '') }}</textarea>
                        @error('description') <div class="field-error">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            {{-- Granular Permissions Matrix --}}
            <div class="tone section-gap">
                <h2>Granular Permissions</h2>
                <p class="small muted mb-3">Check the capabilities this role should grant. You can only delegate permissions you personally possess.</p>

                @error('permissions')
                <div class="alert alert-danger py-2 px-3 small mb-3">{{ $message }}</div>
                @enderror

                <div class="row">
                    @php
                        $assignedPerms = isset($role) ? $role->permissions->pluck('id')->toArray() : [];
                    @endphp

                    @foreach($permissions as $group => $perms)
                        <div class="col-md-3 mb-4">
                            <h4 class="h6 font-weight-bold text-uppercase muted border-bottom pb-2">{{ $group }}</h4>
                            @foreach($perms as $permission)
                                <div class="form-check mb-2 mt-2">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="perm_{{ $permission->id }}"
                                        {{ (is_array(old('permissions')) && in_array($permission->id, old('permissions'))) || in_array($permission->id, $assignedPerms) ? 'checked' : '' }}
                                        {{ (isset($role) && $role->slug === 'owner') ? 'disabled' : '' }}>
                                    <label class="form-check-label small" for="perm_{{ $permission->id }}">
                                        {{ $permission->name }}
                                        <div class="text-muted tiny" style="font-size: 0.75rem;">{{ $permission->slug }}</div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </form>
    </section>
@endsection
