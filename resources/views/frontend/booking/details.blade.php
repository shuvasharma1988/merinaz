@extends('layouts.frontend.frontend')
@section('title', 'Your Details | Merinaz Booking')

@section('content')
    <section class="container section-pad">
        <nav class="breadcrumb-mer mb-3" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Home</a><span>/</span>
            <a href="{{ route('booking.index', ['location' => $location->id]) }}">Book</a><span>/</span>
            <span aria-current="page">Details</span>
        </nav>

        <div class="d-flex align-items-center gap-3 mb-3">
            <span class="badge-soft">Step 2 of 2</span>
            <h1 class="h2 fw-bold mb-0">Your details</h1>
        </div>

        <div class="progress mb-4" style="height:10px;border-radius:100px;background:#EDE3D3" aria-hidden="true">
            <div class="progress-bar" style="width:85%;background:var(--accent);border-radius:100px"></div>
        </div>

        @if($errors->any())
            <div class="alert alert-danger rounded-4 mb-4">
                <ul class="mb-0 small">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="m-card p-4 p-md-5">
                    <form action="{{ route('booking.store') }}" method="POST" id="bookForm" novalidate>
                        @csrf

                        {{-- Hidden payloads mapped from Step 1 --}}
                        <input type="hidden" name="location_id" value="{{ $validated['location_id'] }}">
                        <input type="hidden" name="date" value="{{ $validated['date'] }}">
                        <input type="hidden" name="time" value="{{ $validated['time'] }}">
                        <input type="hidden" name="service_id" value="{{ $validated['service_id'] }}">
                        @if(isset($validated['staff_id']))
                            <input type="hidden" name="staff_id" value="{{ $validated['staff_id'] }}">
                        @endif

                        {{-- Clean PHP Block for User Pre-filling to prevent Blade compiler crashes --}}
                        @php
                            $user = auth()->user();
                            $clientProfile = $user ? $user->clientProfile : null;

                            $firstName = '';
                            $lastName = '';

                            if ($user && $user->name) {
                                $nameParts = explode(' ', $user->name, 2);
                                $firstName = $nameParts[0];
                                $lastName = $nameParts[1] ?? '';
                            }
                        @endphp

                        @guest
                            <div class="mb-4 p-3 rounded-4 d-flex justify-content-between align-items-center flex-wrap gap-2" style="background:var(--sage-bg); border:1px solid #C2D8CC;">
                                <strong class="small">Already have an account?</strong>
                                <a href="{{ route('login') }}" class="btn btn-light-mer btn-sm-mer rounded-pill">Log in for faster booking</a>
                            </div>
                        @endguest

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="bFirst">First name *</label>
                                <input type="text" name="first_name" id="bFirst" class="form-control @error('first_name') is-invalid @enderror" required autocomplete="given-name" placeholder="Amina" value="{{ old('first_name', $firstName) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="bLast">Last name *</label>
                                <input type="text" name="last_name" id="bLast" class="form-control @error('last_name') is-invalid @enderror" required autocomplete="family-name" placeholder="Khan" value="{{ old('last_name', $lastName) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="bPhone">Mobile *</label>
                                <input type="tel" name="phone" id="bPhone" class="form-control @error('phone') is-invalid @enderror" required autocomplete="tel" placeholder="(202) 555-0148" value="{{ old('phone', $clientProfile?->phone ?? '') }}">
                                <div class="form-text small">We text confirmations — please add mobile.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="bEmail">Email *</label>
                                <input type="email" name="email" id="bEmail" class="form-control @error('email') is-invalid @enderror" required autocomplete="email" placeholder="you@email.com" value="{{ old('email', $user?->email ?? '') }}" {{ $user ? 'readonly' : '' }}>
                            </div>

                            <div class="col-12 mt-3">
                                <label class="form-label">Visit type</label>
                                <div class="d-flex gap-2 flex-wrap">
                                    <label class="badge-sand p-2 px-3">
                                        <input type="radio" name="visit_type" value="first_visit" class="form-check-input me-1" {{ old('visit_type') == 'first_visit' || !$user ? 'checked' : '' }}> First visit
                                    </label>
                                    <label class="badge-sand p-2 px-3">
                                        <input type="radio" name="visit_type" value="returning" class="form-check-input me-1" {{ old('visit_type') == 'returning' || $user ? 'checked' : '' }}> Returning
                                    </label>
                                    <label class="badge-sand p-2 px-3">
                                        <input type="radio" name="visit_type" value="touch_up" class="form-check-input me-1" {{ old('visit_type') == 'touch_up' ? 'checked' : '' }}> Touch-up (7 days)
                                    </label>
                                </div>
                            </div>

                            <div class="col-12 mt-2">
                                <label class="form-label" for="bNotes">Notes for your esthetician <span class="text-muted">(skin sensitivities, pregnancy, medications…)</span></label>
                                <textarea name="customer_notes" id="bNotes" class="form-control" rows="3" placeholder="Sensitive skin, prefer female provider, allergic to lavender…">{{ old('customer_notes') }}</textarea>
                            </div>

                            <div class="col-12 mt-4">
                                <div class="p-3 rounded-4" style="background:var(--cream-2);border:1px solid var(--line)">
                                    <div class="form-check">
                                        <input class="form-check-input @error('terms_consent') is-invalid @enderror" type="checkbox" name="terms_consent" id="bConsent" required {{ old('terms_consent') ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="bConsent">
                                            I confirm I’m 18+ (or with guardian), and I agree to the <a href="{{ route('privacy') }}" target="_blank">cancellation policy</a> — free up to 4 hrs before, 50% after. *
                                        </label>
                                    </div>
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="marketing_opt_in" id="bRemind" checked>
                                        <label class="form-check-label small" for="bRemind">Text me reminders + aftercare tips (recommended).</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4 flex-wrap">
                            <a href="{{ route('booking.index') }}" class="btn btn-light-mer btn-merinaz">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary-mer btn-merinaz flex-grow-1">
                                <i class="bi bi-check-circle"></i> Confirm booking — pay in studio
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <aside class="m-card p-4 summary-sticky">
                    <h2 class="h5 fw-bold">Summary</h2>

                    <div class="p-3 rounded-3 small mb-4" style="background:var(--cream-2);border:1px solid var(--line)">
                        <div class="fw-bold mb-1" style="color:var(--accent-dark)">{{ $location->name }}</div>
                        <i class="bi bi-calendar me-1"></i> {{ \Carbon\Carbon::parse($validated['date'])->format('l, M j, Y') }}<br>
                        <i class="bi bi-clock me-1"></i> {{ \Carbon\Carbon::parse($validated['time'])->format('g:i A') }}
                    </div>

                    <div class="small d-grid gap-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $selectedService->name }}</strong><br>
                                <span class="text-muted">{{ $selectedService->duration ?? $selectedService->duration_minutes ?? 30 }} min</span>
                            </div>
                            <strong>${{ number_format($selectedService->price, 2) }}</strong>
                        </div>
                    </div>

                    <hr class="divider-soft my-3">

                    <div class="d-flex justify-content-between small">
                        <span class="text-muted">Subtotal</span>
                        <strong>${{ number_format($selectedService->price, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between small mt-1">
                        <span class="text-muted">Booking fee</span>
                        <strong class="text-success">Free</strong>
                    </div>
                    <div class="d-flex justify-content-between fs-5 mt-2 pt-2 border-top">
                        <span>Total due</span>
                        <strong>${{ number_format($selectedService->price, 2) }}</strong>
                    </div>

                    <small class="text-muted d-block mt-2">Pay in studio · Cash, card, Apple Pay</small>

                    <div class="hygiene-strip mt-4 p-3 small d-flex gap-2">
                        <i class="bi bi-shield-check" style="font-size:1.1rem"></i>
                        <div>No card required today. We hold your slot while you confirm.</div>
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection
