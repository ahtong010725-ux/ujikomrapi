<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile | I FOUND</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
</head>
<body>

@include('components.navbar')

<div class="profile-wrapper">
    <div class="profile-card">

        <!-- Cover & Avatar -->
        <div class="profile-cover">
            <div class="profile-avatar-wrap">
                <img src="{{ asset('storage/' . Auth::user()->photo) }}"
                     class="profile-avatar"
                     id="photoPreview">
                <div class="online-indicator {{ Auth::user()->is_online ? 'is-online' : '' }}"></div>
            </div>
        </div>

        <div class="profile-info-section">
            <h2>{{ Auth::user()->name }}</h2>
            <p class="nisn">NISN: {{ Auth::user()->nisn }}</p>
            <div class="profile-badges">
                <span class="profile-badge">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    {{ Auth::user()->kelas }}
                </span>
                <span class="profile-badge">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
                    {{ Auth::user()->phone }}
                </span>
                <span class="profile-badge">{{ Auth::user()->jenis_kelamin }}</span>
                @if(Auth::user()->tanggal_lahir)
                <span class="profile-badge">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    {{ \Carbon\Carbon::parse(Auth::user()->tanggal_lahir)->format('d M Y') }}
                </span>
                @endif
            </div>
        </div>

        <div class="profile-divider"></div>

        <h3 class="section-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Edit Profile
        </h3>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="profile-form-grid">

                <div class="input-group">
                    <label>Change Photo</label>
                    <input type="file" name="photo" onchange="previewPhoto(event)">
                </div>

                <div class="input-group">
                    <label>Full Name</label>
                    <input type="text" name="name" value="{{ Auth::user()->name }}">
                </div>

                <div class="input-group">
                    <label>Class</label>
                    <input type="text" name="kelas" value="{{ Auth::user()->kelas }}">
                </div>

                <div class="input-group">
                    <label>Phone</label>
                    <input type="text" name="phone" value="{{ Auth::user()->phone }}">
                </div>

                <div class="input-group">
                    <label>Date of Birth</label>
                    <input type="date" name="tanggal_lahir" value="{{ Auth::user()->tanggal_lahir }}">
                </div>

                <div class="input-group">
                    <label>Gender</label>
                    <select name="jenis_kelamin">
                        <option value="Laki-laki" {{ Auth::user()->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ Auth::user()->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div class="input-group">
                    <label>E-Wallet</label>
                    <select name="ewallet_type">
                        <option value="">-- Pilih E-Wallet --</option>
                        <option value="Dana" {{ Auth::user()->ewallet_type == 'Dana' ? 'selected' : '' }}>Dana</option>
                        <option value="GoPay" {{ Auth::user()->ewallet_type == 'GoPay' ? 'selected' : '' }}>GoPay</option>
                        <option value="OVO" {{ Auth::user()->ewallet_type == 'OVO' ? 'selected' : '' }}>OVO</option>
                        <option value="ShopeePay" {{ Auth::user()->ewallet_type == 'ShopeePay' ? 'selected' : '' }}>ShopeePay</option>
                    </select>
                </div>

                <div class="input-group">
                    <label>Nomor E-Wallet</label>
                    <input type="text" name="ewallet_number" value="{{ Auth::user()->ewallet_number }}" placeholder="08xxxxxxxxxx">
                </div>

            </div>

            <div class="profile-actions">
                @if(auth()->user()->role !== 'admin')
                    @php
                        $admin = \App\Models\User::where('role', 'admin')->first();
                    @endphp
                    @if($admin)
                        <a href="{{ route('chat', $admin->id) }}" class="btn-chat-admin">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                            Chat Admin
                        </a>
                    @endif
                @endif
                <button type="submit" class="btn-save">Save Changes</button>
            </div>
        </form>




        <div class="danger-zone">
            <h3>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                Danger Zone
            </h3>
            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('DELETE')
                <button class="btn-delete"
                    onclick="return confirm('Delete your account permanently?')">
                    Delete Account
                </button>
            </form>
        </div>

    </div>
</div>

<footer>
    <div><h4>Site</h4>Lost<br>Report Lost<br>Found<br>Report Found</div>
    <div><h4>Help</h4>Customer Support<br>Terms & Conditions<br>Privacy Policy</div>
    <div><h4>Links</h4>LinkedIn<br>Facebook<br>YouTube<br>About Us</div>
    <div><h4>Contact</h4>Tel: +94 716520690<br>Email: talkprojects@wenix.com</div>
</footer>

<script src="{{ asset('js/home.js') }}"></script>
<script src="{{ asset('js/theme.js') }}"></script>

<script>
function previewPhoto(event) {
    const reader = new FileReader();
    reader.onload = function(){
        document.getElementById('photoPreview').src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
}
</script>

</body>
</html>
