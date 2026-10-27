@extends('layouts.admin.admin')

@section('title', 'Edit Staff')

@section('content')
    <section class="mz-view on requires-owner-manager" id="view-staff-edit" aria-label="Staff edit">
        <a href="{{ route('admin.staff.index') }}" class="btn btn-quiet btn-sm mb-2">
            ← Back to staff
        </a>

        <form action="{{ route('admin.staff.update', $staff) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- GLOBAL ERROR CATCHER --}}
            @if ($errors->any())
                <div class="alert alert-danger" style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 20px;">
                    <strong>Validation Failed:</strong>
                    <ul style="margin-bottom: 0;">
                            <?php foreach ($errors->all() as$error): ?>
                        <li>{{ $error }}</li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            @endif

            <div class="view-head">
                <div>
                    <h1>Edit staff · {{ $staff->name }}</h1>
                    <p class="muted small mb-0">{{ $staff->staff->job_title ?? 'Unassigned' }} · {{ optional($staff->roles->first())->name }} · {{ optional($staff->staff)->is_active ? 'active' : 'inactive' }}</p>
                </div>
                <div class="view-actions">
                    <a href="{{ route('admin.staff.index') }}" class="btn btn-outline btn-sm">Discard changes</a>
                    <button type="submit" class="btn btn-accent btn-sm">Save staff record</button>
                </div>
            </div>

            {{-- Profile Details --}}
            <div class="tone section-gap">
                <h2>Profile</h2>
                <div class="row g-2">
                    <div class="col-md-6">
                        <label class="form-label" for="stName">Name</label>
                        <input class="form-control @error('name') input-error @enderror" id="stName" name="name" value="{{ old('name', $staff->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="stPhone">Phone</label>
                        <input class="form-control @error('phone') input-error @enderror" id="stPhone" name="phone" value="{{ old('phone', $staff->staff->phone ?? '') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="stBio">Public bio</label>
                        <textarea class="form-control" id="stBio" name="bio" rows="2">{{ old('bio', $staff->staff->bio ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Linked Account and Access Control --}}
            <div class="tone section-gap">
                <h2>Linked login account & access</h2>
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label" for="accEmail">Email address</label>
                        <input class="form-control @error('email') input-error @enderror" id="accEmail" name="email" type="email" value="{{ old('email', $staff->email) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="accPassword">Reset Password <span class="muted small">(Leave blank to keep)</span></label>
                        <input class="form-control @error('password') input-error @enderror" id="accPassword" name="password" type="password">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="role_id">System Role (RBAC)</label>
                        <select class="form-select @error('role_id') input-error @enderror" id="role_id" name="role_id" required>
                                <?php foreach ($roles as$role): ?>
                            <option value="{{ $role->id }}" {{ (old('role_id') == $role->id || $staff->roles->contains('id',$role->id)) ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 mt-2">
                        <div class="form-check form-switch mb-2">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" id="accActive" name="is_active" value="1" {{ old('is_active', $staff->staff->is_active ?? 1) ? 'checked' : '' }}>
                            <label class="form-check-label small" for="accActive">Active for booking and login</label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Physical Locations Assigned --}}
            <div class="tone section-gap">
                <h2>Locations Assigned</h2>
                <div class="row small">
                    @php
                        $assignedLocations = $staff->staff ? $staff->staff->locations->pluck('id')->toArray() : [];
                    @endphp
                        <?php foreach ($locations as$loc): ?>
                    <div class="col-md-4">
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" name="locations[]" value="{{ $loc->id }}" id="loc_{{ $loc->id }}"
                                {{ in_array($loc->id, old('locations',$assignedLocations)) ? 'checked' : '' }}>
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
                        <?php foreach ($permissions as $group =>$perms): ?>
                    <div class="col-md-3 mb-4">
                        <h4 class="h6 font-weight-bold text-uppercase muted">{{ $group }}</h4>
                            <?php foreach ($perms as$permission): ?>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="perm_{{ $permission->id }}"
                                {{ (is_array(old('permissions')) && in_array($permission->id, old('permissions'))) || $staff->permissions->contains('id',$permission->id) ? 'checked' : '' }}>
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
                        $assignedServices =$staff->staff ? $staff->staff->services->pluck('id')->toArray() : [];$serviceCount = count($services);$chunkSize = $serviceCount > 0 ? ceil($serviceCount / 3) : 1;
                        $serviceChunks = collect($services)->chunk($chunkSize);
                    @endphp
                        <?php foreach ($serviceChunks as$chunk): ?>
                    <div class="col-md-4">
                            <?php foreach ($chunk as$svc): ?>
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="checkbox" name="services[]" value="{{ $svc->id }}" id="svc_{{ $svc->id }}"
                                {{ in_array($svc->id, old('services',$assignedServices)) ? 'checked' : '' }}>
                            <label class="form-check-label" for="svc_{{ $svc->id }}">{{ $svc->name }}</label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            {{-- Smart Hours Table --}}
            <div class="tone hours-grid section-gap">
                <h2>Weekly working hours</h2>
                <p class="small muted mb-3">Check "Day off" to clear hours. Uncheck to load standard 10:00 - 17:00 schedule.</p>
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
                            $hours = old('hours',$formattedHours ?? []);
                        @endphp

                            <?php foreach ($days as $key =>$label): ?>
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
                            @php $blackouts = old('blackout_dates',$staff->staff->blackoutDates ?? []); @endphp
                                <?php foreach ($blackouts as $index =>$blackout): ?>
                            <div class="d-flex align-items-center justify-content-between border-bottom py-2 blackout-row">
                                <div class="d-flex gap-2 w-100">
                                    <input type="date" class="form-control form-control-sm w-auto"
                                           name="blackout_dates[{{ $index }}][date]"
                                           value="{{ is_array($blackout) ? $blackout['date'] : (is_object($blackout) && $blackout->date ? $blackout->date->format('Y-m-d') : '') }}"
                                           required>

                                    <input type="text" class="form-control form-control-sm"
                                           name="blackout_dates[{{ $index }}][reason]"
                                           value="{{ is_array($blackout) ? ($blackout['reason'] ?? '') : ($blackout->reason ?? '') }}"
                                           placeholder="Reason (e.g., Thanksgiving)">
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
            // Standard 10 to 5 Baseline
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

                            // Automatically populate with default times if empty
                            const match = input.name.match(/\[(start_time\vert{}end_time\vert{}break_start_time\vert{}break_end_time)\]/);
                            if (match && !input.value) {
                                input.value = defaultTimes[match[1]];
                            }
                        });
                    }
                });
                toggle.dispatchEvent(new Event('change'));
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('blackout-dates-container');
            const addBtn = document.getElementById('add-blackout-btn');
            const dateInput = document.getElementById('new-blackout-date');
            const reasonInput = document.getElementById('new-blackout-reason');

            let index = {{ count(old('blackout_dates', $staff->staff->blackoutDates ?? [])) }};

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
