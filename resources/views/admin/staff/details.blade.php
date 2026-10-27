@extends('layouts.admin.admin')

@section('title', 'Staff Member Details')

@section('content')
    <section class="mz-view on requires-owner-manager" id="view-staff-show" aria-label="Staff detail view">
        <a href="{{ route('admin.staff.index') }}" class="btn btn-quiet btn-sm mb-2">
            ← Back to staff
        </a>

        <div class="view-head">
            <div>
                <h1>{{ $user->name }}</h1>
                <p class="muted small mb-0">{{ $user->staff->job_title ?? 'Unassigned' }} · {{ optional($user->roles->first())->name }}</p>
            </div>
            <div class="view-actions">
                @can('update', $user)
                    <a href="{{ route('admin.staff.edit', $user) }}" class="btn btn-accent btn-sm">
                        Edit staff member
                    </a>
                @endcan
            </div>
        </div>

        <div class="detail-grid">
            <div>
                {{-- Bio & Details --}}
                <div class="tone section-gap">
                    <h2>Profile summary</h2>
                    <div class="row g-2 small">
                        <div class="col-sm-6"><strong>Email:</strong> {{ $user->email }}</div>
                        <div class="col-sm-6"><strong>Phone:</strong> {{ $user->staff->phone ?? 'Not provided' }}</div>
                        <div class="col-12 mt-2">
                            <strong>Public bio:</strong>
                            <p class="muted mb-0 mt-1">{{ $user->staff->bio ?? 'No bio provided.' }}</p>
                        </div>
                    </div>
                </div>

                {{-- Services Performed --}}
                <div class="tone section-gap">
                    <h2>Services performed</h2>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        @if($user->staff && $user->staff->services->isNotEmpty())
                            @foreach($user->staff->services as $service)
                                <span class="badge bg-white text-dark border px-2 py-1">{{ $service->name }}</span>
                            @endforeach
                        @else
                            <span class="muted small">No services assigned.</span>
                        @endif
                    </div>
                </div>

                {{-- Working Schedule --}}
                <div class="tone section-gap hours-grid">
                    <h2>Working hours</h2>
                    <div class="mz-table-wrap mt-2">
                        <table class="mz">
                            <thead>
                            <tr>
                                <th>Day</th>
                                <th>Working Window</th>
                                <th>Break Window</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                // Pull normalized working hours and key them by day for easy looping
                                $workingHours = optional($user->staff)->workingHours ? $user->staff->workingHours->keyBy('day_of_week') : collect();
                                $weekdays = ['mon' => 'Monday', 'tue' => 'Tuesday', 'wed' => 'Wednesday', 'thu' => 'Thursday', 'fri' => 'Friday', 'sat' => 'Saturday', 'sun' => 'Sunday'];

                                // Helper to format time safely
                                $formatTime = function($time) {
                                    return $time ? \Carbon\Carbon::parse($time)->format('g:i A') : '--';
                                };
                            @endphp

                            @foreach($weekdays as $key => $label)
                                @php
                                    $dayData = $workingHours->get($key);
                                    $isOff = !$dayData || $dayData->is_day_off;
                                @endphp
                                <tr>
                                    <td><strong>{{ $label }}</strong></td>
                                    @if($isOff)
                                        <td colspan="2" class="muted small">Closed / Day Off</td>
                                        <td><span class="text-muted small fw-bold">Off</span></td>
                                    @else
                                        <td>{{ $formatTime($dayData->start_time) }} – {{ $formatTime($dayData->end_time) }}</td>
                                        <td>
                                            @if($dayData->break_start_time && $dayData->break_end_time)
                                                {{ $formatTime($dayData->break_start_time) }} – {{ $formatTime($dayData->break_end_time) }}
                                            @else
                                                <span class="text-muted">--</span>
                                            @endif
                                        </td>
                                        <td><span class="text-success small fw-bold">Active</span></td>
                                    @endif
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Dynamic Blackout Dates --}}
                <div class="tone section-gap">
                    <h2>Upcoming blackout dates</h2>
                    @php
                        // Get future blackout dates and sort them chronologically
                        $blackoutDates = optional($user->staff)->blackoutDates
                            ? $user->staff->blackoutDates->where('date', '>=', now()->startOfDay())->sortBy('date')
                            : collect();
                    @endphp

                    @if($blackoutDates->isNotEmpty())
                        <div class="mz-table-wrap mt-2">
                            <table class="mz">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Reason</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($blackoutDates as $blackout)
                                    <tr>
                                        <td><strong>{{ \Carbon\Carbon::parse($blackout->date)->format('l, F j, Y') }}</strong></td>
                                        <td>{{ $blackout->reason ?? 'Time off' }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="muted small mb-0 mt-2">No upcoming blackout dates scheduled.</p>
                    @endif
                </div>
            </div>

            <div>
                {{-- Today's Status Box --}}
                <div class="tone section-gap">
                    <h3>Today's load</h3>
                    <div style="font-size:24px; font-weight:800;">0 visits</div>
                    <p class="small muted mb-2">Appointments calculation coming soon.</p>
                    <div class="plain-divide">
                        <p class="small mb-1"><strong>Status:</strong>
                            @if(optional($user->staff)->is_active)
                                <span class="st st-confirmed"><i></i><span>Active on schedule</span></span>
                            @else
                                <span class="st st-cancelled"><i></i><span>Inactive</span></span>
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Granular Permissions Display --}}
                <div class="tone section-gap">
                    <h3>System Access</h3>
                    <ul class="list-unstyled small mb-0 mt-2">
                        @foreach($user->getAllPermissions() as $perm)
                            <li class="py-1 border-bottom"><span class="text-muted">{{ $perm->group }}:</span> {{ $perm->name }}</li>
                        @endforeach
                        @if($user->getAllPermissions()->isEmpty())
                            <li class="py-1 text-muted">No explicit permissions granted.</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </section>
@endsection
