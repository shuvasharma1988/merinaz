{{-- Sticky header shared across every public page --}}
<header class="sticky-header">
    <nav class="navbar navbar-expand-lg container py-2" aria-label="Primary navigation">
        <a class="navbar-brand" href="{{ route('home') }}" aria-label="Merinaz home">
            {{-- Updated Logo Wrapper --}}
            <div style="background-color: var(--ink); padding: 4px 8px; border-radius: 12px; display: flex; align-items: center; justify-content: center; height: 44px; margin-right: 0.5rem;">
                <img src="{{ asset('images/logosmall.png') }}" alt="Merinaz Logo" style="max-height: 24px; width: auto; object-fit: contain;">
            </div>
            <span>
                <span class="brand-name">Merinaz</span><br>
                <span class="brand-sub">Wax • Thread • Sugar • Skin</span>
            </span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <i class="bi bi-list fs-2"></i>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                @foreach ([
                    ['label' => 'Home', 'url' => route('home'), 'active' => 'home'],
                    ['label' => 'Services', 'url' => route('services.index'), 'active' => 'services.*'],
                    ['label' => 'Gallery', 'url' => route('gallery.index'), 'active' => 'gallery.*'],
                    ['label' => 'Journal', 'url' => route('blog.index'), 'active' => 'blog.*'],
                    ['label' => 'FAQ', 'url' => route('faq.index'), 'active' => 'faq.*'],
                    ['label' => 'About', 'url' => route('about'), 'active' => 'about'],
                    ['label' => 'Contact', 'url' => route('contact'), 'active' => 'contact'],
                ] as $item)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs($item['active']) ? 'active' : '' }}" href="{{ $item['url'] }}">
                            {{ $item['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('login') }}" class="btn btn-light-mer btn-merinaz btn-sm-mer" aria-label="My appointments and login">
                    <i class="bi bi-person-circle"></i><span class="d-none d-xl-inline">My Visits</span>
                </a>
                <a href="{{ route('booking.index') }}" class="btn btn-primary-mer btn-merinaz btn-sm-mer">
                    <i class="bi bi-calendar-plus"></i> Book Now
                </a>
            </div>
        </div>
    </nav>
</header>
