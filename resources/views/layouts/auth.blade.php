<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Merinaz Admin · Sign in')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
</head>
<body class="login-wrap">
<div class="login-card" role="dialog" aria-modal="true">
    <div class="mb-3 text-start">
        <span class="wordmark">Merinaz</span>
        <div class="brand-sub">Admin · Adams Morgan, Washington DC</div>
    </div>

    {{-- Session Status / Rate Limit / Error Notifications --}}
    @if (session('status'))
        <div class="rate-box mb-3 text-success border-success" role="alert" style="background: var(--mz-surface-2);">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rate-box mb-3" role="alert">
            @if ($errors->has('throttle'))
                {{ $errors->first('throttle') }}
            @else
                {{ $errors->first() }}
            @endif
        </div>
    @endif

    @yield('content')

    <div class="plain-divide pt-2 mt-3">
        <p class="tiny muted mb-0 text-center">Protected salon area. Authorized staff and managers only.</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/admin.js') }}"></script>
</body>
</html>
