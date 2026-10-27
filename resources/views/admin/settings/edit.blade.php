@extends('layouts.admin.admin')

@section('title', 'Settings - Merinaz Admin')

@section('content')
    <section class="mz-view on requires-owner-manager" id="view-site-settings" aria-label="Site settings">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-quiet btn-sm mb-2">
            ← Back to Dashboard
        </a>

        @if(session('success'))
            <div class="alert alert-success py-2 px-3 small mb-3" style="background:#eaf7e3; border-color:#c8e6c9; color:#2e7d32;">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger py-2 px-3 small mb-3" style="background:#fee2e2; color:#991b1b;">
                <strong>Validation Failed:</strong>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="view-head">
                <div>
                    <h1>Site Settings</h1>
                    <p class="muted small mb-0">Studio identity, business hours, social links, and booking policy</p>
                </div>
                <div class="view-actions">
                    <button type="submit" class="btn btn-accent btn-sm">Save Settings</button>
                </div>
            </div>

            {{-- Business Information --}}
            <div class="tone section-gap">
                <h2>Business Information</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="business_name">Business Name *</label>
                        <input type="text" name="business_name" id="business_name" class="form-control @error('business_name') is-invalid @enderror" value="{{ old('business_name', $settings['business_name'] ?? '') }}" required>
                        @error('business_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="address">Address *</label>
                        <input type="text" name="address" id="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', $settings['address'] ?? '') }}" required>
                        @error('address') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="phone">Phone *</label>
                        <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $settings['phone'] ?? '') }}" required>
                        @error('phone') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="email">Email *</label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $settings['email'] ?? $settings['business_email'] ?? '') }}" required>
                        @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Business Hours --}}
            <div class="tone section-gap">
                <h2>Business Hours</h2>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" for="monday_friday">Mon – Fri *</label>
                        <input type="text" name="business_hours[monday_friday]" id="monday_friday" class="form-control @error('business_hours.monday_friday') is-invalid @enderror" value="{{ old('business_hours.monday_friday', $settings['business_hours']['monday_friday'] ?? '9:00 AM - 7:00 PM') }}" required>
                        @error('business_hours.monday_friday') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="saturday">Saturday *</label>
                        <input type="text" name="business_hours[saturday]" id="saturday" class="form-control @error('business_hours.saturday') is-invalid @enderror" value="{{ old('business_hours.saturday', $settings['business_hours']['saturday'] ?? '10:00 AM - 6:00 PM') }}" required>
                        @error('business_hours.saturday') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="sunday">Sunday *</label>
                        <input type="text" name="business_hours[sunday]" id="sunday" class="form-control @error('business_hours.sunday') is-invalid @enderror" value="{{ old('business_hours.sunday', $settings['business_hours']['sunday'] ?? 'Closed') }}" required>
                        @error('business_hours.sunday') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Social Links --}}
            <div class="tone section-gap">
                <h2>Social Links</h2>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" for="social_facebook">Facebook</label>
                        <input type="url" name="social_facebook" id="social_facebook" class="form-control @error('social_facebook') is-invalid @enderror" value="{{ old('social_facebook', $settings['social_facebook'] ?? '') }}">
                        @error('social_facebook') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="social_instagram">Instagram</label>
                        <input type="url" name="social_instagram" id="social_instagram" class="form-control @error('social_instagram') is-invalid @enderror" value="{{ old('social_instagram', $settings['social_instagram'] ?? '') }}">
                        @error('social_instagram') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="social_tiktok">TikTok</label>
                        <input type="url" name="social_tiktok" id="social_tiktok" class="form-control @error('social_tiktok') is-invalid @enderror" value="{{ old('social_tiktok', $settings['social_tiktok'] ?? '') }}">
                        @error('social_tiktok') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Booking Policy --}}
            <div class="tone section-gap">
                <h2>Booking Policy</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="cancellation_window_hours">Cancellation Window (hours) *</label>
                        <input type="number" name="cancellation_window_hours" id="cancellation_window_hours" class="form-control @error('cancellation_window_hours') is-invalid @enderror" value="{{ old('cancellation_window_hours', $settings['cancellation_window_hours'] ?? 24) }}" required min="0">
                        @error('cancellation_window_hours') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="deposit_percentage">Deposit Percentage *</label>
                        <input type="number" name="deposit_percentage" id="deposit_percentage" class="form-control @error('deposit_percentage') is-invalid @enderror" value="{{ old('deposit_percentage', $settings['deposit_percentage'] ?? 0) }}" required min="0" max="100">
                        @error('deposit_percentage') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-accent">Save Settings</button>
            </div>
        </form>
    </section>
@endsection
