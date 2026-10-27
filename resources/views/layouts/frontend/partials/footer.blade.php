{{-- Shared footer --}}
<footer class="footer" aria-label="Site footer">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="logo-mark" style="background:#F7EEDF;color:#211B17">M<span style="color:var(--accent)">.</span></span>
                    <div>
                        <div class="serif fw-bold fs-4 text-white">Merinaz</div>
                        <small style="letter-spacing:.18em;font-size:.65rem;opacity:.7">WAX • THREAD • SUGAR • SKIN</small>
                    </div>
                </div>

                <p class="small" style="opacity:.8">Downtown's calm studio for precise hair removal &amp; skin. Licensed, hygienic, run since 2017.</p>
                <p class="small mb-1"><i class="bi bi-geo-alt me-1"></i> {{ \App\Models\SiteSetting::get('address', '2436 18th St NW, Washington, DC 20009') }}</p>
                <p class="small mb-3">
                    <i class="bi bi-telephone me-1"></i>
                    {{ \App\Models\SiteSetting::get('phone', '(202) 555-0148') }}
                    · {{ \App\Models\SiteSetting::get('email', 'hello@merinazdc.com') }}
                </p>
            </div>

            <div class="col-6 col-lg-2">
                <h6>Explore</h6>
                <ul class="list-unstyled small d-grid gap-1">
                    <li><a href="{{ route('services.index') }}">Services &amp; prices</a></li>
                    <li><a href="{{ route('about') }}">About &amp; team</a></li>
                    <li><a href="{{ route('gallery.index') }}">Gallery</a></li>
                    <li><a href="{{ route('blog.index') }}">Journal</a></li>
                    <li><a href="{{ route('faq.index') }}">FAQ</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-2">
                <h6>Book</h6>
                <ul class="list-unstyled small d-grid gap-1">
                    <li><a href="{{ route('booking.index') }}">Book now</a></li>
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                    <li><a href="{{ route('privacy') }}">Privacy &amp; terms</a></li>
                </ul>
            </div>

            <div class="col-lg-4">
                <h6>Hours</h6>
                <div class="hours-row"><span>Mon – Fri</span><span>10:00 AM – 8:00 PM</span></div>
                <div class="hours-row"><span>Saturday</span><span>10:00 AM – 8:00 PM</span></div>
                <div class="hours-row"><span>Sunday</span><span>10:00 AM – 7:00 PM</span></div>
                <p class="small mt-3 mb-0" style="opacity:.7">Same-day slots are released at 9am daily. Call or text to reserve.</p>
            </div>
        </div>

        <hr style="border-color:rgba(255,255,255,.15)">

        <div class="d-flex flex-wrap justify-content-between gap-2 small" style="opacity:.7">
            <span>© {{ date('Y') }} Merinaz Studio LLC · Licensed DC Estheticians · All rights reserved.</span>
            <span>
                <a href="{{ route('privacy') }}">Privacy</a> ·
                <a href="{{ route('privacy') }}">Terms</a> ·
                <a href="{{ route('contact') }}">Accessibility: {{ \App\Models\SiteSetting::get('phone', '(202) 555-0148') }}</a>
            </span>
        </div>
    </div>
</footer>
