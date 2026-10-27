<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    {{-- 1. THE CULPRIT FIX: Strict mobile viewport scaling --}}
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    <title>@yield('title', 'Merinaz — Waxing, Threading, Sugaring & Skincare | Adams Morgan, DC')</title>
    <meta name="description" content="@yield('meta_description', 'Merinaz is Adams Morgan\'s calm, hygienic studio for waxing, threading, sugaring and skincare. Same-day appointments. Licensed estheticians.')">

    <link rel="canonical" href="@yield('canonical', url()->current())">
    <link rel="icon" type="image/png" href="{{ asset('images/2c3fcf22-3b57-484b-a6b0-04ca77f440ea_web-logo-merinaz.png') }}">

    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('og_title', 'Merinaz — Waxing, Threading, Sugaring & Skincare')">
    <meta property="og:description" content="@yield('og_description', 'Same-day appointments. Licensed estheticians.')">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    @hasSection('og_image')
        <meta property="og:image" content="@yield('og_image')">
    @endif

    {{-- Bootstrap 5 & Typography --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="{{ asset('css/frontend.css') }}" rel="stylesheet">

    {{-- 2. THE CULPRIT FIX: Global CSS overflow locks --}}
    <style>
        html, body {
            overflow-x: hidden !important;
            width: 100%;
            position: relative;
            margin: 0;
            padding: 0;
        }
    </style>

    @stack('styles')
    @stack('jsonld')
</head>
<body>
<a class="skip-link" href="#main">Skip to main content</a>

@include('layouts.frontend.partials.announce')
@include('layouts.frontend.partials.header')

<main id="main">
    @yield('content')
</main>

@include('layouts.frontend.partials.footer')

{{-- Mobile sticky book bar --}}
<div class="mobile-book d-lg-none">
    <a href="{{ route('contact') }}" class="btn btn-primary-mer btn-merinaz w-100 shadow-lg">
        <i class="bi bi-calendar-check"></i> Book same-day — open till 7 PM
    </a>
</div>

<button class="back-top" id="backTop" type="button" aria-label="Back to top">
    <i class="bi bi-arrow-up"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/frontend.js') }}"></script>
@stack('scripts')
</body>
</html>
