@extends('layouts.frontend.frontend')

@section('title', 'Create New Password | Merinaz')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <h1 class="h3 mb-3 font-weight-bold">Create New Password</h1>
                <p class="text-muted small mb-4">Please enter your new password below.</p>

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold small">Email Address</label>
                        {{-- Fix: Pull email directly from the URL query string --}}
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                               name="email" value="{{ request('email') ?? old('email') }}" required readonly>
                        @error('email') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold small">New Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                               id="password" name="password" required autofocus>
                        {{-- Added Password Requirements Hint --}}
                        <div class="form-text small text-muted mt-1">
                            Password must be at least 8 characters and contain at least one uppercase letter, one number, and one symbol.
                        </div>
                        @error('password') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label fw-bold small">Confirm New Password</label>
                        <input type="password" class="form-control" id="password_confirmation"
                               name="password_confirmation" required>
                    </div>

                    <div class="mb-4">
                        <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                        @error('g-recaptcha-response') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="btn btn-dark w-100 fw-bold">Reset Password</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endpush
