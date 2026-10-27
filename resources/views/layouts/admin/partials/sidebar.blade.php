<aside class="app-sidebar" aria-label="Primary">
    {{-- Brand Wordmark --}}
    <div class="brand">
        <span class="wordmark">Merinaz</span>
        <span class="brand-sub">Admin · {{ auth()->user()->roles->first()->name ?? 'front desk' }}</span>
    </div>

    {{-- Navigation Scroll Area --}}
    <nav class="side-scroll" aria-label="Admin sections">
        <div class="nav-label">Schedule</div>
        <a class="side-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
            <span>Today</span>
        </a>
        <a class="side-link" href="#" data-view="calendar">
            <span>Calendar</span>
        </a>
        <a class="side-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}" href="{{ route('admin.bookings.index') }}">
            <span>Bookings</span>
        </a>

        <div class="nav-label">People</div>
        <a class="side-link {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}" href="{{ route('admin.clients.index') }}">
            <span>Clients</span>
        </a>

        @if(auth()->user()->hasPermission('staff.view') || auth()->user()->hasPermission('staff.manage'))
            <a class="side-link {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}" href="{{ route('admin.staff.index') }}">
                <span>Staff</span>
            </a>
        @endif

        @if(auth()->user()->hasPermission('services.view') || auth()->user()->hasPermission('services.manage'))
            <div class="nav-label">Catalogue</div>
            <a class="side-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}" href="{{ route('admin.services.index') }}">
                <span>Services</span>
            </a>
            <a class="side-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                <span>Categories</span>
            </a>
        @endif

        @if(
            auth()->user()->hasPermission('pages.view') ||
            auth()->user()->hasPermission('messages.view') ||
            auth()->user()->hasPermission('content.manage')
        )
            <div class="nav-label">Content</div>
            <a class="side-link" href="#" data-view="reviews">
                <span>Reviews</span>
            </a>

            @if(auth()->user()->hasPermission('posts.view') || auth()->user()->hasPermission('content.manage'))
                <a class="side-link {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}" href="{{ route('admin.posts.index') }}">
                    <span>Blog</span>
                </a>
            @endif

            @if(auth()->user()->hasPermission('gallery.view') || auth()->user()->hasPermission('content.manage'))
                <a class="side-link {{ request()->routeIs('admin.gallery.*') ? 'active' : '' }}" href="{{ route('admin.gallery.index') }}">
                    <span>Gallery</span>
                </a>
            @endif

            @if(auth()->user()->hasPermission('pages.view') || auth()->user()->hasPermission('content.manage'))
                <a class="side-link {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}" href="{{ route('admin.pages.index') }}">
                    <span>Pages</span>
                </a>
            @endif

            @if(auth()->user()->hasPermission('faqs.view') || auth()->user()->hasPermission('content.manage'))
                <a class="side-link {{ request()->routeIs('admin.faq.*') ? 'active' : '' }}" href="{{ route('admin.faq.index') }}">
                    <span>FAQ</span>
                </a>
            @endif

            @if(auth()->user()->hasPermission('messages.view') || auth()->user()->hasPermission('content.manage'))
                @php
                    $unreadMessagesCount = class_exists(\App\Models\ContactMessage::class)
                        ? \App\Models\ContactMessage::unread()->count()
                        : 0;
                @endphp
                <a class="side-link {{ request()->routeIs('admin.messages.*') ? 'active' : '' }}" href="{{ route('admin.messages.index') }}">
                    <span>Messages</span>
                    @if($unreadMessagesCount > 0)
                        <span class="cnt">{{ $unreadMessagesCount }}</span>
                    @endif
                </a>
            @endif
        @endif

        @if(
            auth()->user()->hasPermission('settings.view') ||
            auth()->user()->hasPermission('settings.manage') ||
            auth()->user()->hasPermission('roles.manage') ||
            auth()->user()->hasRole('owner')
        )
            <div class="nav-label">Settings</div>

            @if(auth()->user()->hasRole('owner') && \Illuminate\Support\Facades\Route::has('admin.locations.index'))
                <a class="side-link {{ request()->routeIs('admin.locations.*') ? 'active' : '' }}" href="{{ route('admin.locations.index') }}">
                    <span>Locations</span>
                </a>
            @endif

            @if(auth()->user()->hasPermission('settings.view') || auth()->user()->hasPermission('settings.manage'))
                <a class="side-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.edit') }}">
                    <span>Site settings</span>
                </a>
                <a class="side-link" href="#" data-view="email-notifications">
                    <span>Email notifications</span>
                </a>
            @endif
        @endif
    </nav>

    {{-- Footer Info --}}
    <div class="sidebar-foot">
        {{ \App\Models\SiteSetting::get('address', '1800 Adams Mill Rd NW') }}<br>
        Open today 9:00a to 7:00p<br>
        <a href="#" data-view="account" id="footAccount">Account</a> ·

        @auth
            <a href="#" onclick="event.preventDefault(); document.getElementById('sidebarLogoutForm').submit();" id="footLogout">Log out</a>
            <form id="sidebarLogoutForm" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        @else
            <a href="{{ route('admin.login') }}" id="footLogin">Log in</a>
        @endauth
    </div>
</aside>
