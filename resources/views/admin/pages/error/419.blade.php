@extends('layouts.error')

@section('title', 'Session Expired')
@section('code_label', 'Error 419')
@section('heading', 'Your session expired.')
@section('message', 'Log in again to continue. Unsaved work was kept on this device.')

@section('actions')
    <a href="{{ Route::has('login') ? route('login') : url('/login') }}" class="btn btn-accent btn-sm">
        Log in again
    </a>
@endsection
