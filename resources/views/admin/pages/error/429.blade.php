@extends('layouts.error')

@section('title', 'Too Many Requests')
@section('code_label', 'Error 429')
@section('heading', 'Too many requests.')
@section('message', 'Wait a moment, then try again. The desk line stays open at 202-555-0100.')

@section('actions')
    <button type="button" onclick="window.location.reload();" class="btn btn-accent btn-sm">
        Try again
    </button>
@endsection
