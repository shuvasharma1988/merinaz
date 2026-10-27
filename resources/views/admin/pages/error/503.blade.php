@extends('layouts.error')

@section('title', 'Service Unavailable')
@section('code_label', 'Error 503')
@section('heading', 'The site is down for maintenance.')
@section('message', 'Bookings are paused for about 20 minutes. Existing visits are safe.')

@section('actions')
    <button type="button" onclick="window.location.reload();" class="btn btn-accent btn-sm">
        Try again
    </button>
@endsection
