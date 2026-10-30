<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin') · Merinaz Adams Morgan</title>

    {{-- Typography --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Vendor Styles --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta3/dist/css/adminlte.min.css" rel="stylesheet">

    {{-- Extracted Merinaz Custom Style Sheet --}}
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body data-role="{{ auth()->user()->role ?? 'owner' }}">
<a class="visually-hidden-focusable" href="#mainContent">Skip to content</a>

<div class="app-wrapper">
    {{-- Sidebar Partial --}}
    @include('layouts.admin.partials.sidebar')

    {{-- Main Content Column --}}
    <div class="app-main">
        {{-- Staff Banner Alert --}}
        <div class="role-banner">
            You are viewing as Staff. Only your schedule, bookings and clients are shown.
        </div>

        {{-- Header Partial --}}
        @include('layouts.admin.partials.header')

        {{-- Main Page Outlet --}}
        <main class="app-content" id="mainContent">
            @yield('content')
        </main>

        {{-- App Footer --}}
        <footer class="app-footer">
            Merinaz Admin · 1768 Columbia Rd NW ·
        </footer>
    </div>
</div>

{{-- Persistent UI Elements: Unsaved bar, Modals, Toasts --}}
<div class="unsaved" id="unsavedBar">
    <span>You have unsaved changes.</span>
    <span class="d-flex gap-2">
            <button class="btn btn-outline btn-sm" style="color:#fff;border-color:#fff;" id="unsavedDiscard">Discard</button>
            <button class="btn btn-accent btn-sm" id="unsavedSave">Save changes</button>
        </span>
</div>

<div class="modal-back" id="modalBack">
    <div class="mz-modal" id="mzModal" role="dialog" aria-modal="true">
        <div class="modal-head" id="mTitle">Confirm</div>
        <div class="modal-body">
            <p class="small" id="mBody">Are you sure?</p>
            <div id="mPreview" style="display:none;"></div>
        </div>
        <div class="modal-foot">
            <button class="btn btn-outline btn-sm" id="mCancel">Keep as is</button>
            <button class="btn btn-accent btn-sm" id="mOk">Confirm</button>
        </div>
    </div>
</div>

<div class="toast-box" id="toastBox" aria-live="polite"></div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/admin.js') }}"></script>

@stack('scripts')
</body>
</html>
