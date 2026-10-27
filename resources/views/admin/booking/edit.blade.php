@extends('layouts.admin.admin')

@section('title', 'Modify Visit ' . $booking->booking_reference)

@section('content')
    <section class="mz-view on" id="view-booking-edit">
        <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-quiet btn-sm mb-2">
            ← Back to visit
        </a>

        <div class="view-head">
            <div>
                <h1>Modify Visit {{ $booking->booking_reference }}</h1>
                <p class="muted small mb-0">Reassign staff, change locations, or edit the service itinerary.</p>
            </div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger mt-2">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.bookings.update', $booking) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="detail-grid">
                <div>
                    {{-- Step 1: Location & Date --}}
                    <div class="tone section-gap">
                        <h2>1 · Location & Date</h2>
                        <div class="row g-2 mb-2">
                            <div class="col-md-6">
                                <label class="form-label" for="location_id">Studio Location</label>
                                <select class="form-select @error('location_id') is-invalid @enderror" id="location_id" name="location_id" required>
                                    @foreach($locations ?? [] as $location)
                                        <option value="{{ $location->id }}" {{ old('location_id', $booking->location_id) ==$location->id ? 'selected' : '' }}>
                                            {{ $location->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="appointment_date">Date</label>
                                <input type="date" class="form-control @error('appointment_date') is-invalid @enderror" id="appointment_date" name="appointment_date" value="{{ old('appointment_date', $booking->appointment_date->format('Y-m-d')) }}" required>
                            </div>
                        </div>
                    </div>

                    {{-- Step 2: Multi-Service Array --}}
                    <div class="tone section-gap">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2>2 · Modify Services</h2>
                            <button type="button" class="btn btn-ink btn-sm" id="add_service_btn">
                                + Add Service
                            </button>
                        </div>
                        <p class="small muted mb-3" id="service_warning" style="display:none;">Loading available staff...</p>

                        <div id="services_container">
                            {{-- Pre-load existing items --}}
                            @php $i = 0; @endphp
                            @foreach(old('items', $booking->items) as $item)
                                @php
                                    // Handle both Eloquent models and old() arrays gracefully
                                    $sId = is_array($item) ? ($item['service_id'] ?? '') :$item->service_id;
                                    $stId = is_array($item) ? ($item['staff_id'] ?? '') :$item->staff_id;
                                    $sTime = is_array($item) ? ($item['start_time'] ?? '') :$item->start_time->format('H:i');
                                @endphp
                                <div class="service-row border p-3 rounded mb-2 bg-white" data-existing-staff="{{ $stId }}">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <strong class="small">Service Item</strong>
                                        <button type="button" class="btn btn-quiet btn-sm text-danger remove-service-btn">Remove</button>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-md-12 mb-2">
                                            <select class="form-select service-select" name="items[{{$i}}][service_id]" required>
                                                @foreach($services ?? [] as $service)
                                                    <option value="{{ $service->id }}" {{ $sId ==$service->id ? 'selected' : '' }}>
                                                        {{ $service->name }} · ${{$service->price }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <select class="form-select staff-select" name="items[{{$i}}][staff_id]" required>
                                                <option value="{{ $stId }}" selected>Loading staff...</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="time" class="form-control time-input" name="items[{{$i}}][start_time]" value="{{ $sTime }}" required>
                                        </div>
                                    </div>
                                </div>
                                @php $i++; @endphp
                            @endforeach
                        </div>
                    </div>

                    {{-- Step 3: Notes --}}
                    <div class="tone section-gap">
                        <h2>3 · Financials & Notes</h2>
                        <div class="row g-2 mb-2">
                            <div class="col-md-6">
                                <label class="form-label" for="discount">Discount ($)</label>
                                <input type="number" step="0.01" class="form-control" id="discount" name="discount" value="{{ old('discount', $booking->discount) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="tax">Tax ($)</label>
                                <input type="number" step="0.01" class="form-control" id="tax" name="tax" value="{{ old('tax', $booking->tax) }}">
                            </div>
                        </div>
                        <label class="form-label" for="customer_notes">Customer Notes</label>
                        <textarea class="form-control mb-2" id="customer_notes" name="customer_notes" rows="2">{{ old('customer_notes', $booking->customer_notes) }}</textarea>

                        <label class="form-label" for="staff_notes">Internal Staff Notes</label>
                        <textarea class="form-control" id="staff_notes" name="staff_notes" rows="2">{{ old('staff_notes', $booking->staff_notes) }}</textarea>
                    </div>
                </div>

                {{-- Right Summary Panel --}}
                <div>
                    <div class="tone sticky-top">
                        <h3>Update Action</h3>
                        <p class="small mb-3 text-muted">Saving these changes will execute a full availability check. If a collision is found, changes will be blocked.</p>
                        <button type="submit" class="btn btn-accent btn-sm w-100 mt-3">
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </form>

        {{-- Hidden Template for New Service Rows --}}
        <template id="service_row_template">
            <div class="service-row border p-3 rounded mb-2 bg-white">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <strong class="small">New Service Item</strong>
                    <button type="button" class="btn btn-quiet btn-sm text-danger remove-service-btn">Remove</button>
                </div>
                <div class="row g-2">
                    <div class="col-md-12 mb-2">
                        <select class="form-select service-select" required>
                            <option value="" selected disabled>Select Service...</option>
                            @foreach($services ?? [] as $service)
                                <option value="{{ $service->id }}">{{ $service->name }} · ${{$service->price }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <select class="form-select staff-select" required>
                            <option value="" selected disabled>Loading staff...</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <input type="time" class="form-control time-input" required>
                    </div>
                </div>
            </div>
        </template>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let serviceIndex = {{ $i }};
            let currentStaff = [];

            const locationSelect = document.getElementById('location_id');
            const dateInput = document.getElementById('appointment_date');
            const addServiceBtn = document.getElementById('add_service_btn');
            const servicesContainer = document.getElementById('services_container');
            const warningText = document.getElementById('service_warning');

            // --- Fetch Staff by Location (Same logic as Create view) ---
            async function fetchStaff() {
                const locationId = locationSelect.value;
                const date = dateInput.value;
                if (!locationId || !date) return;

                warningText.style.display = 'block';
                try {
                    const response = await fetch(`/admin/bookings/api/locations/${locationId}/staff?date=${date}`);
                    currentStaff = await response.json();

                    if(currentStaff.length > 0) {
                        warningText.style.display = 'none';
                        updateAllStaffDropdowns();
                    } else {
                        warningText.innerText = "Warning: No staff scheduled for this location/date.";
                    }
                } catch (error) {
                    console.error("Error fetching staff:", error);
                }
            }

            locationSelect.addEventListener('change', fetchStaff);
            dateInput.addEventListener('change', fetchStaff);

            function updateAllStaffDropdowns() {
                document.querySelectorAll('.service-row').forEach(row => {
                    const select = row.querySelector('.staff-select');
                    // On first load, check if the row has a data-existing-staff attribute to maintain selection
                    const selectedVal = row.dataset.existingStaff || select.value;

                    select.innerHTML = '<option value="" selected disabled>Assign Staff...</option>';
                    currentStaff.forEach(staff => {
                        const option = document.createElement('option');
                        option.value = staff.id;
                        option.textContent = staff.name;
                        if(staff.id == selectedVal) option.selected = true;
                        select.appendChild(option);
                    });

                    // Clear the dataset so it doesn't force this selection if the user changes location again
                    row.dataset.existingStaff = '';
                });
            }

            // --- Multi-Service Row Logic ---
            addServiceBtn.addEventListener('click', () => {
                const template = document.getElementById('service_row_template').content.cloneNode(true);
                const row = template.querySelector('.service-row');

                row.querySelector('.service-select').name = `items[${serviceIndex}][service_id]`;
                const staffSelect = row.querySelector('.staff-select');
                staffSelect.name = `items[${serviceIndex}][staff_id]`;
                row.querySelector('.time-input').name = `items[${serviceIndex}][start_time]`;

                staffSelect.innerHTML = '<option value="" selected disabled>Assign Staff...</option>';
                currentStaff.forEach(staff => {
                    staffSelect.add(new Option(staff.name, staff.id));
                });

                row.querySelector('.remove-service-btn').addEventListener('click', () => row.remove());

                servicesContainer.appendChild(row);
                serviceIndex++;
            });

            // Bind remove event to existing rows pre-loaded by Blade
            document.querySelectorAll('.remove-service-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    this.closest('.service-row').remove();
                });
            });

            // Trigger fetch on load to populate the staff dropdowns for the current inputs
            fetchStaff();
        });
    </script>
@endsection
