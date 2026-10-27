@extends('layouts.admin.admin')

@section('title', 'Client Profile')

@section('content')
    <section class="mz-view on" id="view-client-detail" aria-label="Client detail">
        <a href="{{ route('admin.clients.index') }}" class="btn btn-quiet btn-sm mb-2">
            ← Back to clients
        </a>

        @if(session('success'))
            <div class="alert alert-success py-2 px-3 small mb-3">{{ session('success') }}</div>
        @endif

        <div class="view-head">
            <div>
                <h1 class="d-flex align-items-center">
                    {{ $client->name }}
                    @if($client->clientProfile?->has_medical_issue)
                        <span class="badge bg-danger ms-2 fs-6">MEDICAL ALERT</span>
                    @endif
                </h1>
                <p class="muted small mb-0">
                    {{ $client->phone ?? 'No Phone' }} ·
                    {{ $client->email ?? 'No Email' }} ·
                    Client since {{ $client->created_at->format('M Y') }}
                </p>
            </div>
            <div class="view-actions">
                @can('update', $client->clientProfile)
                    <a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-outline btn-sm">
                        Edit profile
                    </a>
                @endcan
                <a href="{{ Route::has('admin.appointments.create') ? route('admin.appointments.create', ['client_id' => $client->id]) : '#' }}" class="btn btn-accent btn-sm">
                    New booking
                </a>
            </div>
        </div>

        <div class="detail-grid">
            {{-- Appointment History Table --}}
            <div>
                <h2>Recent Appointments</h2>
                <div class="mz-table-wrap section-gap">
                    <table class="mz">
                        <thead>
                        <tr>
                            <th scope="col">Date</th>
                            <th scope="col">Service Details</th>
                            <th scope="col">Staff</th>
                            <th scope="col">Status</th>
                            @if(auth()->user()->hasAnyRole(['owner', 'manager']))
                                <th scope="col">Total</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody id="clientHist">
                        @forelse($client->appointments ?? [] as $appt)
                            <tr>
                                <td>{{ $appt->start_time ? \Carbon\Carbon::parse($appt->start_time)->format('M d · h:ia') : 'N/A' }}</td>
                                <td>{{ $appt->service->name ?? 'Service Data' }}</td>
                                <td>{{ $appt->staff->name ?? 'Staff Data' }}</td>
                                <td>
                                    <span class="st st-{{ strtolower($appt->status) }}">
                                        <i></i><span>{{ ucfirst($appt->status) }}</span>
                                    </span>
                                </td>
                                @if(auth()->user()->hasAnyRole(['owner', 'manager']))
                                    <td>${{ number_format($appt->total ?? 0, 2) }}</td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->hasAnyRole(['owner', 'manager']) ? '5' : '4' }}" class="text-center text-muted py-4">No appointment history found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Contact Details & Private Notes --}}
            <div>
                <div class="tone section-gap">
                    <h3>Contact & Preferences</h3>
                    <p class="small mb-1">
                        <strong>Phone:</strong> {{ $client->clientProfile?->phone ?? $client->phone ?? '—' }}<br>
                        <strong>Email:</strong> {{ $client->email ?? '—' }}<br>
                        <strong>DOB:</strong> {{ $client->clientProfile?->date_of_birth ? $client->clientProfile->date_of_birth->format('F d, Y') : 'Not set' }}<br>
                        <strong>Gender:</strong> {{ ucfirst($client->clientProfile?->gender ?? 'Not set') }}
                    </p>

                    @if($client->clientProfile?->tag)
                        <div class="mt-2">
                            <span class="badge bg-light text-dark border">Tag: {{ $client->clientProfile->tag }}</span>
                        </div>
                    @endif

                    <div class="plain-divide mt-3">
                        <p class="small text-muted mb-0">
                            <strong>Consents:</strong>
                            SMS: {!! $client->clientProfile?->sms_consent ? '<span class="text-success">Yes</span>' : '<span class="text-danger">No</span>' !!} |
                            Email: {!! $client->clientProfile?->email_consent ? '<span class="text-success">Yes</span>' : '<span class="text-danger">No</span>' !!}
                        </p>
                    </div>
                </div>

                {{-- Client Notes Component --}}
                @include('admin.clients.partials._notes')
            </div>
        </div>
    </section>
@endsection
