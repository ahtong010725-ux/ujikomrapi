@include('components.toast')
<nav class="navbar" id="mainNavbar">

    <div class="logo">
        <a href="/">I FOUND</a>
    </div>

    <!-- Hamburger Button (mobile) -->
    <button class="nav-hamburger" id="navHamburger" aria-label="Menu" onclick="toggleMobileNav()">
        <span></span><span></span><span></span>
    </button>

    <!-- Slide-out drawer (mobile) + normal links (desktop) -->
    <ul class="nav-links" id="navLinks">
        <li>
            <button id="theme-toggle" style="background: none; border: none; font-size: 20px; cursor: pointer; padding: 5px;" aria-label="Toggle Dark Mode">🌙</button>
        </li>
        <li><a href="/">Home</a></li>
        <li><a href="/lost">Lost</a></li>
        <li><a href="/found">Found</a></li>
        <li><a href="/leaderboard">🏆 Leaderboard</a></li>

        @guest
            <li><a href="{{ route('login') }}">Login</a></li>
        @endguest

        @auth
            <li style="position:relative;">
                <a href="/inbox">
                    Inbox
                    @php
                        $totalUnread = \App\Models\Message::where('receiver_id', auth()->id())
                            ->where('is_read', false)
                            ->count();
                    @endphp
                    @if($totalUnread > 0)
                        <span class="inbox-badge">{{ $totalUnread }}</span>
                    @endif
                </a>
            </li>

            {{-- Mobile-only: Profile as regular nav link --}}
            <li class="mobile-nav-item">
                <a href="/profile">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Profile
                </a>
            </li>

            {{-- Mobile-only: Admin Dashboard link --}}
            @if(auth()->user()->role === 'admin')
            <li class="mobile-nav-item">
                <a href="{{ route('admin.dashboard') }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Admin Dashboard
                </a>
            </li>
            @endif

            {{-- Mobile-only: Logout --}}
            <li class="mobile-nav-item mobile-logout-item">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        Logout
                    </button>
                </form>
            </li>

            {{-- Desktop-only: avatar dropdown --}}
            <li class="user-menu desktop-only-menu">
                <div class="user-avatar" onclick="toggleDropdown()">
                    <img src="{{ asset('storage/' . auth()->user()->photo) }}">
                </div>

                <div class="dropdown-menu" id="dropdownMenu">
                    <div class="dropdown-header">
                        {{ auth()->user()->name }}
                    </div>

                    @if(auth()->user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
                    @endif
                    <a href="/profile">Profile</a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">Logout</button>
                    </form>
                </div>
            </li>
        @endauth
    </ul>

    <!-- Mobile overlay -->
    <div class="nav-overlay" id="navOverlay" onclick="closeMobileNav()"></div>
</nav>

<script>
function toggleMobileNav() {
    const links = document.getElementById('navLinks');
    const overlay = document.getElementById('navOverlay');
    const hamburger = document.getElementById('navHamburger');
    const isOpen = links.classList.contains('mobile-open');
    if (isOpen) {
        links.classList.remove('mobile-open');
        overlay.classList.remove('active');
        hamburger.classList.remove('active');
    } else {
        links.classList.add('mobile-open');
        overlay.classList.add('active');
        hamburger.classList.add('active');
    }
}
function closeMobileNav() {
    document.getElementById('navLinks').classList.remove('mobile-open');
    document.getElementById('navOverlay').classList.remove('active');
    document.getElementById('navHamburger').classList.remove('active');
}
</script>
