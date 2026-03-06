@extends('layouts.auth')

@section('content')

<div class="auth-container">
    <div class="auth-card">

        <div class="auth-logo">
            <a href="/">I FOUND</a>
        </div>

        <h2 class="title">Welcome Back 👋</h2>
        <p class="subtitle">Sign in to continue</p>

        @if(session('success'))
            <div class="auth-alert auth-alert-success">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                {{ session('success') }}
            </div>
        @endif

        @if($errors->has('login'))
            <div class="auth-alert auth-alert-error">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                {{ $errors->first('login') }}
            </div>
        @endif

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
