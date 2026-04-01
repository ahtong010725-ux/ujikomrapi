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
                        <span style="
                            position:absolute;
                            top:-5px;
                            right:-10px;
                            background:red;
                            color:white;
                            font-size:11px;
                            padding:3px 6px;
                            border-radius:10px;
                        ">
                            {{ $totalUnread }}
                        </span>
                    @endif
                </a>
            </li>

            {{-- Profile as regular nav link (visible on mobile, hidden on desktop via CSS) --}}
            <li class="mobile-nav-item">
                <a href="/profile">Profile</a>
            </li>

            {{-- Desktop-only avatar dropdown --}}
            <li class="user-menu">
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

            {{-- Mobile-only logout --}}
            <li class="mobile-logout-item">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">🚪 Logout</button>
                </form>
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
