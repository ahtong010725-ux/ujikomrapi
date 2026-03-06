@extends('admin.layout')

@section('content')
    <h1 style="margin-top:0;">Manage Users</h1>
    <div class="card" style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>NISN/Name</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>
                        @if($user->photo)
                            <a href="{{ asset('storage/' . $user->photo) }}" target="_blank">
                                <img src="{{ asset('storage/' . $user->photo) }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 10px;">
                            </a>
                        @else
                            <div style="width: 50px; height: 50px; background: #eee; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #888;">No Photo</div>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $user->nisn }}</strong><br>
                        <small>{{ $user->name }}</small>
                    </td>
                    <td><span class="status-badge" style="background: {{ $user->role === 'admin' ? '#007bff' : '#6c757d' }};">{{ ucfirst($user->role) }}</span></td>
                    <td>
                        @if($user->registration_status === 'pending')
                            <span class="status-badge" style="background: #ffc107; color: black;">Pending</span>
                        @elseif($user->registration_status === 'approved')
                            <span class="status-badge" style="background: #28a745;">Approved</span>
                        @else
                            <span class="status-badge" style="background: #dc3545;">Rejected</span>
                            @if($user->rejection_reason)
                                <br><small style="color: #e53935; font-size: 11px; margin-top: 4px; display: block;">{{ $user->rejection_reason }}</small>
                            @endif
                        @endif
                    </td>
                    <td>{{ $user->created_at->format('d M Y') }}</td>
                    <td>
                        <div style="display: flex; gap: 5px; align-items: center; flex-wrap: wrap;">
                            @if($user->registration_status === 'pending' && $user->role !== 'admin')
                                <form action="{{ route('admin.users.approve', $user->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-approve">Approve</button>
                                </form>
                                <form action="{{ route('admin.users.reject', $user->id) }}" method="POST" class="reject-form">
                                    @csrf
                                    <div style="display: flex; gap: 4px; align-items: center;">
                                        <input type="text" name="rejection_reason" placeholder="Alasan reject..." style="padding: 5px 8px; border-radius: 6px; border: 1px solid #ddd; font-size: 12px; width: 140px; font-family: 'Poppins', sans-serif;">
                                        <button type="submit" class="btn-reject">Reject</button>
                                    </div>
                                </form>
                            @endif
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger" {{ auth()->id() == $user->id ? 'disabled' : '' }} style="{{ auth()->id() == $user->id ? 'opacity: 0.5; cursor: not-allowed;' : '' }}">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
