@extends('layouts.admin.admin')

@section('title', 'Add Staff Member')

@section('content')
    <section class="mz-view on requires-owner-manager" id="view-staff-create" aria-label="Create staff">
        <a href="{{ route('admin.staff.index') }}" class="btn btn-quiet btn-sm mb-2">
            ← Back to staff
        </a>

        <form action="{{ route('admin.staff.store') }}" method="POST">
            @csrf

            {{-- GLOBAL ERROR CATCHER --}}
            @if ($errors->any())
                <div class="alert alert-danger" style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 20px;">
                    <strong>Validation Failed:</strong>
                    <ul style="margin-bottom: 0;">
                            <?php foreach ($errors->all() as $error): ?>
                        <li>{{ $error }}</li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            @endif

            <div class="view-head">
                <div>
                    <h1>Add staff member</h1>
                    <p class="muted small mb-0">Create new staff account, roles, and booking availability</p>
                </div>
                <div class="view-actions">
                    <a href="{{ route('admin.staff.index') }}" class="btn btn-outline btn-sm">Discard</a>
                    <button type="submit" class="btn btn-accent btn-sm">Save staff member</button>
                </div>
            </div>

            {{-- Profile --}}
            <div class="tone section-gap">
                <h2>Profile</h2>
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label" for="stName">Name</label>
                        <input class="form-control @error('name') input-error @enderror" id="stName" name="name" value="{{ old('name') }}" placeholder="Full name" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="stPhone">Phone</label>
                        <input class="form-control @error('phone') input-error @enderror" id="stPhone" name="phone" value="{{ old('phone') }}" placeholder="202-555-0100">
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="stBio">Public bio</label>
                        <textarea class="form-control" id="stBio" name="bio" rows="2" placeholder="Short description displayed on public booking...">{{ old('bio') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Linked Login Account & System Role --}}
            <div class="tone section-gap">
                <h2>Linked login account & access</h2>
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label" for="accEmail">Email address</label>
                        <input class="form-control @error('email') input-error @enderror" id="accEmail" name="email" type="email" value="{{ old('email') }}" placeholder="staff@merinazdc.com" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="accPassword">Password</label>
                        <input class="form-control @error('password') input-error @enderror" id="accPassword" name="password" type="password" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="role_id">System Role (RBAC)</label>
                        <select class="form-select @error('role_id') input-error @enderror" id="role_id" name="role_id" required>
                            <option value="">Select a System Role...</option>
                                <?php foreach($roles as $role): ?>
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 mt-2">
                        <div class="form-check form-switch mb-2">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" id="accActive" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="accActive">Active for booking and login</label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Physical Locations Assigned --}}
            <div class="tone section-gap">
                <h2>Locations Assigned</h2>
                <div class="row small">
                        <?php foreach ($locations as $loc): ?>
                    <div class="col-md-4">
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" name="locations[]" value="{{ $loc->id }}" id="loc_{{ $loc->id }}"
                                {{ in_array($loc->id, old('locations', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="loc_{{ $loc->id }}">{{ $loc->name }}</label>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            {{-- Granular Permissions (RBAC) --}}
            <div class="tone section-gap">
                <h2>Granular System Permissions</h2>
                <p class="small muted mb-3">Assign specific additional permissions. You can only assign permissions you currently possess.</p>
                <div class="row">
                        <?php foreach($permissions as $group => $perms): ?>
                    <div class="col-md-3 mb-4">
                        <h4 class="h6 font-weight-bold text-uppercase muted">{{ $group }}</h4>
                            <?php foreach($perms as $permission): ?>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="perm_{{ $permission->id }}"
                                {{ is_array(old('permissions')) && in_array($permission->id, old('permissions')) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="perm_{{ $permission->id }}">
                                {{ $permission->name }}
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            {{-- Services Performed --}}
            <div class="tone section-gap">
                <h2>Services this person performs</h2>
                <div class="row small">
                    @php
                        $serviceCount = count($services);
                        $chunkSize = $serviceCount > 0 ? ceil($serviceCount / 3) : 1;
                        $serviceChunks = collect($services)->chunk($chunkSize);
                    @endphp
                        <?php foreach ($serviceChunks as $chunk): ?>
                    <div class="col-md-4">
                            <?php foreach ($chunk as $svc): ?>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" name="services[]" value="{{ $svc->id }}" id="svc_{{ $svc->id }}"
                                {{ in_array($svc->id, old('services', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="svc_{{ $svc->id }}">{{ $svc->name }}</label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            {{-- Smart Hours Table (Inlined) --}}
            <div class="tone hours-grid section-gap">
                <h2>Weekly working hours</h2>
                <p class="small muted mb-3">Check "Day off" to clear hours. Uncheck to restore default 10:00 - 17:00 schedule.</p>
                <div class="table-responsive">
                    <table class="table table-sm align-middle" id="working-hours-table">
                        <thead>
                        <tr>
                            <th>Day</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Break start</th>
                            <th>Break end</th>
                            <th class="text-center">Day off</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $days = ['mon' => 'Monday', 'tue' => 'Tuesday', 'wed' => 'Wednesday', 'thu' => 'Thursday', 'fri' => 'Friday', 'sat' => 'Saturday', 'sun' => 'Sunday'];
                            $hours = old('hours', $formattedHours ?? []);
                        @endphp

                            <?php foreach ($days as $key => $label): ?>
                        @php
                            $dayData = $hours[$key] ?? [];
                            $isOff = isset($dayData['is_day_off']) ? (bool) $dayData['is_day_off'] : ($key === 'sun');
                        @endphp
                        <tr class="day-row">
                            <td><strong>{{ $label }}</strong></td>
                            <td><input type="time" class="form-control form-control-sm time-input" name="hours[{{ $key }}][start_time]" value="{{ $dayData['start_time'] ?? '' }}"></td>
                            <td><input type="time" class="form-control form-control-sm time-input" name="hours[{{ $key }}][end_time]" value="{{ $dayData['end_time'] ?? '' }}"></td>
                            <td><input type="time" class="form-control form-control-sm time-input" name="hours[{{ $key }}][break_start_time]" value="{{ $dayData['break_start_time'] ?? '' }}"></td>
                            <td><input type="time" class="form-control form-control-sm time-input" name="hours[{{ $key }}][break_end_time]" value="{{ $dayData['break_end_time'] ?? '' }}"></td>
                            <td class="text-center">
                                <input type="hidden" name="hours[{{ $key }}][is_day_off]" value="0">
                                <input class="form-check-input day-off-toggle" type="checkbox" name="hours[{{ $key }}][is_day_off]" value="1" {{ $isOff ? 'checked' : '' }}>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Blackout Dates --}}
            <div class="tone section-gap">
                <h2>Blackout dates</h2>
                <p class="small muted mb-3">Manage exceptions like deep cleans, holidays, or sick leave.</p>
                <div class="row">
                    <div class="col-md-8">
                        <div id="blackout-dates-container" class="mb-3">
                            @php $blackouts = old('blackout_dates', []); @endphp
                                <?php foreach ($blackouts as $index => $blackout): ?>
                            <div class="d-flex align-items-center justify-content-between border-bottom py-2 blackout-row">
                                <div class="d-flex gap-2 w-100">
                                    <input type="date" class="form-control form-control-sm w-auto" name="blackout_dates[{{ $index }}][date]" value="{{ is_array($blackout) ? $blackout['date'] : '' }}" required>
                                    <input type="text" class="form-control form-control-sm" name="blackout_dates[{{ $index }}][reason]" value="{{ is_array($blackout) ? ($blackout['reason'] ?? '') : '' }}" placeholder="Reason (e.g., Thanksgiving)">
                                </div>
                                <button type="button" class="btn btn-link text-danger text-decoration-none btn-sm remove-blackout ms-3">Remove</button>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="d-flex gap-2 mt-2 pt-2">
                            <input type="date" id="new-blackout-date" class="form-control form-control-sm w-auto">
                            <input type="text" id="new-blackout-reason" class="form-control form-control-sm" placeholder="Reason (e.g. Dentist appointment)">
                            <button type="button" id="add-blackout-btn" class="btn btn-outline-secondary btn-sm text-nowrap">Add date</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Updated Standard Baseline Template
            const defaultTimes = {
                'start_time': '10:00',
                'end_time': '17:00',
                'break_start_time': '13:00',
                'break_end_time': '13:30'
            };

            const toggles = document.querySelectorAll('.day-off-toggle');

            toggles.forEach(function(toggle) {
                toggle.addEventListener('change', function() {
                    const row = this.closest('.day-row');
                    const timeInputs = row.querySelectorAll('.time-input');

                    if (this.checked) {
                        timeInputs.forEach(input => {
                            input.value = '';
                            input.readOnly = true;
                            input.setAttribute('tabindex', '-1');
                            input.style.backgroundColor = '#e9ecef';
                            input.style.pointerEvents = 'none';
                        });
                    } else {
                        timeInputs.forEach(input => {
                            input.readOnly = false;
                            input.removeAttribute('tabindex');
                            input.style.backgroundColor = '';
                            input.style.pointerEvents = 'auto';

                            // Fill with default time if it was empty
                            const match = input.name.match(/\[(start_time|end_time|break_start_time|break_end_time)\]/);
                            if (match && !input.value) {
                                input.value = defaultTimes[match[1]];
                            }
                        });
                    }
                });
                // Initialize state on load
                toggle.dispatchEvent(new Event('change'));
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('blackout-dates-container');
            const addBtn = document.getElementById('add-blackout-btn');
            const dateInput = document.getElementById('new-blackout-date');
            const reasonInput = document.getElementById('new-blackout-reason');

            let index = {{ count(old('blackout_dates', [])) }};

            addBtn.addEventListener('click', function() {
                if (!dateInput.value) {
                    alert('Please select a date first.');
                    return;
                }

                const row = document.createElement('div');
                row.className = 'd-flex align-items-center justify-content-between border-bottom py-2 blackout-row';

                row.innerHTML = `
                    <div class="d-flex gap-2 w-100">
                        <input type="date" class="form-control form-control-sm w-auto" name="blackout_dates[${index}][date]" value="${dateInput.value}" required>
                        <input type="text" class="form-control form-control-sm" name="blackout_dates[${index}][reason]" value="${reasonInput.value}" placeholder="Reason">
                    </div>
                    <button type="button" class="btn btn-link text-danger text-decoration-none btn-sm remove-blackout ms-3">Remove</button>
                `;

                container.appendChild(row);
                index++;

                dateInput.value = '';
                reasonInput.value = '';
            });

            container.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-blackout')) {
                    e.target.closest('.blackout-row').remove();
                }
            });
        });
    </script>
@endpush
