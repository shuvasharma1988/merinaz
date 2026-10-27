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

        <form action="{{ route('booking.details') }}" method="POST" id="publicBookingForm">
            @csrf
            <div class="row g-4">
                <div class="col-lg-8 d-grid gap-4">

                    <!-- 1. Location Selection -->
                    <div class="m-card p-4">
                        <h2 class="h5 fw-bold"><span class="step-num me-2">1</span> Select Studio</h2>
                        <select class="form-select mt-3" name="location_id" id="locationSelect" required>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ $selectedLocation == $loc->id ? 'selected' : '' }}>
                                    {{ $loc->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 2. Multi-Service Selection -->
                    <div class="m-card p-4">
                        <h2 class="h5 fw-bold"><span class="step-num me-2">2</span> Select Services</h2>
                        <p class="small text-muted mb-3">Select one or more treatments for this visit.</p>

                        <div class="row g-2" id="serviceGrid">
                            @foreach($services as $service)
                                <div class="col-md-6">
                                    <label class="service-pick w-100 text-start d-block position-relative p-3 border rounded" style="cursor:pointer;">
                                        <input type="checkbox" name="services[]" value="{{ $service->id }}" class="form-check-input position-absolute top-0 end-0 m-3 service-checkbox" data-price="{{ $service->price }}" data-duration="{{ $service->duration_minutes ?? $service->duration ?? 30 }}" data-name="{{ $service->name }}">
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

                    <!-- 3. Staff Selection -->
                    <div class="m-card p-4">
                        <h2 class="h5 fw-bold"><span class="step-num me-2">3</span> Choose Specialist</h2>
                        <p class="small text-muted mb-3">Select your preferred provider or choose first available.</p>
                        <div id="staffContainer" class="d-flex gap-2 flex-wrap">
                            <label class="badge-sand p-2 px-3 border rounded" style="cursor:pointer;">
                                <input type="radio" name="staff_id" value="" class="form-check-input me-1 staff-radio" checked>
                                First Available
                            </label>

                            @foreach($staffList as $staff)
                                <label class="badge-sand p-2 px-3 border rounded" style="cursor:pointer;">
                                    <input type="radio" name="staff_id" value="{{ $staff->id }}" class="form-check-input me-1 staff-radio">
                                    {{ $staff->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- 4. Date & Time Selection -->
                    <div class="m-card p-4">
                        <h2 class="h5 fw-bold"><span class="step-num me-2">4</span> Choose Date & Time</h2>
                        <input type="date" class="form-control mt-3 mb-3" name="date" id="dateSelect" min="{{ date('Y-m-d') }}" required disabled>

                        <div class="row g-2 mt-2" id="timeSlotsContainer">
                            <div class="col-12 text-muted small py-3" id="timeSlotMessage">
                                Select services and a date to view available times.
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
                            <p class="text-muted">No services selected.</p>
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
        .slot-btn { border: 1.5px solid var(--line); background: #fff; color: var(--ink); border-radius: 12px; padding: 0.65rem 0.4rem; font-weight: 600; font-size: 0.9rem; transition: 0.2s; }
        .slot-btn:hover { border-color: var(--ink); }
        .slot-btn.selected { background: var(--ink); color: #fff; border-color: var(--ink); }
        .selected-service-ui { border-color: var(--ink) !important; background: var(--cream) !important; }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.service-checkbox');
            const staffRadios = document.querySelectorAll('.staff-radio');
            const dateInput = document.getElementById('dateSelect');
            const timeInput = document.getElementById('selectedTime');
            const timeSlotsContainer = document.getElementById('timeSlotsContainer');
            const timeSlotMessage = document.getElementById('timeSlotMessage');
            const locationSelect = document.getElementById('locationSelect');

            const cartItemsContainer = document.getElementById('cartItems');
            const cartTotalElement = document.getElementById('cartTotal');
            const continueBtn = document.getElementById('continueBtn');
            const summaryDateTime = document.getElementById('summaryDateTime');
            const sumDate = document.getElementById('sumDate');
            const sumTime = document.getElementById('sumTime');

            function updateCart() {
                let total = 0;
                let cartHTML = '';
                const selected = document.querySelectorAll('.service-checkbox:checked');

                if (selected.length === 0) {
                    cartItemsContainer.innerHTML = '<p class="text-muted mb-0">No services selected.</p>';
                    cartTotalElement.textContent = '$0.00';
                    dateInput.disabled = true;
                    timeInput.value = '';
                    renderTimeSlots([]);
                    validateForm();
                    return;
                }

                dateInput.disabled = false;

                selected.forEach(cb => {
                    const price = parseFloat(cb.dataset.price);
                    total += price;
                    cartHTML += `
                    <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                        <div>
                            <strong class="d-block">${cb.dataset.name}</strong>
                            <small class="text-muted">${cb.dataset.duration} min</small>
                        </div>
                        <strong>$${price.toFixed(2)}</strong>
                    </div>`;
                });

                cartItemsContainer.innerHTML = cartHTML;
                cartTotalElement.textContent = '$' + total.toFixed(2);
                fetchSlotsIfReady();
            }

            function fetchSlotsIfReady() {
                const hasServices = document.querySelectorAll('.service-checkbox:checked').length > 0;
                if (!hasServices || !dateInput.value) {
                    timeSlotMessage.style.display = 'block';
                    return;
                }

                timeSlotMessage.style.display = 'none';
                timeSlotsContainer.innerHTML = '<div class="col-12 small text-muted"><div class="spinner-border spinner-border-sm" role="status"></div> Loading times...</div>';

                const selectedServices = Array.from(document.querySelectorAll('.service-checkbox:checked')).map(cb => cb.value);
                const staffId = document.querySelector('.staff-radio:checked')?.value || '';

                const params = new URLSearchParams({
                    location_id: locationSelect.value,
                    date: dateInput.value,
                    staff_id: staffId
                });
                selectedServices.forEach(id => params.append('services[]', id));

                fetch(`/book/api/slots?${params.toString()}`)
                    .then(res => res.json())
                    .then(data => renderTimeSlots(data))
                    .catch(err => {
                        console.error(err);
                        timeSlotsContainer.innerHTML = '<div class="col-12 text-danger small">Error loading slots.</div>';
                    });
            }

            function renderTimeSlots(slots) {
                timeSlotsContainer.innerHTML = '';
                timeInput.value = '';

                if (slots.length === 0) {
                    timeSlotsContainer.innerHTML = '<div class="col-12 small text-muted py-2">No availability on this date. Please try another day.</div>';
                    validateForm();
                    return;
                }

                slots.forEach(slot => {
                    const col = document.createElement('div');
                    col.className = 'col-4 col-md-3';
                    col.innerHTML = `<button type="button" class="slot-btn w-100" data-time="${slot.time}">${slot.label}</button>`;
                    timeSlotsContainer.appendChild(col);
                });

                document.querySelectorAll('.slot-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('selected'));
                        this.classList.add('selected');
                        timeInput.value = this.dataset.time;
                        sumTime.textContent = this.textContent;
                        validateForm();
                    });
                });
            }

            function validateForm() {
                const hasServices = document.querySelectorAll('.service-checkbox:checked').length > 0;
                const hasTime = timeInput.value !== '';

                if (dateInput.value && hasTime) {
                    summaryDateTime.classList.remove('d-none');
                    sumDate.textContent = new Date(dateInput.value + 'T00:00:00').toLocaleDateString(undefined, { weekday: 'long', month: 'long', day: 'numeric' });
                } else {
                    summaryDateTime.classList.add('d-none');
                }

                continueBtn.disabled = !(hasServices && hasTime && dateInput.value);
            }

            // Attach listeners
            checkboxes.forEach(cb => {
                cb.addEventListener('change', () => {
                    const parentLabel = cb.closest('.service-pick');
                    cb.checked ? parentLabel.classList.add('selected-service-ui') : parentLabel.classList.remove('selected-service-ui');
                    updateCart();
                });
            });

            staffRadios.forEach(radio => radio.addEventListener('change', fetchSlotsIfReady));
            dateInput.addEventListener('change', fetchSlotsIfReady);
            locationSelect.addEventListener('change', fetchSlotsIfReady);

            updateCart(); // Initialize
        });
    </script>
@endpush
