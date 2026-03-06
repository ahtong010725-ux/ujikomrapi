@extends('layouts.profile')

@section('content')

<div class="profile-wrapper">

    <div class="profile-card">

        <div class="profile-header">
            <img src="{{ asset('storage/' . Auth::user()->photo) }}"
                 class="profile-avatar"
                 id="photoPreview">

            <h2>{{ Auth::user()->name }}</h2>
            <p class="nisn">NISN: {{ Auth::user()->nisn }}</p>
        </div>

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

            </div>

            <div class="profile-actions" style="display: flex; justify-content: flex-end; gap: 10px;">
                @if(auth()->user()->role !== 'admin')
                    @php
                        $admin = \App\Models\User::where('role', 'admin')->first();
                    @endphp
                    @if($admin)
                        <a href="{{ route('chat', $admin->id) }}" class="btn-save" style="background: #007bff; text-decoration: none; display: inline-block;">Chat Admin</a>
                    @endif
                @endif
                <button type="submit" class="btn-save">Save Changes</button>
            </div>
        </form>
@if(session('success'))
    <div class="alert-success">
        {{ session('success') }}
    </div>
@endif

        <div class="danger-zone">
            <h3>Danger Zone</h3>
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

<script>
function previewPhoto(event) {
    const reader = new FileReader();
    reader.onload = function(){
        document.getElementById('photoPreview').src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
}
</script>

@endsection
