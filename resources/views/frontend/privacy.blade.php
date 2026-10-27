@extends('layouts.frontend.frontend')

@section('title', 'Privacy Policy | Merinaz')
@section('meta_description', 'How Merinaz collects, uses and protects your personal information.')
@section('canonical', route('privacy'))

@section('content')
    <section class="container section-pad">
        <nav class="breadcrumb-mer mb-3" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Home</a><span aria-hidden="true">/</span><span aria-current="page">Privacy Policy</span>
        </nav>

        <span class="eyebrow">The fine print</span>
        <h1 class="display-5 fw-bold mt-2">Privacy policy</h1>

        <div class="article-body mt-4">
            @if ($page && $page->content)
                {!! $page->content !!}
            @else
                <p class="lead-soft">We collect the minimum we need to run a salon: your name, contact details, appointment history and any notes you give us. We never sell personal information.</p>

                <h2 class="h5 fw-bold mt-4">What we collect</h2>
                <ul>
                    <li>Name, email and phone number when you book or contact us.</li>
                    <li>Appointment details and service history so we can serve you consistently.</li>
                    <li>Notes you share with us, including allergies, skin sensitivities or preferences.</li>
                </ul>

                <h2 class="h5 fw-bold mt-4">How we use it</h2>
                <ul>
                    <li>To schedule, confirm and remind you about appointments.</li>
                    <li>To provide the treatment you booked and keep accurate aftercare notes.</li>
                    <li>To answer your questions and improve the studio.</li>
                </ul>

                <h2 class="h5 fw-bold mt-4">Who sees it</h2>
                <p>Only the Merinaz team members who need your information to serve you. We do not sell, rent or trade personal information. Payment details are processed by our card processor and are never stored on our servers.</p>

                <h2 class="h5 fw-bold mt-4">Cookies</h2>
                <p>We use cookies that keep you signed in and remember basic preferences. Analytics are aggregate and anonymous.</p>

                <h2 class="h5 fw-bold mt-4">Your choices</h2>
                <p>You can ask us to correct or delete your information at any time, or opt out of non-essential messages. Call us or email and we will handle it within a few days.</p>

                <h2 class="h5 fw-bold mt-4">Contact</h2>
                <p>Questions about this policy? Reach us at {{ \App\Models\SiteSetting::get('email', 'hello@merinaz.com') }} or visit the studio during business hours.</p>
            @endif

            <p class="small text-muted mt-4">Last updated: {{ \Illuminate\Support\Carbon::now()->format('F Y') }}</p>
        </div>
    </section>
@endsection
