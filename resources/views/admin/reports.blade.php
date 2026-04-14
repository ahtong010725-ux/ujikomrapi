@extends('admin.layout')
@section('content')

<style>
    .reports-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 28px;
    }
    .reports-header h2 {
        font-size: 22px;
        font-weight: 700;
        background: linear-gradient(135deg, #e53935, #ff7043);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .reports-stats {
        display: flex;
        gap: 12px;
    }
    .stat-pill {
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .stat-pill.pending {
        background: linear-gradient(135deg, rgba(229,57,53,0.1), rgba(255,112,67,0.1));
        color: #e53935;
    }
    .stat-pill.resolved {
        background: linear-gradient(135deg, rgba(46,125,50,0.1), rgba(67,160,71,0.1));
        color: #2e7d32;
    }

    .report-card {
        background: rgba(255,255,255,0.75);
        backdrop-filter: blur(16px);
        border-radius: 16px;
        border: 1px solid rgba(0,0,0,0.06);
        padding: 0;
        margin-bottom: 16px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 2px 12px rgba(0,0,0,0.03);
    }
    .report-card:hover {
        box-shadow: 0 8px 32px rgba(0,0,0,0.08);
        transform: translateY(-2px);
    }
    .report-card-top {
        display: flex;
        align-items: stretch;
        gap: 0;
    }
    .report-status-bar {
        width: 5px;
        flex-shrink: 0;
    }
    .report-status-bar.pending { background: linear-gradient(180deg, #e53935, #ff7043); }
    .report-status-bar.resolved { background: linear-gradient(180deg, #2e7d32, #43a047); }
    .report-status-bar.banned { background: linear-gradient(180deg, #b71c1c, #c62828); }

    .report-body {
        flex: 1;
        padding: 20px 24px;
    }
    .report-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
        flex-wrap: wrap;
        gap: 8px;
    }
    .report-users {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
    }
    .report-user-chip {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px 4px 4px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 12px;
    }
    .report-user-chip.reporter {
        background: rgba(102,126,234,0.08);
        color: #667eea;
    }
    .report-user-chip.reported {
        background: rgba(229,57,53,0.08);
        color: #e53935;
    }
    .report-user-chip .chip-avatar {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #333;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 700;
        overflow: hidden;
        flex-shrink: 0;
    }
    .report-user-chip .chip-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .report-arrow {
        color: #ccc;
        font-size: 14px;
    }

    .report-badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        color: white;
    }
    .report-badge.pending { background: linear-gradient(135deg, #e65100, #f57c00); }
    .report-badge.resolved { background: linear-gradient(135deg, #2e7d32, #43a047); }
    .report-badge.banned { background: linear-gradient(135deg, #b71c1c, #c62828); }

    .report-reason {
        background: rgba(0,0,0,0.02);
        border-radius: 12px;
        padding: 14px 18px;
        font-size: 13px;
        color: #444;
        line-height: 1.6;
        margin-bottom: 14px;
        border-left: 3px solid rgba(229,57,53,0.3);
    }
    .report-date {
        font-size: 11px;
        color: #999;
    }
    .report-admin-notes {
        background: rgba(46,125,50,0.05);
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 12px;
        color: #2e7d32;
        margin-top: 10px;
        border-left: 3px solid rgba(46,125,50,0.3);
    }

    .report-actions {
        border-top: 1px solid rgba(0,0,0,0.04);
        padding: 16px 24px;
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: flex-end;
    }
    .report-actions .action-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .report-actions label {
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #888;
    }
    .report-actions input,
    .report-actions select {
        padding: 7px 12px;
        border: 1px solid rgba(0,0,0,0.1);
        border-radius: 10px;
        font-size: 12px;
        font-family: 'Poppins', sans-serif;
        background: rgba(255,255,255,0.8);
        transition: all 0.2s;
    }
    .report-actions input:focus,
    .report-actions select:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
    }
    .btn-resolve {
        background: linear-gradient(135deg, #2e7d32, #43a047);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 10px;
        cursor: pointer;
        font-size: 12px;
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        transition: all 0.25s;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .btn-resolve:hover {
        box-shadow: 0 4px 16px rgba(46,125,50,0.3);
        transform: translateY(-1px);
    }
    .btn-ban {
        background: linear-gradient(135deg, #c62828, #e53935);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 10px;
        cursor: pointer;
        font-size: 12px;
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        transition: all 0.25s;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .btn-ban:hover {
        box-shadow: 0 4px 16px rgba(198,40,40,0.3);
        transform: translateY(-1px);
    }

    .ban-form {
        display: none;
        border-top: 1px solid rgba(229,57,53,0.1);
        padding: 16px 24px;
        background: rgba(229,57,53,0.02);
        animation: slideDown 0.3s ease;
    }
    .ban-form.show { display: block; }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .ban-form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 10px;
        margin-bottom: 12px;
    }
    .ban-form-grid .action-group select,
    .ban-form-grid .action-group input {
        width: 100%;
    }

    .empty-state {
        text-align: center;
        padding: 80px 40px;
        color: #aaa;
    }
    .empty-state .icon { font-size: 56px; margin-bottom: 16px; opacity: 0.6; }
    .empty-state p { font-size: 15px; }

    /* Dark Mode */
    body.dark-theme .report-card {
        background: rgba(30,30,35,0.8);
        border-color: rgba(255,255,255,0.06);
        box-shadow: 0 2px 12px rgba(0,0,0,0.2);
    }
    body.dark-theme .report-card:hover {
        box-shadow: 0 8px 32px rgba(0,0,0,0.4);
    }
    body.dark-theme .report-reason {
        background: rgba(255,255,255,0.03);
        color: #ccc;
        border-left-color: rgba(229,57,53,0.4);
    }
    body.dark-theme .report-user-chip.reporter {
        background: rgba(129,140,248,0.1);
        color: #818cf8;
    }
    body.dark-theme .report-user-chip.reported {
        background: rgba(229,57,53,0.1);
        color: #ef5350;
    }
    body.dark-theme .report-arrow { color: #555; }
    body.dark-theme .report-date { color: #666; }
    body.dark-theme .report-admin-notes {
        background: rgba(46,125,50,0.08);
        color: #66bb6a;
        border-left-color: rgba(46,125,50,0.4);
    }
    body.dark-theme .report-actions {
        border-top-color: rgba(255,255,255,0.04);
    }
    body.dark-theme .report-actions input,
    body.dark-theme .report-actions select {
        background: rgba(255,255,255,0.06);
        border-color: rgba(255,255,255,0.1);
        color: #ddd;
    }
    body.dark-theme .report-actions label { color: #777; }
    body.dark-theme .ban-form {
        background: rgba(229,57,53,0.03);
        border-top-color: rgba(229,57,53,0.08);
    }
    body.dark-theme .stat-pill.pending {
        background: rgba(229,57,53,0.12);
        color: #ef5350;
    }
    body.dark-theme .stat-pill.resolved {
        background: rgba(46,125,50,0.12);
        color: #66bb6a;
    }
    body.dark-theme .reports-header h2 {
        background: linear-gradient(135deg, #ef5350, #ff8a65);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    body.dark-theme .empty-state { color: #555; }
</style>

<div class="reports-header">
    <h2>🚩 User Reports</h2>
    <div class="reports-stats">
        <div class="stat-pill pending">
            <span>⏳</span>
            {{ $reports->where('status', 'pending')->count() }} Pending
        </div>
        <div class="stat-pill resolved">
            <span>✅</span>
            {{ $reports->where('status', 'resolved')->count() }} Resolved
        </div>
    </div>
</div>

@if($reports->count() > 0)
    @foreach($reports as $i => $report)
    <div class="report-card" style="animation: fadeInUp 0.4s ease {{ $i * 0.08 }}s both;">
        <div class="report-card-top">
            <div class="report-status-bar {{ $report->status }}"></div>
            <div class="report-body">
                <div class="report-meta">
                    <div class="report-users">
                        <div class="report-user-chip reporter">
                            <div class="chip-avatar">
                                @if($report->reporter && $report->reporter->photo)
                                    <img src="{{ asset('storage/' . $report->reporter->photo) }}">
                                @else
                                    {{ strtoupper(substr($report->reporter->name ?? '?', 0, 1)) }}
                                @endif
                            </div>
                            {{ $report->reporter->name ?? 'Deleted' }}
                        </div>
                        <span class="report-arrow">→</span>
                        <div class="report-user-chip reported">
                            <div class="chip-avatar">
                                @if($report->reportedUser && $report->reportedUser->photo)
                                    <img src="{{ asset('storage/' . $report->reportedUser->photo) }}">
                                @else
                                    {{ strtoupper(substr($report->reportedUser->name ?? '?', 0, 1)) }}
                                @endif
                            </div>
                            {{ $report->reportedUser->name ?? 'Deleted' }}
                            @if($report->reportedUser && $report->reportedUser->ban_type)
                                <span style="font-size:10px;">🚫</span>
                            @endif
                        </div>
                    </div>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span class="report-badge {{ $report->status }}">
                            {{ $report->status === 'pending' ? '⏳ Pending' : '✅ Resolved' }}
                        </span>
                        <span class="report-date">{{ $report->created_at->format('d-m-Y H:i') }}</span>
                    </div>
                </div>
                <div class="report-reason">
                    {{ $report->reason }}
                </div>
                @if($report->admin_notes && $report->status === 'resolved')
                <div class="report-admin-notes">
                    📝 {{ $report->admin_notes }}
                </div>
                @endif
            </div>
        </div>

        @if($report->status === 'pending')
        {{-- Resolve Form --}}
        <div class="report-actions">
            <form action="{{ route('admin.reports.resolve', $report->id) }}" method="POST" style="display:flex; gap:8px; align-items:flex-end; flex-wrap:wrap;">
                @csrf
                <div class="action-group">
                    <label>Catatan Admin</label>
                    <input type="text" name="admin_notes" placeholder="Catatan untuk user..." style="width: 200px;">
                </div>
                <button type="submit" class="btn-resolve">✅ Resolve & Kirim Pesan</button>
            </form>
            <button type="button" class="btn-ban" onclick="toggleBanForm({{ $report->id }})">🚫 Ban User</button>
        </div>

        {{-- Ban Form (hidden by default) --}}
        <div class="ban-form" id="ban-form-{{ $report->id }}">
            <form action="{{ route('admin.reports.ban', $report->id) }}" method="POST">
                @csrf
                <div class="ban-form-grid">
                    <div class="action-group">
                        <label>Tipe Ban</label>
                        <select name="ban_type" onchange="toggleDuration(this, {{ $report->id }})">
                            <option value="soft">🟡 Soft Ban</option>
                            <option value="hard">🔴 Hard Ban</option>
                        </select>
                    </div>
                    <div class="action-group" id="duration-group-{{ $report->id }}" style="display:none;">
                        <label>Durasi (Hari)</label>
                        <input type="number" name="ban_duration" placeholder="7" min="1" max="365">
                    </div>
                    <div class="action-group">
                        <label>Alasan Ban</label>
                        <input type="text" name="ban_reason" value="{{ $report->reason }}" style="width:100%;">
                    </div>
                </div>
                <div style="display:flex; gap:8px;">
                    <button type="submit" class="btn-ban">🚫 Konfirmasi Ban</button>
                    <button type="button" onclick="toggleBanForm({{ $report->id }})" style="background:none; border:1px solid rgba(0,0,0,0.1); padding:8px 16px; border-radius:10px; cursor:pointer; font-size:12px; font-family:'Poppins',sans-serif; color:#888;">Batal</button>
                </div>
            </form>
        </div>
        @endif
    </div>
    @endforeach
@else
<div class="card empty-state">
    <div class="icon">🎉</div>
    <p>Belum ada laporan pengguna.</p>
</div>
@endif

<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
function toggleBanForm(id) {
    const form = document.getElementById('ban-form-' + id);
    form.classList.toggle('show');
}
function toggleDuration(select, id) {
    const group = document.getElementById('duration-group-' + id);
    group.style.display = select.value === 'hard' ? 'flex' : 'none';
}
</script>

@endsection
