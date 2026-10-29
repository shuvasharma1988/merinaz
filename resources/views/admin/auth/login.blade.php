<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- CSRF Token for XHR requests if needed --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Merinaz Admin Login</title>

    {{-- Typography --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Vendor Styles --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    {{-- Merinaz Admin Custom Styles --}}
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body data-role="guest">
<main class="login-wrap" id="loginWrap">
    <div class="login-card" role="dialog" aria-modal="true" aria-labelledby="loginTitle">
        <span class="wordmark">Merinaz</span>
        <div class="brand-sub mb-1">Admin Portal</div>

        <h1 id="loginTitle" style="font-size:18px; margin:12px 0 4px;">Log in</h1>
        <p class="muted small" style="margin-bottom:16px;">Front desk and owner access. Use your work email.</p>

        {{-- Backend Validation / Error Alert --}}
        @if ($errors->any())
            <div class="rate-box mb-3" role="alert">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Session Status Alert --}}
        @if (session('status'))
            <div class="rate-box mb-3" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login') }}" id="loginForm" novalidate>
            {{-- CSRF Protection --}}
            @csrf

            <div class="mb-3">
                <label class="form-label" for="loginEmail">Email</label>
                <input class="form-control @error('email') input-error @enderror"
                       id="loginEmail"
                       name="email"
                       type="email"
                       value="{{ old('email') }}"
                       autocomplete="username"
                       required
                       autofocus>
            </div>

            <div class="mb-3">
                <label class="form-label" for="loginPass">Password</label>
                <input class="form-control @error('password') input-error @enderror"
                       id="loginPass"
                       name="password"
                       type="password"
                       autocomplete="current-password"
                       required>
            </div>

            {{-- Bot Protection: Google reCAPTCHA --}}
            <div class="mb-3">
                <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                @error('g-recaptcha-response')
                <div class="field-error mt-1" style="color: var(--mz-accent-dark); font-size: 12.5px; font-weight: 600;">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="mb-3 form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label small" for="remember">
                    Remember this device
                </label>
            </div>

            <button class="btn btn-accent w-100" type="submit" id="loginBtn">
                Log in to Merinaz admin
            </button>
        </form>

        <p class="tiny muted mt-3 mb-0">Protected area. Sessions expire after 60 minutes of inactivity.</p>
    </div>
</main>

{{-- Bootstrap Bundle --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

