<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') · Merinaz Admin</title>

    {{-- Typography --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Vendor Styles --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('assets/admin/css/admin.css') }}" rel="stylesheet">
</head>
<body data-role="guest" class="d-flex align-items-center justify-content-center min-vh-100 p-3">
<main class="tone text-start" style="max-width: 540px; width: 100%;">
    <div class="tiny muted fw-bold text-uppercase mb-1">@yield('code_label')</div>
    <h1 class="h4 fw-bold mt-1 mb-2">@yield('heading')</h1>
    <p class="small muted mb-4">@yield('message')</p>
    <div>
        @yield('actions')
    </div>
</main>
</body>
</html>
