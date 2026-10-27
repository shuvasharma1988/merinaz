@extends('layouts.admin.admin')

@section('title', 'Dashboard')

@section('content')
    <section class="mz-view on" id="view-today" aria-label="Today schedule">
        <div class="view-head">
            <div>
                <h1>Today</h1>
                <p class="muted small mb-0">{{ $todaySummary ?? 'Monday 5 Oct 2026 · 18 visits · 4 staff on · 3 need confirmation' }}</p>
            </div>
            <div class="view-actions">
                <a href="{{ Route::has('admin.calendar') ? route('admin.calendar') : '#' }}" class="btn btn-outline btn-sm" data-view="calendar">
                    View calendar
                </a>
                <a href="{{ Route::has('admin.bookings.create') ? route('admin.bookings.create') : '#' }}" class="btn btn-accent btn-sm" data-view="booking-new">
                    New booking
                </a>
            </div>
        </div>

        <div class="today-grid">
            {{-- Left Schedule Grid --}}
            <div>
                <div class="tl-cols">
                    {{-- Column 1: Lila Karki --}}
                    <div class="tl-person" data-staff="Lila Karki">
                        <div class="tl-person-head">
                            <strong>Lila Karki</strong>
                            <span class="muted">6 visits · threading lead</span>
                        </div>
                        <div class="tl-list">
                            <div class="appt confirmed">
                                <div class="appt-time">9:00a - 9:30a</div>
                                <div class="appt-client">Sofia Mendez</div>
                                <div class="appt-svc">Eyebrow threading, upper lip threading</div>
                                <span class="st st-confirmed"><i></i><span>confirmed</span></span>
                            </div>
                            <div class="appt confirmed">
                                <div class="appt-time">9:35a - 10:05a</div>
                                <div class="appt-client">Mei Lin</div>
                                <div class="appt-svc">Full face threading · 30 min</div>
                                <span class="st st-confirmed"><i></i><span>confirmed</span></span>
                            </div>
                            <div class="appt pending">
                                <div class="appt-time">10:20a - 10:35a</div>
                                <div class="appt-client">Ana Petrova</div>
                                <div class="appt-svc">Upper lip threading · $14</div>
                                <span class="st st-pending"><i></i><span>pending</span></span>
                            </div>
                            <div class="free-row">Break 12:00p - 12:30p</div>
                            <div class="appt confirmed">
                                <div class="appt-time">1:30p - 1:45p</div>
                                <div class="appt-client">Linh Nguyen</div>
                                <div class="appt-svc">Eyebrow threading</div>
                                <span class="st st-completed"><i></i><span>completed</span></span>
                            </div>
                            <div class="appt pending">
                                <div class="appt-time">3:40p - 3:55p</div>
                                <div class="appt-client">Grace Adeyemi</div>
                                <div class="appt-svc">Eyebrow threading</div>
                                <span class="st st-pending"><i></i><span>pending</span></span>
                            </div>
                        </div>
                    </div>

                    {{-- Column 2: Priya Sharma --}}
                    <div class="tl-person" data-staff="Priya Sharma">
                        <div class="tl-person-head">
                            <strong>Priya Sharma</strong>
                            <span class="muted">5 visits · waxing</span>
                        </div>
                        <div class="tl-list">
                            <div class="appt confirmed">
                                <div class="appt-time">9:00a - 9:45a</div>
                                <div class="appt-client">Rachel Kim</div>
                                <div class="appt-svc">Brazilian wax · 45 min · $68</div>
                                <span class="st st-confirmed"><i></i><span>confirmed</span></span>
                            </div>
                            <div class="appt confirmed">
                                <div class="appt-time">10:00a - 11:15a</div>
                                <div class="appt-client">Maya Thompson</div>
                                <div class="appt-svc">Full leg wax · 75 min · $92</div>
                                <span class="link-badge">Visit 1042 · part 1 of 2</span><br>
                                <span class="st st-confirmed"><i></i><span>confirmed</span></span>
                            </div>
                            <div class="appt confirmed">
                                <div class="appt-time">11:10a - 11:55a</div>
                                <div class="appt-client">Elena Rodriguez</div>
                                <div class="appt-svc">Half leg wax · 45 min</div>
                                <span class="st st-confirmed"><i></i><span>confirmed</span></span>
                            </div>
                            <div class="free-row">Lunch 12:30p - 1:15p</div>
                            <div class="appt pending">
                                <div class="appt-time">2:00p - 2:45p</div>
                                <div class="appt-client">Nadia Ali</div>
                                <div class="appt-svc">Brazilian wax, underarm wax</div>
                                <span class="st st-pending"><i></i><span>pending</span></span>
                            </div>
                        </div>
                    </div>

                    {{-- Column 3: Daniela Ruiz --}}
                    <div class="tl-person" data-staff="Daniela Ruiz">
                        <div class="tl-person-head">
                            <strong>Daniela Ruiz</strong>
                            <span class="muted">4 visits · skin</span>
                        </div>
                        <div class="tl-list">
                            <div class="appt confirmed">
                                <div class="appt-time">10:30a - 11:30a</div>
                                <div class="appt-client">Maya Thompson</div>
                                <div class="appt-svc">Custom facial · 60 min · $78</div>
                                <span class="link-badge">Visit 1042 · part 2 of 2</span><br>
                                <span class="st st-confirmed"><i></i><span>confirmed</span></span>
                            </div>
                            <div class="appt pending">
                                <div class="appt-time">11:30a - 12:00p</div>
                                <div class="appt-client">Julia Novak</div>
                                <div class="appt-svc">Express facial · 30 min</div>
                                <span class="st st-pending"><i></i><span>pending</span></span>
                            </div>
                            <div class="free-row">Break 1:00p - 1:30p</div>
                            <div class="appt cancelled">
                                <div class="appt-time">3:15p - 4:15p</div>
                                <div class="appt-client">Olivia Bennett</div>
                                <div class="appt-svc">Deep cleanse facial</div>
                                <span class="st st-noshow"><i></i><span>no-show</span></span>
                            </div>
                        </div>
                    </div>

                    {{-- Column 4: Amara Okafor --}}
                    <div class="tl-person" data-staff="Amara Okafor">
                        <div class="tl-person-head">
                            <strong>Amara Okafor</strong>
                            <span class="muted">3 visits · waxing</span>
                        </div>
                        <div class="tl-list">
                            <div class="appt pending">
                                <div class="appt-time">9:45a - 10:05a</div>
                                <div class="appt-client">Fatima Hassan</div>
                                <div class="appt-svc">Underarm wax · 20 min</div>
                                <span class="st st-pending"><i></i><span>pending</span></span>
                            </div>
                            <div class="appt confirmed">
                                <div class="appt-time">12:15p - 1:00p</div>
                                <div class="appt-client">Keisha Williams</div>
                                <div class="appt-svc">Full arm wax · 45 min</div>
                                <span class="st st-confirmed"><i></i><span>confirmed</span></span>
                            </div>
                            <div class="dayoff">Off after 2:00p · approved time off</div>
                        </div>
                    </div>
                </div>
                <p class="tiny muted mt-2 mb-0">Visit 1042 spans two columns. Priya does waxing while Daniela does skin. Times overlap by design.</p>
            </div>

            {{-- Right Sidebar: Needs Attention --}}
            <aside class="tone-2" aria-label="Needs attention">
                <h2>Needs attention <span class="muted" style="font-weight:500;">· 7</span></h2>

                <h3>Unconfirmed bookings</h3>
                <div class="att-item">
                    <div class="att-title">Ana Petrova · 10:20a</div>
                    <div class="att-meta">Upper lip threading with Lila · $14</div>
                    <div class="d-flex gap-2 mt-2">
                        <button type="button" class="btn btn-ink btn-sm" data-toast="Booking confirmed. Reminder set for tomorrow.">Confirm booking</button>
                        <button type="button" class="btn btn-outline btn-sm" data-toast="Reminder sent to Ana Petrova.">Resend reminder</button>
                    </div>
                </div>

                <div class="att-item">
                    <div class="att-title">Fatima Hassan · 9:45a</div>
                    <div class="att-meta">Underarm wax with Amara · pending 2 days</div>
                    <div class="d-flex gap-2 mt-2">
                        <button type="button" class="btn btn-ink btn-sm" data-toast="Booking confirmed. Reminder set for tomorrow.">Confirm booking</button>
                        <button type="button" class="btn btn-outline btn-sm" data-view="booking-detail">Review visit</button>
                    </div>
                </div>

                <div class="att-item requires-owner-manager">
                    <div class="att-title">Nadia Ali · 2:00p</div>
                    <div class="att-meta">Brazilian plus underarm with Priya · $92</div>
                    <div class="d-flex gap-2 mt-2">
                        <button type="button" class="btn btn-ink btn-sm" data-toast="Booking confirmed. Reminder set for tomorrow.">Confirm booking</button>
                        <button type="button" class="btn btn-outline btn-sm" data-toast="Visit moved to Thursday 10:30a.">Move to Thursday</button>
                    </div>
                </div>

                <h3 class="mt-3">Unread messages</h3>
                <div class="att-item">
                    <div class="att-title"><span class="dot-unread me-1"></span>Sarah Goldstein</div>
                    <div class="att-meta">Do you take walk-ins on Saturdays · Oct 4</div>
                    <button type="button" class="btn btn-outline btn-sm mt-2 requires-owner-manager" data-view="messages">Open message</button>
                    <button type="button" class="btn btn-outline btn-sm mt-2 d-none" data-toast="Saved. Your lead will reply.">Mark for lead</button>
                </div>

                <div class="att-item requires-owner-manager">
                    <div class="att-title"><span class="dot-unread me-1"></span>Nadia Ali</div>
                    <div class="att-meta">Reaction after wax, what to do · Oct 4</div>
                    <button type="button" class="btn btn-outline btn-sm mt-2" data-view="messages">Open message</button>
                </div>

                <h3 class="mt-3 requires-owner-manager">Reviews awaiting approval</h3>
                <div class="att-item requires-owner-manager">
                    <div class="att-title">Mei Lin · 3 of 5</div>
                    <div class="att-meta">Yelp · Sep 18 · threading</div>
                    <button type="button" class="btn btn-outline btn-sm mt-2" data-view="reviews">Moderate review</button>
                </div>

                <div class="att-item requires-owner-manager">
                    <div class="att-title">Julia Novak · 4 of 5</div>
                    <div class="att-meta">Google · Sep 12 · facial</div>
                    <button type="button" class="btn btn-outline btn-sm mt-2" data-view="reviews">Moderate review</button>
                </div>
            </aside>
        </div>
    </section>
@endsection
