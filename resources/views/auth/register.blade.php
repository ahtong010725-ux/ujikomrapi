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
                <input type="text" name="nisn" id="nisn-input" placeholder="Masukkan NISN kamu"
                    value="{{ old('nisn') }}" required>
                <div id="nisn-status" style="margin-top: 6px; font-size: 12px; display: none;"></div>
                @error('nisn')
                    <small class="error-text">{{ $message }}</small>
                @enderror
            </div>

            {{-- Name (auto-filled, readonly) --}}
            <div class="input-group">
                <label>Full Name</label>
                <input type="text" name="name" id="name-input" placeholder="Otomatis terisi dari NISN"
                    value="{{ old('name') }}" readonly
                    style="background: rgba(0,0,0,0.03); cursor: not-allowed;">
                @error('name')
                    <small class="error-text">{{ $message }}</small>
                @enderror
            </div>

            {{-- Kelas (auto-filled, readonly) --}}
            <div class="input-group">
                <label>Kelas</label>
                <input type="text" name="kelas" id="kelas-input" placeholder="Otomatis terisi dari NISN"
                    value="{{ old('kelas') }}" readonly
                    style="background: rgba(0,0,0,0.03); cursor: not-allowed;">
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

            <button type="submit" class="btn-auth" id="submit-btn">
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

    // NISN Auto-fill
    let nisnTimeout;
    const nisnInput = document.getElementById('nisn-input');
    const nameInput = document.getElementById('name-input');
    const kelasInput = document.getElementById('kelas-input');
    const nisnStatus = document.getElementById('nisn-status');
    const submitBtn = document.getElementById('submit-btn');

    nisnInput.addEventListener('input', function() {
        clearTimeout(nisnTimeout);
        const nisn = this.value.trim();

        if (nisn.length < 4) {
            nisnStatus.style.display = 'none';
            nameInput.value = '';
            kelasInput.value = '';
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.5';
            return;
        }

        nisnStatus.style.display = 'block';
        nisnStatus.innerHTML = '<span style="color: #888;">🔍 Mencari NISN...</span>';

        nisnTimeout = setTimeout(function() {
            fetch('/api/check-nisn/' + nisn)
                .then(res => res.json())
                .then(data => {
                    if (data.found) {
                        nisnStatus.innerHTML = '<span style="color: #2e7d32; font-weight: 500;">✅ NISN ditemukan!</span>';
                        nameInput.value = data.name;
                        kelasInput.value = data.kelas;
                        submitBtn.disabled = false;
                        submitBtn.style.opacity = '1';
                    } else {
                        nisnStatus.innerHTML = '<span style="color: #e53935; font-weight: 500;">❌ ' + data.message + '</span>';
                        nameInput.value = '';
                        kelasInput.value = '';
                        submitBtn.disabled = true;
                        submitBtn.style.opacity = '0.5';
                    }
                })
                .catch(() => {
                    nisnStatus.innerHTML = '<span style="color: #e53935;">⚠️ Gagal cek NISN. Coba lagi.</span>';
                });
        }, 500);
    });

    // Initialize button state
    if (!nameInput.value) {
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.5';
    }
</script>

@endsection
