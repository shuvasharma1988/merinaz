@extends('layouts.frontend.frontend')

@section('title', 'Reset Password | Merinaz')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <h1 class="h3 mb-3 font-weight-bold">Reset Password</h1>
                <p class="text-muted small mb-4">Enter your email address and we will send you a link to reset your password.</p>

                @if (session('status'))
                    <div class="alert alert-success small">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold small">Email Address</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                               name="email" value="{{ old('email') }}" required autofocus>
                        @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4">
                        <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                        @error('g-recaptcha-response') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="btn btn-dark w-100 fw-bold">Send Reset Link</button>
                </form>

                <div class="mt-4 text-center small">
                    <a href="{{ route('login') }}" class="text-decoration-none text-muted">← Back to login</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endpush
