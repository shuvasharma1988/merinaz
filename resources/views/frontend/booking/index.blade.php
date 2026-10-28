@extends('layouts.frontend.frontend')
@section('title', 'Book Appointment | Merinaz')

@section('content')
    <section class="container section-pad">
        <nav class="breadcrumb-mer mb-3" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Home</a><span>/</span><span aria-current="page">Book · Step 1</span>
        </nav>

        <div class="d-flex align-items-center gap-3 mb-3">
            <span class="badge-soft">Step 1 of 2</span>
            <h1 class="h2 fw-bold mb-0">Build your visit</h1>
        </div>

        @if($errors->any())
            <div class="alert alert-danger rounded-4 mb-4">
                <strong>Booking Could Not Be Completed:</strong>
                <ul class="mb-0 small mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('booking.details') }}" method="POST" id="publicBookingForm">
            @csrf
            <div class="row g-4">
                <div class="col-lg-8 d-grid gap-4">

                    <!-- 1. Location Selection -->
                    <div class="m-card p-4" id="stepLocation">
                        <h2 class="h5 fw-bold"><span class="step-num me-2">1</span> Select Studio</h2>
                        <select class="form-select mt-3 border-dark" name="location_id" id="locationSelect" required>
                            <option value="" selected disabled>Choose a location...</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ $selectedLocation == $loc->id ? 'selected' : '' }}>
                                    {{ $loc->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 2. Date Selection -->
                    <div class="m-card p-4 locked-step" id="stepDate">
                        <h2 class="h5 fw-bold"><span class="step-num me-2">2</span> Choose Date</h2>
                        <input type="date" class="form-control mt-3" name="date" id="dateSelect" min="{{ date('Y-m-d') }}" required disabled>
                    </div>

                    <!-- 3. Service Selection (Single Radio) -->
                    <div class="m-card p-4 locked-step" id="stepServices">
                        <h2 class="h5 fw-bold"><span class="step-num me-2">3</span> Select Service</h2>
                        <p class="small text-muted mb-3">Please choose one primary service for this visit.</p>

                        <div class="accordion" id="servicesAccordion">
                            @php $catIndex = 0; @endphp
                            @foreach($servicesByCategory as $categoryName => $servicesList)
                                <div class="accordion-item mb-2" style="border:1px solid var(--line); border-radius:12px; overflow:hidden;">
                                    <h3 class="accordion-header">
                                        <button class="accordion-button {{ $catIndex === 0 ? '' : 'collapsed' }} fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCat{{ $catIndex }}">
                                            {{ $categoryName }}
                                        </button>
                                    </h3>
                                    <div id="collapseCat{{ $catIndex }}" class="accordion-collapse collapse {{ $catIndex === 0 ? 'show' : '' }}" data-bs-parent="#servicesAccordion">
                                        <div class="accordion-body bg-light">
                                            <div class="row g-2">
                                                @foreach($servicesList as $service)
                                                    <div class="col-md-6">
                                                        <label class="service-pick w-100 text-start d-block position-relative p-3 border rounded bg-white" style="cursor:pointer; height:100%;">
                                                            <input type="radio" name="service_id" value="{{ $service->id }}" class="form-check-input position-absolute top-0 end-0 m-3 service-radio" data-price="{{ $service->price }}" data-duration="{{ $service->duration_minutes ?? $service->duration ?? 30 }}" data-name="{{ $service->name }}" required>
                                                            <div class="d-flex justify-content-between pe-4">
                                                                <strong class="small">{{ $service->name }}</strong>
                                                                <strong>${{ number_format($service->price, 2) }}</strong>
                                                            </div>
                                                            <small class="text-muted">{{ $service->duration_minutes ?? $service->duration ?? 30 }} min</small>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @php $catIndex++; @endphp
                            @endforeach
                        </div>
                    </div>

                    <!-- 4. Staff Selection -->
                    <div class="m-card p-4 locked-step" id="stepStaff">
                        <h2 class="h5 fw-bold"><span class="step-num me-2">4</span> Choose Specialist</h2>
                        <p class="small text-muted mb-3" id="staffMessage">Select a service above to see available specialists.</p>
                        <div id="staffContainer" class="d-flex gap-2 flex-wrap">
                            <!-- Populated via AJAX -->
                        </div>
                    </div>

                    <!-- 5. Time Selection -->
                    <div class="m-card p-4 locked-step" id="stepTime">
                        <h2 class="h5 fw-bold"><span class="step-num me-2">5</span> Pick a Time</h2>
                        <div class="row g-2 mt-2" id="timeSlotsContainer">
                            <div class="col-12 text-muted small py-2" id="timeSlotMessage">
                                Waiting for staff selection...
                            </div>
                        </div>
                        <input type="hidden" name="time" id="selectedTime" required>
                    </div>
                </div>

                <!-- Sticky Summary Sidebar -->
                <div class="col-lg-4">
                    <aside class="m-card p-4 summary-sticky">
                        <h2 class="h5 fw-bold">Your visit</h2>

                        <div class="p-2 rounded-3 small mb-3 mt-3 d-none" id="summaryDateTime" style="background:var(--cream-2);border:1px solid var(--line)">
                            <i class="bi bi-calendar me-1"></i> <span id="sumDate"></span><br>
                            <i class="bi bi-clock me-1"></i> <span id="sumTime"></span>
                        </div>

                        <div id="cartItems" class="small mt-3">
                            <p class="text-muted">No service selected.</p>
                        </div>

                        <hr class="divider-soft">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Total Due</span>
                            <strong id="cartTotal">$0.00</strong>
                        </div>
                        <button type="submit" class="btn btn-primary-mer btn-merinaz w-100" id="continueBtn" disabled>
                            Continue to Details <i class="bi bi-arrow-right"></i>
                        </button>
                    </aside>
                </div>
            </div>
        </form>
    </section>
@endsection

@push('styles')
    <style>
        .locked-step { opacity: 0.4; pointer-events: none; transition: opacity 0.3s ease; }
        .locked-step.unlocked { opacity: 1; pointer-events: auto; }
        .slot-btn { border: 1.5px solid var(--line); background: #fff; color: var(--ink); border-radius: 12px; padding: 0.65rem 0.4rem; font-weight: 600; font-size: 0.9rem; transition: 0.2s; }
        .slot-btn:hover { border-color: var(--ink); }
        .slot-btn.selected { background: var(--ink); color: #fff; border-color: var(--ink); }
        .selected-service-ui { border-color: var(--ink) !important; background: var(--cream) !important; }
        .accordion-button:not(.collapsed) { background-color: var(--blush); color: var(--ink); box-shadow: inset 0 -1px 0 rgba(0,0,0,.125); }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const elLocation = document.getElementById('locationSelect');
            const elDate = document.getElementById('dateSelect');
            const elServiceRadios = document.querySelectorAll('.service-radio');

            const stepDate = document.getElementById('stepDate');
            const stepServices = document.getElementById('stepServices');
            const stepStaff = document.getElementById('stepStaff');
            const stepTime = document.getElementById('stepTime');

            const staffContainer = document.getElementById('staffContainer');
            const staffMessage = document.getElementById('staffMessage');
            const timeSlotsContainer = document.getElementById('timeSlotsContainer');
            const timeSlotMessage = document.getElementById('timeSlotMessage');
            const timeInput = document.getElementById('selectedTime');

            const cartItemsContainer = document.getElementById('cartItems');
            const cartTotalElement = document.getElementById('cartTotal');
            const continueBtn = document.getElementById('continueBtn');
            const summaryDateTime = document.getElementById('summaryDateTime');
            const sumDate = document.getElementById('sumDate');
            const sumTime = document.getElementById('sumTime');

            // --- STEP 1: Location Changed ---
            elLocation.addEventListener('change', function() {
                if(this.value) {
                    stepDate.classList.add('unlocked');
                    elDate.disabled = false;
                }
                elDate.value = '';
                lockStep(stepServices);
                lockStep(stepStaff);
                lockStep(stepTime);
                uncheckAllServices();
                updateCart();
            });

            // --- STEP 2: Date Changed ---
            elDate.addEventListener('change', function() {
                if(this.value) {
                    stepServices.classList.add('unlocked');
                    sumDate.textContent = new Date(this.value + 'T00:00:00').toLocaleDateString(undefined, { weekday: 'long', month: 'long', day: 'numeric' });
                }
                lockStep(stepStaff);
                lockStep(stepTime);
                uncheckAllServices();
                updateCart();
            });

            // --- STEP 3: Service Changed ---
            elServiceRadios.forEach(radio => {
                radio.addEventListener('change', () => {
                    // Remove highlight from all
                    document.querySelectorAll('.service-pick').forEach(el => el.classList.remove('selected-service-ui'));
                    // Add highlight to selected
                    if(radio.checked) {
                        radio.closest('.service-pick').classList.add('selected-service-ui');
                        updateCart();
                        fetchCapableStaff();
                    }
                });
            });

            // --- FETCH STAFF (AJAX) ---
            function fetchCapableStaff() {
                stepStaff.classList.add('unlocked');
                lockStep(stepTime);
                staffContainer.innerHTML = '';
                staffMessage.innerHTML = '<div class="spinner-border spinner-border-sm text-secondary" role="status"></div> Finding available specialists...';

                const selectedService = document.querySelector('.service-radio:checked').value;
                const params = new URLSearchParams({
                    location_id: elLocation.value,
                    date: elDate.value,
                    service_id: selectedService
                });

                fetch(`/book/api/capable-staff?${params.toString()}`)
                    .then(res => res.json())
                    .then(data => {
                        staffMessage.textContent = "Select your preferred specialist.";

                        if (data.length === 0) {
                            staffMessage.innerHTML = '';
                            staffContainer.innerHTML = '<span class="text-danger small fw-bold"><i class="bi bi-exclamation-triangle"></i> No staff member is available for this service on this date.</span>';
                            return;
                        }

                        let html = '';

                        data.forEach(staff => {
                            html += `<label class="badge-sand p-2 px-3 border rounded" style="cursor:pointer;">
                                        <input type="radio" name="staff_id" value="${staff.id}" class="form-check-input me-1 staff-radio">
                                        ${staff.name}
                                     </label>`;
                        });

                        staffContainer.innerHTML = html;

                        document.querySelectorAll('.staff-radio').forEach(radio => {
                            radio.addEventListener('change', fetchTimeSlots);
                        });
                    })
                    .catch(err => {
                        staffMessage.innerHTML = '<span class="text-danger">Failed to load specialists.</span>';
                    });
            }

            // --- FETCH TIME SLOTS (AJAX) ---
            function fetchTimeSlots() {
                stepTime.classList.add('unlocked');
                timeInput.value = '';
                timeSlotsContainer.innerHTML = '<div class="col-12 text-muted small"><div class="spinner-border spinner-border-sm" role="status"></div> Loading available times...</div>';
                validateForm();

                const staffRadio = document.querySelector('.staff-radio:checked');
                const staffId = staffRadio ? staffRadio.value : '';
                const selectedService = document.querySelector('.service-radio:checked').value;

                const params = new URLSearchParams({
                    location_id: elLocation.value,
                    date: elDate.value,
                    staff_id: staffId,
                    service_id: selectedService
                });

                fetch(`/book/api/slots?${params.toString()}`)
                    .then(res => res.json())
                    .then(slots => {
                        timeSlotsContainer.innerHTML = '';

                        if (slots.length === 0) {
                            timeSlotsContainer.innerHTML = '<div class="col-12 small text-danger py-2">No availability for this selection. Try another specialist or date.</div>';
                            return;
                        }

                        slots.forEach(slot => {
                            const col = document.createElement('div');
                            col.className = 'col-4 col-md-3 mb-2';

                            if (slot.is_available) {
                                // Active, clickable slot
                                col.innerHTML = `<button type="button" class="slot-btn w-100" data-time="${slot.time}">${slot.label}</button>`;
                            } else {
                                // Greyed out, disabled slot (Lunch or Booked)
                                col.innerHTML = `<button type="button" class="slot-btn w-100" disabled style="background-color: #f8f9fa; color: #adb5bd; border-color: #dee2e6; cursor: not-allowed;">${slot.label}</button>`;
                            }

                            timeSlotsContainer.appendChild(col);
                        });

                        document.querySelectorAll('.slot-btn').forEach(btn => {
                            btn.addEventListener('click', function() {
                                document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('selected'));
                                this.classList.add('selected');
                                timeInput.value = this.dataset.time;
                                sumTime.textContent = this.textContent;
                                summaryDateTime.classList.remove('d-none');
                                validateForm();
                            });
                        });
                    })
                    .catch(err => {
                        timeSlotsContainer.innerHTML = '<div class="col-12 text-danger small">Error loading slots.</div>';
                    });
            }

            // --- HELPERS ---
            function updateCart() {
                const selected = document.querySelector('.service-radio:checked');

                if (!selected) {
                    cartItemsContainer.innerHTML = '<p class="text-muted mb-0">No service selected.</p>';
                    cartTotalElement.textContent = '$0.00';
                } else {
                    const price = parseFloat(selected.dataset.price);
                    cartItemsContainer.innerHTML = `<div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                                    <div><strong class="d-block">${selected.dataset.name}</strong><small class="text-muted">${selected.dataset.duration} min</small></div>
                                    <strong>$${price.toFixed(2)}</strong>
                                 </div>`;
                    cartTotalElement.textContent = '$' + price.toFixed(2);
                }
                validateForm();
            }

            function lockStep(el) {
                el.classList.remove('unlocked');
                if (el === stepTime) {
                    timeInput.value = '';
                    timeSlotsContainer.innerHTML = '<div class="col-12 text-muted small py-2">Waiting for staff selection...</div>';
                    summaryDateTime.classList.add('d-none');
                }
            }

            function uncheckAllServices() {
                elServiceRadios.forEach(cb => {
                    cb.checked = false;
                    cb.closest('.service-pick').classList.remove('selected-service-ui');
                });
            }

            function validateForm() {
                const hasLocation = elLocation.value !== '';
                const hasDate = elDate.value !== '';
                const hasService = document.querySelector('.service-radio:checked') !== null;
                const hasStaff = document.querySelector('.staff-radio:checked') !== null;
                const hasTime = timeInput.value !== '';

                continueBtn.disabled = !(hasLocation && hasDate && hasService && hasStaff && hasTime);
            }

            if (elLocation.value) {
                elLocation.dispatchEvent(new Event('change'));
            }
        });
    </script>
@endpush
