@extends('layouts.auth')

@section('title', 'Log in · Merinaz Admin')

@section('content')
    <h1 class="h5 fw-bold mb-1">Log in</h1>
    <p class="muted small mb-3">Front desk and management access. Use your work email.</p>

    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        <div class="mb-3 text-start">
            <label class="form-label" for="email">Email</label>
            <input
                class="form-control @error('email') input-error @enderror"
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                placeholder="merinaz@merinazdc.com"
            >
            @error('email')
            <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3 text-start">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label class="form-label mb-0" for="password">Password</label>
                @if (Route::has('password.request'))
                    <a class="tiny" href="{{ route('password.request') }}">Forgot password?</a>
                @endif
            </div>
            <input
                class="form-control @error('password') input-error @enderror"
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
            >
            @error('password')
            <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label small" for="remember">Keep me logged in</label>
            </div>
        </div>

        <button class="btn btn-accent w-100" type="submit">Log in to Merinaz admin</button>
    </form>

    @if (Route::has('register'))
        <div class="mt-3 text-center">
            <span class="small muted">Need an account?</span>
            <a href="{{ route('register') }}" class="small fw-bold ms-1">Register staff profile</a>
        </div>
    @endif
@endsection
