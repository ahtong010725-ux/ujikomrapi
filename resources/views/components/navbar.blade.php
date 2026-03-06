<nav class="navbar">

    <div class="logo">
        <a href="/">I FOUND</a>
    </div>

    <ul class="nav-links">
        <li>
            <button id="theme-toggle" style="background: none; border: none; font-size: 20px; cursor: pointer; padding: 5px;" aria-label="Toggle Dark Mode">🌙</button>
        </li>
        <li><a href="/">Home</a></li>
        <li><a href="/lost">Lost</a></li>
        <li><a href="/found">Found</a></li>

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
                    <a href="{{ route('bookmarks.index') }}">Bookmarks</a>
                    <a href="/profile">Profile</a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">Logout</button>
                    </form>
                </div>
            </li>
        @endauth
    </ul>
</nav>

<script src="{{ asset('js/theme.js') }}"></script>
