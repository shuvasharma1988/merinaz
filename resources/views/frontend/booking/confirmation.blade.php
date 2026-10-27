@extends('layouts.frontend.frontend')
@section('title', 'Booking Confirmed | Merinaz')

@section('content')
    <section class="container section-pad text-center">
        <div class="mx-auto" style="max-width:680px">

            {{-- Success Icon & Status --}}
            <div class="mx-auto mb-3" style="width:84px;height:84px;border-radius:50%;background:#DDEFE6;border:2px solid #9CC8B2;display:grid;place-items:center;font-size:2.2rem;color:#1E6B4F">
                <i class="bi bi-check-lg"></i>
            </div>
            <span class="badge-sage">Confirmed · No prepay</span>

            <h1 class="display-5 fw-bold mt-2">You’re booked, <span id="confName">{{ explode(' ', $appointment->client->name)[0] ?? 'friend' }}</span>!</h1>
            <p class="lead-soft">We’ve emailed your confirmation. Arrive 5 minutes early — herbal tea is on us.</p>

            {{-- Appointment Details Card --}}
            <div class="m-card p-4 text-start mt-4" id="confDetails">
                <div class="row g-3 small">
                    <div class="col-6">
                        <span class="text-muted">Booking Reference</span><br>
                        <strong class="fs-6">{{ $appointment->booking_reference }}</strong>
                    </div>
                    <div class="col-6 text-end">
                        <span class="text-muted">Total (due in studio)</span><br>
                        <strong class="fs-6">${{ number_format($appointment->total, 2) }}</strong>
                    </div>

                    <div class="col-12 mt-3 pt-3 border-top">
                        <span class="text-muted d-block mb-2">Your Itinerary</span>

                        @foreach($appointment->items as $item)
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 {{ !$loop->last ? 'border-bottom border-light' : '' }}">
                                <div>
                                    <strong class="d-block">{{ $item->service->name }}</strong>
                                    <span class="text-muted">with {{ $item->staff->name ?? 'Next Available' }}</span>
                                </div>
                                <div class="text-end">
                                    <strong>{{ \Carbon\Carbon::parse($item->start_time)->format('g:i A') }}</strong><br>
                                    <span class="text-muted">{{ $item->service->duration_minutes ?? $item->service->duration ?? 30 }} min</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="col-12 p-3 rounded-3 mt-3" style="background:var(--cream-2);border:1px solid var(--line)">
                        Date: <strong>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('l, F j, Y') }}</strong><br>
                        Location: <strong>{{ $appointment->location->name }}</strong>
                    </div>
                </div>
            </div>

            {{-- Location & Actions Card --}}
            <div class="m-card p-4 mt-3 text-start">
                <h2 class="h6 fw-bold"><i class="bi bi-geo-alt me-1"></i> How to find us</h2>
                <p class="small text-muted mb-3">
                    {{ $appointment->location->address ?? '2436 18th St NW' }} · Ring the bell · 2nd floor, Suite B. Early? Tivoli Coffee next door validates 30 min.
                </p>

                <div class="d-flex gap-2 flex-wrap justify-content-center justify-content-sm-start">
                    <button class="btn btn-dark-mer btn-merinaz btn-sm-mer" id="icsBtn">
                        <i class="bi bi-calendar-plus"></i> Add to calendar
                    </button>
                    @auth
                        <a href="#" class="btn btn-light-mer btn-merinaz btn-sm-mer">
                            <i class="bi bi-person"></i> View my visits
                        </a>
                    @endauth
                    <a href="{{ route('home') }}" class="btn btn-light-mer btn-merinaz btn-sm-mer">
                        <i class="bi bi-house"></i> Home
                    </a>
                </div>
            </div>

        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const icsBtn = document.getElementById('icsBtn');
            if(icsBtn) {
                icsBtn.addEventListener('click', function() {
                    // Generate a minimal ICS file based on the appointment data
                    const dateStr = "{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('Ymd') }}";
                    const timeStr = "{{ \Carbon\Carbon::parse($appointment->start_time)->format('His') }}";
                    const endStr = "{{ \Carbon\Carbon::parse($appointment->items->last()->end_time)->format('His') }}";

                    const ics = `BEGIN:VCALENDAR\nVERSION:2.0\nBEGIN:VEVENT\nSUMMARY:Merinaz Salon Visit\nDESCRIPTION:Booking Ref: {{ $appointment->booking_reference }}\\nServices: {{ $appointment->items->pluck('service.name')->implode(', ') }}\nLOCATION:{{ $appointment->location->name }}\nDTSTART:${dateStr}T${timeStr}\nDTEND:${dateStr}T${endStr}\nEND:VEVENT\nEND:VCALENDAR`;

                    const blob = new Blob([ics], { type: 'text/calendar;charset=utf-8' });
                    const link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.setAttribute('download', `merinaz-visit-{{ $appointment->booking_reference }}.ics`);
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    alert('Calendar file downloaded.');
                });
            }
        });
    </script>
@endpush
