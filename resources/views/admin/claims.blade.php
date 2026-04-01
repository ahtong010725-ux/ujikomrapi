@extends('admin.layout')

@section('content')
    <h1 style="margin-top:0; font-size: 24px; color: #222; margin-bottom: 24px;">Manage Claims</h1>

    @php
        $pending = $claims->where('status', 'pending');
        $processed = $claims->where('status', '!=', 'pending');
    @endphp

    @if($pending->count() > 0)
    <div class="card" style="margin-bottom: 20px;">
        <h3 style="margin: 0 0 16px 0; font-size: 16px; color: #f9a825;">⏳ Pending Claims ({{ $pending->count() }})</h3>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Claimer</th>
                        <th>Item</th>
                        <th>Type</th>
                        <th>Bukti</th>
                        <th>Foto Bukti</th>
                        <th>Tanggal</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pending as $claim)
                    <tr>
                        <td>
                            <strong>{{ $claim->claimer->name ?? 'Unknown' }}</strong>
                            <br><small style="color:#888;">{{ $claim->claimer->kelas ?? '' }}</small>
                        </td>
                        <td>
                            @if($claim->item_model)
                                <strong>{{ $claim->item_model->item_name }}</strong>
                                <br><small style="color:#888;">by {{ $claim->item_model->user->name ?? $claim->item_model->name }}</small>
                            @else
                                <span style="color:#999;">Item dihapus</span>
                            @endif
                        </td>
                        <td>
                            <span class="status-badge" style="background: {{ $claim->item_type === 'lost' ? '#e53935' : '#28a745' }};">
                                {{ strtoupper($claim->item_type) }}
                            </span>
                        </td>
                        <td style="max-width: 200px; font-size: 12px;">{{ $claim->proof }}</td>
                        <td>
                            @if($claim->proof_photo)
                                <a href="{{ asset('storage/' . $claim->proof_photo) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $claim->proof_photo) }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd;">
                                </a>
                            @else
                                <span style="color:#999; font-size: 12px;">—</span>
                            @endif
                        </td>
                        <td>{{ $claim->created_at->format('d M Y H:i') }}</td>
                        <td>
                            <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                                <form action="{{ route('admin.claims.approve', $claim->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-approve" onclick="return confirm('Approve klaim ini dan berikan poin?')">✓ Approve</button>
                                </form>
                                <form action="{{ route('admin.claims.reject', $claim->id) }}" method="POST" style="display:flex; gap:4px;">
                                    @csrf
                                    <input type="text" name="admin_notes" placeholder="Alasan..." style="padding: 5px 8px; border-radius: 6px; border: 1px solid #ddd; font-size: 11px; width: 100px; font-family: 'Poppins', sans-serif;">
                                    <button type="submit" class="btn-reject" onclick="return confirm('Reject klaim ini?')">✕ Reject</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="card" style="margin-bottom: 20px; text-align: center; padding: 40px; color: #999;">
        <p style="font-size: 32px; margin-bottom: 8px;">✅</p>
        <p>Tidak ada klaim yang perlu diproses.</p>
    </div>
    @endif

    @if($processed->count() > 0)
    <div class="card">
        <h3 style="margin: 0 0 16px 0; font-size: 16px; color: #555;">📋 Riwayat Klaim ({{ $processed->count() }})</h3>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Claimer</th>
                        <th>Item</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Notes</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($processed as $claim)
                    <tr>
                        <td><strong>{{ $claim->claimer->name ?? 'Unknown' }}</strong></td>
                        <td>
                            @if($claim->item_model)
                                {{ $claim->item_model->item_name }}
                            @else
                                <span style="color:#999;">Item dihapus</span>
                            @endif
                        </td>
                        <td>
                            <span class="status-badge" style="background: {{ $claim->item_type === 'lost' ? '#e53935' : '#28a745' }};">
                                {{ strtoupper($claim->item_type) }}
                            </span>
                        </td>
                        <td>
                            <span class="status-badge" style="background: {{ $claim->status === 'approved' ? '#28a745' : '#dc3545' }};">
                                {{ ucfirst($claim->status) }}
                            </span>
                        </td>
                        <td style="font-size: 12px; max-width: 200px;">{{ $claim->admin_notes ?? '-' }}</td>
                        <td>{{ $claim->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
@endsection
