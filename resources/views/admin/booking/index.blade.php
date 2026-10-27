@extends('layouts.admin.admin')

@section('title', 'Bookings')

@section('content')
    <section class="mz-view on" id="view-bookings" aria-label="Bookings list">
        <div class="view-head">
            <div>
                <h1>Bookings</h1>
                <p class="muted small mb-0" id="bookingsCount">
                </p>
            </div>
            <div class="view-actions">
                <a href="{{ route('admin.bookings.create') }}" class="btn btn-accent btn-sm">
                    New booking
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success mt-2">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger mt-2">{{ session('error') }}</div>
        @endif

        {{-- Filters Toolbar --}}
        <div class="filters" role="search">
            <select class="form-select" id="fLocation" name="location_id" aria-label="Location filter" onchange="this.form?.submit()">
                <option value="">All locations</option>
                @foreach($locations ?? [] as $location)
                    <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>
                        {{ $location->name }}
                    </option>
                @endforeach
            </select>
            <select class="form-select" id="fStatus" aria-label="Status filter">
                <option value="">All statuses</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
                <option value="no-show">No-show</option>
            </select>
            <input class="form-control" id="fSearch" placeholder="Search client or service" aria-label="Search bookings">
        </div>

        {{-- Bookings Table --}}
        <div class="mz-table-wrap mt-3">
            <table class="mz" id="bookingsTable">
                <thead>
                <tr>
                    <th scope="col">Date & Time</th>
                    <th scope="col">Location</th>
                    <th scope="col">Client</th>
                    <th scope="col">Service</th>
                    <th scope="col">Assigned staff</th>
                    <th scope="col">Status</th>
                    @can('viewAny', App\Models\Appointment::class)
                        <th scope="col">Total</th>
                    @endcan
                    <th scope="col" class="text-end"><span class="visually-hidden">Actions</span></th>
                </tr>
                </thead>
                <tbody id="bookingsBody">
                @forelse($bookings as $booking)
                    @php
                        // Extract unique staff and service names for the multi-service display
                        $serviceNames = $booking->items->pluck('service.name')->unique()->implode(', ');
                        $staffNames = $booking->items->pluck('staff.name')->unique()->implode(', ');
                        $totalDuration = $booking->items->sum(function($item) {
                            return \Carbon\Carbon::parse($item->start_time)->diffInMinutes($item->end_time);
                        });
                    @endphp
                    <tr data-status="{{ strtolower($booking->status) }}">
                        <td>
                            {{ $booking->appointment_date->format('j M Y') }}<br>
                            <span class="muted small">
                                {{ $booking->visit_start_time ? \Carbon\Carbon::parse($booking->visit_start_time)->format('g:i A') : 'N/A' }}
                            </span>
                        </td>
                        <td>
                            <strong>{{ $booking->location ? $booking->location->name : 'N/A' }}</strong>
                        </td>
                        <td>
                            <strong>{{ $booking->client->name }}</strong><br>
                            <span class="tiny muted">{{ $booking->booking_reference }}</span>
                        </td>
                        <td>
                            {{ \Illuminate\Support\Str::limit($serviceNames, 30) }}<br>
                            <span class="tiny muted">{{ $booking->items->count() }} service(s) · {{ $totalDuration }} min</span>
                        </td>
                        <td>
                            {{ \Illuminate\Support\Str::limit($staffNames, 30) }}
                        </td>
                        <td>
                            <span class="st st-{{ strtolower(str_replace('_', '-', $booking->status)) }}">
                                <i></i><span>{{ str_replace('_', ' ', strtolower($booking->status)) }}</span>
                            </span>
                        </td>
                        @can('viewAny', App\Models\Appointment::class)
                            <td>${{ number_format($booking->total, 2) }}</td>
                        @endcan
                        <td class="text-end">
                            <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-quiet btn-sm">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 muted">No bookings found for the selected criteria.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-3">
            {{ $bookings->links() }}
        </div>
    </section>
@endsection
