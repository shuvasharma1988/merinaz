@extends('layouts.admin.admin')

@section('title', 'Locations')

@section('content')
    <section class="mz-view on" id="view-locations" aria-label="Locations">
        <div class="view-head">
            <div>
                <h1>Salon Locations</h1>
                <p class="muted small mb-0">{{ $locations->count() }} branches · Manage your studio locations and staff assignments</p>
            </div>
            <div class="view-actions">
                <a href="{{ route('admin.locations.create') }}" class="btn btn-accent btn-sm">
                    Add location
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success py-2 px-3 small mb-3">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger py-2 px-3 small mb-3">{{ session('error') }}</div>
        @endif

        {{-- Locations Table --}}
        <div class="mz-table-wrap mt-4">
            <table class="mz" id="locationsTable">
                <thead>
                <tr>
                    <th scope="col">Branch Name</th>
                    <th scope="col">Address</th>
                    <th scope="col">Contact</th>
                    <th scope="col">Assigned Staff</th>
                    <th scope="col">Status</th>
                    <th scope="col" class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody id="locationsBody">
                @forelse($locations as $location)
                    <tr>
                        <td>
                            <a href="{{ route('admin.locations.edit', $location) }}" class="fw-bold text-decoration-none text-dark">
                                {{ $location->name }}
                            </a>
                        </td>
                        <td>
                            <span class="d-block text-truncate" style="max-width: 250px;" title="{{ $location->address }}">
                                {{ $location->address ?? '—' }}
                            </span>
                        </td>
                        <td>
                            <span class="small d-block">{{ $location->phone ?? 'No phone' }}</span>
                            <span class="small d-block text-muted">{{ $location->email ?? 'No email' }}</span>
                        </td>
                        <td>{{ $location->staff_count }} members</td>
                        <td>
                            <span class="badge bg-{{ $location->is_active ? 'success' : 'secondary' }}">
                                {{ $location->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('admin.locations.edit', $location) }}" class="btn btn-quiet btn-sm">Edit</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No locations found. Add your first salon branch.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
