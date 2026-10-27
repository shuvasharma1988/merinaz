@extends('layouts.admin.admin')

@section('title', 'New Booking')

@section('content')
    <section class="mz-view on" id="view-booking-new" aria-label="New booking">
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-quiet btn-sm mb-2">
            ← Back to bookings
        </a>

        <div class="view-head">
            <div>
                <h1>New Multi-Service Visit</h1>
                <p class="muted small mb-0">Select a location, find a client, and allocate services to available staff.</p>
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

        <form action="{{ route('admin.bookings.store') }}" method="POST">
            @csrf
            <input type="hidden" name="booking_source" value="admin">
            <input type="hidden" name="status" value="confirmed">

            <div class="detail-grid">
                <div>
                    {{-- Step 1: Location & Date --}}
                    <div class="tone section-gap">
                        <h2>1 · Location & Date</h2>
                        <div class="row g-2 mb-2">
                            <div class="col-md-6">
                                <label class="form-label" for="location_id">Studio Location</label>
                                <select class="form-select @error('location_id') is-invalid @enderror" id="location_id" name="location_id" required>
                                    <option value="" selected disabled>Choose location...</option>
                                    @foreach($locations ?? [] as $location)
                                        <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                            {{ $location->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="appointment_date">Date</label>
                                <input type="date" class="form-control @error('appointment_date') is-invalid @enderror" id="appointment_date" name="appointment_date" value="{{ old('appointment_date', now()->format('Y-m-d')) }}" required min="{{ now()->format('Y-m-d') }}">
                            </div>
                        </div>
                    </div>

                    {{-- Step 2: Secure Client Search --}}
                    <div class="tone section-gap">
                        <h2>2 · Client</h2>
                        <div class="mb-2 position-relative">
                            <label class="form-label" for="client_search">Search Client</label>
                            <input type="text" class="form-control" id="client_search" placeholder="Type name or email..." autocomplete="off">
                            <div id="client_results" class="list-group position-absolute w-100 shadow-sm" style="z-index: 1000; display: none;"></div>

                            <input type="hidden" id="client_id" name="client_id" value="{{ old('client_id') }}" required>
                            <div id="selected_client_display" class="mt-2 p-2 border rounded bg-light" style="display: {{ old('client_id') ? 'block' : 'none' }};">
                                <span class="small fw-bold text-success">✓ Client Selected</span>
                                <button type="button" class="btn btn-link btn-sm p-0 ms-2 text-danger" id="clear_client">Clear</button>
                            </div>
                        </div>
                    </div>

                    {{-- Step 3: Multi-Service Line Items --}}
                    <div class="tone section-gap">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2>3 · Services</h2>
                            <button type="button" class="btn btn-ink btn-sm" id="add_service_btn" disabled>
                                + Add Service
                            </button>
                        </div>
                        <p class="small muted mb-3" id="service_warning">Please select a location and date to load available staff.</p>

                        <div id="services_container">
                            {{-- Dynamic rows will be injected here --}}
                        </div>
                    </div>

                    {{-- Step 4: Notes --}}
                    <div class="tone section-gap">
                        <h2>4 · Notes & Adjustments</h2>
                        <div class="row g-2 mb-2">
                            <div class="col-md-6">
                                <label class="form-label" for="discount">Discount ($)</label>
                                <input type="number" step="0.01" class="form-control" id="discount" name="discount" value="{{ old('discount', 0) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="tax">Tax ($)</label>
                                <input type="number" step="0.01" class="form-control" id="tax" name="tax" value="{{ old('tax', 0) }}">
                            </div>
                        </div>
                        <label class="form-label" for="customer_notes">Customer Notes (Optional)</label>
                        <textarea class="form-control mb-2" id="customer_notes" name="customer_notes" rows="2">{{ old('customer_notes') }}</textarea>

                        <label class="form-label" for="staff_notes">Internal Staff Notes (Optional)</label>
                        <textarea class="form-control" id="staff_notes" name="staff_notes" rows="2">{{ old('staff_notes') }}</textarea>
                    </div>
                </div>

                {{-- Right Summary Panel --}}
                <div>
                    <div class="tone sticky-top">
                        <h3>Action</h3>
                        <p class="small mb-3 text-muted">A valid client, location, date, and at least one assigned service are required to finalize.</p>
                        <button type="submit" class="btn btn-accent btn-sm w-100 mt-3" id="submit_booking">
                            Create Visit
                        </button>
                    </div>
                </div>
            </div>
        </form>

        {{-- Hidden Template for Service Rows --}}
        <template id="service_row_template">
            <div class="service-row border p-3 rounded mb-2 bg-white">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <strong class="small">Service Item</strong>
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
            let serviceIndex = 0;
            let currentStaff = [];

            const locationSelect = document.getElementById('location_id');
            const dateInput = document.getElementById('appointment_date');
            const addServiceBtn = document.getElementById('add_service_btn');
            const servicesContainer = document.getElementById('services_container');
            const warningText = document.getElementById('service_warning');

            // --- 1. Fetch Staff by Location ---
            async function fetchStaff() {
                const locationId = locationSelect.value;
                const date = dateInput.value;
                if (!locationId || !date) return;

                warningText.innerText = "Loading available staff...";
                try {
                    const response = await fetch(`/admin/bookings/api/locations/${locationId}/staff?date=${date}`);
                    currentStaff = await response.json();

                    if(currentStaff.length > 0) {
                        warningText.style.display = 'none';
                        addServiceBtn.disabled = false;
                        updateAllStaffDropdowns();
                    } else {
                        warningText.innerText = "No staff scheduled for this location/date.";
                        warningText.style.display = 'block';
                        addServiceBtn.disabled = true;
                    }
                } catch (error) {
                    console.error("Error fetching staff:", error);
                }
            }

            locationSelect.addEventListener('change', fetchStaff);
            dateInput.addEventListener('change', fetchStaff);

            function updateAllStaffDropdowns() {
                document.querySelectorAll('.staff-select').forEach(select => {
                    const selectedVal = select.value;
                    select.innerHTML = '<option value="" selected disabled>Assign Staff...</option>';
                    currentStaff.forEach(staff => {
                        const option = document.createElement('option');
                        option.value = staff.id;
                        option.textContent = staff.name;
                        if(staff.id == selectedVal) option.selected = true;
                        select.appendChild(option);
                    });
                });
            }

            // --- 2. Multi-Service Row Logic ---
            addServiceBtn.addEventListener('click', () => {
                const template = document.getElementById('service_row_template').content.cloneNode(true);
                const row = template.querySelector('.service-row');

                // Assign array names
                row.querySelector('.service-select').name = `items[${serviceIndex}][service_id]`;
                const staffSelect = row.querySelector('.staff-select');
                staffSelect.name = `items[${serviceIndex}][staff_id]`;
                row.querySelector('.time-input').name = `items[${serviceIndex}][start_time]`;

                // Populate staff
                staffSelect.innerHTML = '<option value="" selected disabled>Assign Staff...</option>';
                currentStaff.forEach(staff => {
                    staffSelect.add(new Option(staff.name, staff.id));
                });

                // Remove logic
                row.querySelector('.remove-service-btn').addEventListener('click', () => row.remove());

                servicesContainer.appendChild(row);
                serviceIndex++;
            });

            // --- 3. Secure Client Search AJAX ---
            const clientSearch = document.getElementById('client_search');
            const clientResults = document.getElementById('client_results');
            const clientIdInput = document.getElementById('client_id');
            const clientDisplay = document.getElementById('selected_client_display');
            const clearClient = document.getElementById('clear_client');

            clientSearch.addEventListener('input', async function(e) {
                const query = e.target.value;
                if (query.length < 2) {
                    clientResults.style.display = 'none';
                    return;
                }

                const response = await fetch(`/admin/bookings/api/search-clients?q=${query}`);
                const clients = await response.json();

                clientResults.innerHTML = '';
                if(clients.length > 0) {
                    clients.forEach(client => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'list-group-item list-group-item-action';
                        btn.innerText = `${client.name} (${client.email})`;
                        btn.onclick = () => {
                            clientIdInput.value = client.id;
                            clientSearch.value = client.name;
                            clientResults.style.display = 'none';
                            clientDisplay.style.display = 'block';
                        };
                        clientResults.appendChild(btn);
                    });
                    clientResults.style.display = 'block';
                } else {
                    clientResults.innerHTML = '<div class="list-group-item text-muted">No clients found</div>';
                    clientResults.style.display = 'block';
                }
            });

            clearClient.addEventListener('click', () => {
                clientIdInput.value = '';
                clientSearch.value = '';
                clientDisplay.style.display = 'none';
            });

            // Trigger initial fetch if old input exists (e.g., validation failed)
            if(locationSelect.value) fetchStaff();
        });
    </script>
@endsection
