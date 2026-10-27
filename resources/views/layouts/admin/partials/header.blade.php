<header class="app-header">
    {{-- Mobile Sidebar Toggle --}}
    <button class="btn btn-quiet btn-sm d-lg-none" id="menuBtn" aria-label="Open menu">
        <i class="bi bi-list"></i>
    </button>

    {{-- Dynamic View / Page Title --}}
    <span class="page-title" id="pageTitle">@yield('title', 'Today')</span>
    <span class="muted small d-none d-md-inline" id="todayDate">{{ now()->format('D j M Y') }}</span>

    <div class="flex-grow-1"></div>

    {{-- Global Search Bar --}}
    <input class="form-control form-control-sm top-search"
           id="topSearch"
           type="search"
           placeholder="Search clients, bookings"
           aria-label="Search">

    {{-- Primary Action CTA --}}
    <a href="{{route('admin.bookings.create')}}" class="btn btn-accent btn-sm">
        New booking
    </a>

    {{-- User Dropdown Menu --}}
    <div class="dropdown">
        <button class="avatar" data-bs-toggle="dropdown" aria-label="Account menu" id="avatarBtn">
            {{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 2)) : 'MH' }}
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <a class="dropdown-item" href="#">Account</a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                    @csrf
                    <button type="submit" class="dropdown-item" id="ddLogout">Log out</button>
                </form>
            </li>
        </ul>
    </div>
</header>
