@extends('layouts.error')

@section('title', 'Access Denied')
@section('code_label', 'Error 403')
@section('heading', 'You do not have access to this page.')
@section('message', 'Your current role does not have permission to view this section. Ask an owner or manager if you need access.')

@section('actions')
    <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : url('/') }}" class="btn btn-accent btn-sm">
        Go to today
    </a>
@endsection
