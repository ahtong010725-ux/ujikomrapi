@extends('layouts.auth')

@section('content')

<div class="auth-container">
    <div class="auth-card">

        <div class="auth-logo">
            <a href="/">I FOUND</a>
        </div>

        <h2 class="title">Welcome Back 👋</h2>
        <p class="subtitle">Sign in to continue</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- NISN --}}
            <div class="input-group">
                <label>NISN</label>
                <input
                    type="text"
                    name="nisn"
                    placeholder="Enter your NISN"
                    value="{{ old('nisn') }}"
                    required
                >
                @error('nisn')
                    <small class="error-text">{{ $message }}</small>
                @enderror
            </div>

            {{-- Password --}}
            <div class="input-group password-group">
                <label>Password</label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    placeholder="Enter your password"
                    required
                >
                <span onclick="togglePassword()" class="eye-icon">👁</span>

                @error('password')
                    <small class="error-text">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="btn-auth">
                Sign In
            </button>

            <div class="switch-text">
                Don't have an account?
                <a href="{{ route('register') }}">Sign Up</a>
            </div>

        </form>

    </div>
</div>
<script src="{{ asset('js/theme.js') }}"></script>
<script>
    function togglePassword() {
        const password = document.getElementById("password");
        password.type = password.type === "password" ? "text" : "password";
    }
</script>

@endsection
