{{-- Shared footer --}}
<footer class="footer" aria-label="Site footer">
    <div class="container py-4">
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div style="background-color: #211B17; border: 1px solid rgba(255,255,255,0.1); padding: 4px 8px; border-radius: 12px; display: flex; align-items: center; justify-content: center; height: 44px;">
                        <img src="{{ asset('images/logo-merinaz.png') }}" alt="Merinaz Logo" style="max-height: 24px; width: auto; object-fit: contain;">
                    </div>
                    <div class="serif fw-bold fs-4 text-white">Merinaz</div>
                </div>

                <p class="small mb-3" style="opacity:.8">Calm studios for precise hair removal &amp; skin. Licensed &amp; hygienic since 2017.</p>

                <div class="small" style="opacity:.7">
                    <p class="mb-1"><strong>Columbia Rd:</strong> 1768 Columbia Rd NW, DC 20009 · (202) 469-2869</p>
                    <p class="mb-1"><strong>Wisconsin Ave:</strong> 3500 Wisconsin Ave NW, DC 20016 · (202) 804-4185</p>
                    <p class="mb-0"><i class="bi bi-envelope"></i> {{ \App\Models\SiteSetting::get('email', 'merinazofficial@gmail.com') }}</p>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <h6 class="text-white">Quick Links</h6>
                <ul class="list-unstyled small d-grid gap-2" style="opacity:.8">
                    <li><a href="{{ route('services.index') }}">Services &amp; Prices</a></li>
                    <li><a href="{{ route('booking.index') }}">Book Now</a></li>
                    <li><a href="{{ route('about') }}">About &amp; Team</a></li>
                    <li><a href="{{ route('faq.index') }}">FAQ &amp; Contact</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-4">
                <h6 class="text-white">Hours</h6>
                <div class="small" style="opacity:.8">
                    <div class="d-flex justify-content-between mb-1"><span>Mon – Sat</span><span>10:00 AM – 8:00 PM</span></div>
                    <div class="d-flex justify-content-between mb-3"><span>Sunday</span><span>10:00 AM – 7:00 PM</span></div>
                    <p class="mb-0 text-muted" style="font-size: 0.8rem;">Same-day slots released at 9am daily.</p>
                </div>
            </div>
        </div>

        <hr style="border-color:rgba(255,255,255,.15)" class="my-4">

        <div class="d-flex flex-wrap justify-content-between gap-2 small" style="opacity:.7">
            <span>© {{ date('Y') }} Merinaz Studio LLC · All rights reserved.</span>
            <span>
                <a href="{{ route('privacy') }}">Privacy &amp; Terms</a>
            </span>
        </div>
    </div>
</footer>
