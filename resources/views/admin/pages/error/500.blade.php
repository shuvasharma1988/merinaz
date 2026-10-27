@extends('layouts.error')

@section('title', 'Internal Server Error')
@section('code_label', 'Error 500')
@section('heading', 'Something went wrong on our side.')
@section('message', 'We logged the error with time and page. Try again in a minute.')

@section('actions')
    <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : url('/') }}" class="btn btn-accent btn-sm">
        Go to today
    </a>
@endsection
