@extends('layouts.admin.admin')

@section('title', 'Calendar')

@section('content')
    <section class="mz-view on" id="view-calendar" aria-label="Calendar">
        {{-- Header with view toggles and actions --}}
        <div class="view-head">
            <div>
                <h1>Calendar</h1>
                <p class="muted small mb-0">Day and week. Hatched blocks are time off or closed hours.</p>
            </div>
            <div class="view-actions">
                {{-- Day vs Week View Switcher --}}
                <div class="cal-toggle" role="tablist">
                    <button type="button" class="on" id="calDayBtn">Day</button>
                    <button type="button" id="calWeekBtn">Week</button>
                </div>

                <button type="button" class="btn btn-outline btn-sm">
                    {{ $currentDateFormatted ?? 'Mon 5 Oct 2026' }}
                </button>

                <a href="{{ Route::has('admin.bookings.create') ? route('admin.bookings.create') : '#' }}" class="btn btn-accent btn-sm" data-view="booking-new">
                    New booking
                </a>
            </div>
        </div>

        {{-- ================= DAY VIEW ================= --}}
        <div id="calDay">
            <div class="tl-cols">
                {{-- Staff Column: Lila Karki --}}
                <div class="tl-person" data-staff="Lila Karki">
                    <div class="tl-person-head">
                        <strong>Lila Karki</strong>
                        <span class="muted">9:00a to 5:30p</span>
                    </div>
                    <div class="tl-list">
                        <div class="appt confirmed">
                            <div class="appt-time">9:00a</div>
                            <div class="appt-client">Sofia Mendez</div>
                            <div class="appt-svc">Eyebrow, upper lip · confirmed</div>
                        </div>
                        <div class="appt confirmed">
                            <div class="appt-time">9:35a</div>
                            <div class="appt-client">Mei Lin</div>
                            <div class="appt-svc">Full face · confirmed</div>
                        </div>
                        <div class="appt pending">
                            <div class="appt-time">10:20a</div>
                            <div class="appt-client">Ana Petrova</div>
                            <div class="appt-svc">Upper lip · pending</div>
                        </div>
                        {{-- Hatched Break Block --}}
                        <div class="free-row">Break 12:00p - 12:30p</div>
                        <div class="appt completed">
                            <div class="appt-time">1:30p</div>
                            <div class="appt-client">Linh Nguyen</div>
                            <div class="appt-svc">Eyebrow · completed</div>
                        </div>
                    </div>
                </div>

                {{-- Staff Column: Priya Sharma --}}
                <div class="tl-person" data-staff="Priya Sharma">
                    <div class="tl-person-head">
                        <strong>Priya Sharma</strong>
                        <span class="muted">9:00a to 6:00p</span>
                    </div>
                    <div class="tl-list">
                        <div class="appt confirmed">
                            <div class="appt-time">9:00a · 45 min</div>
                            <div class="appt-client">Rachel Kim</div>
                            <div class="appt-svc">Brazilian wax · confirmed</div>
                        </div>
                        {{-- Multi-service Linked Visit (Part 1) --}}
                        <div class="appt confirmed" style="border-color:var(--mz-ink);">
                            <div class="appt-time">10:00a · 75 min</div>
                            <div class="appt-client">Maya Thompson</div>
                            <div class="appt-svc">Full leg wax · $92</div>
                            <span class="link-badge">Visit 1042 · part 1</span>
                        </div>
                        <div class="appt confirmed">
                            <div class="appt-time">11:10a</div>
                            <div class="appt-client">Elena Rodriguez</div>
                            <div class="appt-svc">Half leg wax · confirmed</div>
                        </div>
                    </div>
                </div>

                {{-- Staff Column: Daniela Ruiz --}}
                <div class="tl-person" data-staff="Daniela Ruiz">
                    <div class="tl-person-head">
                        <strong>Daniela Ruiz</strong>
                        <span class="muted">10:00a to 6:30p</span>
                    </div>
                    <div class="tl-list">
                        {{-- Multi-service Linked Visit (Part 2) --}}
                        <div class="appt confirmed" style="border-color:var(--mz-ink);">
                            <div class="appt-time">10:30a · 60 min</div>
                            <div class="appt-client">Maya Thompson</div>
                            <div class="appt-svc">Custom facial · $78</div>
                            <span class="link-badge">Visit 1042 · part 2</span>
                        </div>
                        <div class="appt pending">
                            <div class="appt-time">11:30a</div>
                            <div class="appt-client">Julia Novak</div>
                            <div class="appt-svc">Express facial · pending</div>
                        </div>
                        <div class="free-row">Break 1:00p - 1:30p</div>
                    </div>
                </div>

                {{-- Staff Column: Amara Okafor --}}
                <div class="tl-person" data-staff="Amara Okafor">
                    <div class="tl-person-head">
                        <strong>Amara Okafor</strong>
                        <span class="muted">Day off after 2p</span>
                    </div>
                    <div class="tl-list">
                        <div class="appt pending">
                            <div class="appt-time">9:45a · 20 min</div>
                            <div class="appt-client">Fatima Hassan</div>
                            <div class="appt-svc">Underarm wax · pending</div>
                        </div>
                        <div class="appt confirmed">
                            <div class="appt-time">12:15p · 45 min</div>
                            <div class="appt-client">Keisha Williams</div>
                            <div class="appt-svc">Full arm wax · confirmed</div>
                        </div>
                        {{-- Hatched Day-Off / Time-Off Block --}}
                        <div class="dayoff">
                            Approved time off<br>2:00p to close
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= WEEK VIEW ================= --}}
        <div id="calWeek" style="display:none;">
            <div class="week-grid">
                {{-- Monday --}}
                <div class="week-col">
                    <div class="week-head">Mon 5 Oct · 18 visits</div>
                    <div class="week-body">
                        <div class="mini"><b>9:00a Sofia M.</b>Eyebrow · Lila · confirmed</div>
                        <div class="mini"><b>10:00a Maya T.</b>Full leg + facial · 2 staff</div>
                        <div class="mini"><b>2:00p Nadia A.</b>Brazilian · pending</div>
                    </div>
                </div>

                {{-- Tuesday --}}
                <div class="week-col">
                    <div class="week-head">Tue 6 Oct · 14 visits</div>
                    <div class="week-body">
                        <div class="mini"><b>9:20a Hannah L.</b>Brow threading · confirmed</div>
                        <div class="mini"><b>11:00a Priya N.</b>Bikini wax · confirmed</div>
                    </div>
                </div>

                {{-- Wednesday --}}
                <div class="week-col">
                    <div class="week-head">Wed 7 Oct · 16 visits</div>
                    <div class="week-body">
                        <div class="mini"><b>10:15a Emily C.</b>Underarm · pending</div>
                        <div class="mini"><b>1:00p Sara B.</b>Facial · confirmed</div>
                    </div>
                </div>

                {{-- Thursday --}}
                <div class="week-col">
                    <div class="week-head">Thu 8 Oct · 19 visits</div>
                    <div class="week-body">
                        <div class="mini"><b>9:00a Aisha R.</b>Full face · confirmed</div>
                        <div class="mini"><b>12:30p Grace A.</b>Brow cleanup · pending</div>
                    </div>
                </div>

                {{-- Friday --}}
                <div class="week-col">
                    <div class="week-head">Fri 9 Oct · 21 visits</div>
                    <div class="week-body">
                        <div class="mini"><b>9:45a Mei L.</b>Lip and chin · confirmed</div>
                        <div class="mini"><b>3:00p Olivia B.</b>Rebook facial · pending</div>
                    </div>
                </div>

                {{-- Saturday --}}
                <div class="week-col">
                    <div class="week-head">Sat 10 Oct · 23 visits</div>
                    <div class="week-body">
                        <div class="mini"><b>9:00a Walk-in block</b>Held for front desk · Lila</div>
                        <div class="mini"><b>10:30a Julia N.</b>Express facial · confirmed</div>
                    </div>
                </div>

                {{-- Sunday & Blackout Day --}}
                <div class="week-col blackout-col">
                    <div class="week-head">Sun 11 Oct · closed</div>
                    <div class="week-body">
                        <div class="mini"><b>Closed</b>No bookings. Phones off.</div>
                        <div class="mini"><b>Mon 12 Oct · blackout</b>Deep clean. No bookings.</div>
                    </div>
                </div>
            </div>
            <p class="tiny muted mt-2">Sunday is closed. Monday 12 Oct is a blackout date for deep cleaning.</p>
        </div>
    </section>
@endsection
```
