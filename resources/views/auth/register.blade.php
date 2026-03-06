@extends('layouts.auth')

@section('content')

<div class="auth-container">
    <div class="auth-card register-card">

        <div class="auth-logo">
            <a href="/">I FOUND</a>
        </div>

        <h2 class="title">Create Account ✨</h2>
        <p class="subtitle">Join I FOUND today</p>

        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
            @csrf

            {{-- NISN --}}
            <div class="input-group">
                <label>NISN</label>
                <input type="text" name="nisn" placeholder="Enter your NISN"
                    value="{{ old('nisn') }}" required>
                @error('nisn')
                    <small class="error-text">{{ $message }}</small>
                @enderror
            </div>

            {{-- Name --}}
            <div class="input-group">
                <label>Full Name</label>
                <input type="text" name="name" placeholder="Enter your full name"
                    value="{{ old('name') }}" required>
                @error('name')
                    <small class="error-text">{{ $message }}</small>
                @enderror
            </div>

            {{-- Kelas --}}
            <div class="input-group">
                <label>Class</label>
                <input type="text" name="kelas" placeholder="e.g. XII RPL 1"
                    value="{{ old('kelas') }}" required>
                @error('kelas')
                    <small class="error-text">{{ $message }}</small>
                @enderror
            </div>

            {{-- Phone --}}
            <div class="input-group">
                <label>Phone Number</label>
                <input type="text" name="phone" placeholder="e.g. 08123456789"
                    value="{{ old('phone') }}" required>
                @error('phone')
                    <small class="error-text">{{ $message }}</small>
                @enderror
            </div>

            {{-- Tanggal Lahir --}}
            <div class="input-group">
                <label>Date of Birth</label>
                <input type="date" name="tanggal_lahir"
                    value="{{ old('tanggal_lahir') }}" required>
                @error('tanggal_lahir')
                    <small class="error-text">{{ $message }}</small>
                @enderror
            </div>

            {{-- Jenis Kelamin --}}
            <div class="input-group">
                <label>Gender</label>
                <select name="jenis_kelamin" required>
                    <option value="">Select Gender</option>
                    <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                </select>
                @error('jenis_kelamin')
                    <small class="error-text">{{ $message }}</small>
                @enderror
            </div>

            {{-- Photo --}}
            <div class="input-group">
                <label>Profile Photo</label>
                <input type="file" name="photo" accept="image/*" required>
                @error('photo')
                    <small class="error-text">{{ $message }}</small>
                @enderror
            </div>

            {{-- Password --}}
            <div class="input-group password-group">
                <label>Password</label>
                <input type="password" name="password" id="password"
                    placeholder="Create a password" required>
                <span onclick="togglePassword()" class="eye-icon">👁</span>
                @error('password')
                    <small class="error-text">{{ $message }}</small>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div class="input-group">
                <label>Confirm Password</label>
                <input type="password" name="password_confirmation"
                    placeholder="Confirm your password" required>
            </div>

            <button type="submit" class="btn-auth">
                Sign Up
            </button>

            <div class="switch-text">
                Already have an account?
                <a href="{{ route('login') }}">Sign In</a>
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
