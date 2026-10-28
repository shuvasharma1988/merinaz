@extends('layouts.frontend.frontend')

@section('title', 'Log In | Merinaz')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <h1 class="h3 mb-3 font-weight-bold">Log In</h1>
                <p class="text-muted small mb-4">Welcome back to Merinaz. Log in to book and manage your appointments.</p>

                <form method="POST" action="{{ route('login') }}" novalidate>
                    @csrf

                    <div class="mb-3 text-start">
                        <label class="form-label fw-bold small" for="email">Email</label>
                        <input
                            class="form-control @error('email') is-invalid @enderror"
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="you@email.com"
                        >
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 text-start">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold small mb-0" for="password">Password</label>
                            @if (Route::has('password.request'))
                                <a class="small text-decoration-none" href="{{ route('password.request') }}">Forgot password?</a>
                            @endif
                        </div>
                        <input
                            class="form-control @error('password') is-invalid @enderror"
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                        >
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label small" for="remember">Keep me logged in</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                        @error('g-recaptcha-response') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <button class="btn btn-dark w-100 fw-bold" type="submit">Log In</button>
                </form>

                @if (Route::has('register'))
                    <div class="mt-4 text-center small">
                        <span class="text-muted">Need an account?</span>
                        <a href="{{ route('register') }}" class="text-decoration-none fw-bold ms-1">Sign up</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endpush
