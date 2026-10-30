@extends('layouts.error')

@section('title', 'Page Not Found')
@section('code_label', 'Error 404')
@section('heading', 'This page was not found.')
@section('message', 'It may have been moved or the link is old. Check the menu and try again.')

@section('actions')
    <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : url('/') }}" class="btn btn-accent btn-sm">
        Go to today
    </a>
@endsection


@extends('layouts.error')

@section('title', 'Session Expired')
@section('code_label', 'Error 419')
@section('heading', 'Your session expired.')
@section('message', 'Log in again to continue. Unsaved work was kept on this device.')
