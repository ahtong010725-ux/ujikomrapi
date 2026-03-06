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
                                <img src="{{ asset('storage/' . $user->photo) }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                            </a>
                        @else
                            <div style="width: 50px; height: 50px; background: #eee; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #888;">No Photo</div>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $user->nisn }}</strong><br>
                        <small>{{ $user->name }}</small>
                    </td>
                    <td><span style="background: {{ $user->role === 'admin' ? '#007bff' : '#6c757d' }}; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px;">{{ ucfirst($user->role) }}</span></td>
                    <td>
                        @if($user->registration_status === 'pending')
                            <span style="background: #ffc107; color: black; padding: 2px 8px; border-radius: 12px; font-size: 12px;">Pending</span>
                        @elseif($user->registration_status === 'approved')
                            <span style="background: #28a745; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px;">Approved</span>
                        @else
                            <span style="background: #dc3545; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px;">Rejected</span>
                        @endif
                    </td>
                    <td>{{ $user->created_at->format('d M Y') }}</td>
                    <td style="display: flex; gap: 5px; align-items: center;">
                        @if($user->registration_status === 'pending' && $user->role !== 'admin')
                            <form action="{{ route('admin.users.approve', $user->id) }}" method="POST">
                                @csrf
                                <button type="submit" style="background: #28a745; color: white; border: none; padding: 6px 10px; border-radius: 6px; cursor: pointer;">Approve</button>
                            </form>
                            <form action="{{ route('admin.users.reject', $user->id) }}" method="POST">
                                @csrf
                                <button type="submit" style="background: #ffc107; color: black; border: none; padding: 6px 10px; border-radius: 6px; cursor: pointer;">Reject</button>
                            </form>
                        @endif
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger" {{ auth()->id() == $user->id ? 'disabled' : '' }} style="{{ auth()->id() == $user->id ? 'opacity: 0.5; cursor: not-allowed;' : '' }}">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
