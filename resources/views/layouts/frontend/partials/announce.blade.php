{{-- Announcement bar --}}
<div class="announce text-center py-2 px-3" role="note">
    <i class="bi bi-calendar-check me-1"></i>
    Same-day appointments available today in Adams Morgan —
    <a href="{{ route('booking.index') }}">Check live availability</a>
    &nbsp;·&nbsp;
    <i class="bi bi-telephone"></i> {{ \App\Models\SiteSetting::get('phone', '(202) 555-0148') }}
</div>
