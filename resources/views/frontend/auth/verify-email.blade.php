@extends('layouts.auth')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5 text-center">
                <h1 class="h3 mb-3 font-weight-bold">Verify Your Email</h1>
                <p class="text-muted small mb-4">
                    Thanks for signing up! Before getting started, could you verify your email address by clicking on
                    the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
                </p>

                @if (session('status') == 'verification-link-sent')
                    <div class="alert alert-success small mb-4">
                        A new verification link has been sent to the email address you provided during registration.
                    </div>
                @endif

                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <div class="mb-4 d-flex justify-content-center">
                        <div>
                            <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                            @error('g-recaptcha-response') <span
                                    class="text-danger small d-block mt-2">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-dark fw-bold px-4">Resend Verification Email</button>
                </form>

                <form method="POST" action="{{ route('logout') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="btn btn-link text-muted small text-decoration-none">Log Out</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endpush
