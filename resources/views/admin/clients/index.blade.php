@extends('layouts.admin.admin')

@section('title', 'Clients')

@section('content')
    <section class="mz-view on" id="view-clients" aria-label="Clients">
        <div class="view-head">
            <div>
                <h1>Clients</h1>
                <p class="muted small mb-0">{{ $clients->total() }} records · search by name, phone or email</p>
            </div>
            <div class="view-actions">
                @can('create', \App\Models\ClientProfile::class)
                    <a href="{{ route('admin.clients.create') }}" class="btn btn-accent btn-sm">
                        Add client
                    </a>
                @endcan
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success py-2 px-3 small mb-3">{{ session('success') }}</div>
        @endif

        {{-- Filter Toolbar --}}
        <div class="filters">
            <input class="form-control"
                   id="clientSearch"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Search clients"
                   aria-label="Search clients"
                   style="min-width: 260px;">
            <select class="form-select" id="clientFilter" aria-label="Filter">
                <option value="all">All clients</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="flagged">Flagged</option>
            </select>
        </div>

        {{-- Clients Table --}}
        <div class="mz-table-wrap">
            <table class="mz" id="clientsTable">
                <thead>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Phone</th>
                    <th scope="col">Email</th>
                    <th scope="col">Status</th>
                    <th scope="col">Visits</th>
                    @if(auth()->user()->hasAnyRole(['owner', 'manager']))
                        <th scope="col" class="requires-owner-manager">Total spent</th>
                    @endif
                    <th scope="col" class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody id="clientsBody">
                @forelse($clients as $client)
                    <tr>
                        <td>
                            <a href="{{ route('admin.clients.show', $client) }}" class="fw-bold text-decoration-none text-dark">
                                {{ $client->name }}
                                @if($client->clientProfile?->has_medical_issue)
                                    <span class="badge bg-danger ms-1" title="Medical Issue / Allergy" style="font-size: 0.65em;">+</span>
                                @endif
                            </a>
                        </td>
                        <td>{{ $client->clientProfile?->phone ?? $client->phone ?? '—' }}</td>
                        <td>{{ $client->email ?? '—' }}</td>
                        <td>
                            <span class="badge bg-{{ $client->clientProfile?->status === 'active' ? 'success' : ($client->clientProfile?->status === 'flagged' ? 'danger' : 'secondary') }}">
                                {{ ucfirst($client->clientProfile?->status ?? 'active') }}
                            </span>
                        </td>
                        <td>{{ $client->clientProfile?->total_visits ?? 0 }}</td>

                        @if(auth()->user()->hasAnyRole(['owner', 'manager']))
                            <td class="requires-owner-manager">${{ number_format($client->clientProfile?->lifetime_spend ?? 0, 2) }}</td>
                        @endif

                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                @can('view', $client->clientProfile ?? new \App\Models\ClientProfile)
                                    <a href="{{ route('admin.clients.show', $client) }}" class="btn btn-quiet btn-sm">View</a>
                                @endcan
                                @can('update', $client->clientProfile ?? new \App\Models\ClientProfile)
                                    <a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-quiet btn-sm">Edit</a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No clients found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $clients->links() }}
        </div>
    </section>
@endsection
