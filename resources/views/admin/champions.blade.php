@extends('admin.layout')
@section('content')

<style>
    .champ-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 28px;
        flex-wrap: wrap;
        gap: 12px;
    }
    .champ-header h2 {
        font-size: 22px;
        font-weight: 700;
        background: linear-gradient(135deg, #f9a825, #ff8f00);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .champ-actions-top {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    .btn-trigger {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        border: none;
        padding: 9px 18px;
        border-radius: 12px;
        cursor: pointer;
        font-size: 13px;
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        transition: all 0.25s;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .btn-trigger:hover {
        box-shadow: 0 6px 20px rgba(102,126,234,0.3);
        transform: translateY(-2px);
    }

    /* Trigger Form */
    .trigger-panel {
        background: rgba(255,255,255,0.8);
        backdrop-filter: blur(16px);
        border-radius: 16px;
        border: 1px solid rgba(102,126,234,0.12);
        padding: 24px;
        margin-bottom: 24px;
        display: none;
        animation: slideDown 0.3s ease;
    }
    .trigger-panel.show { display: block; }
    .trigger-panel h3 {
        font-size: 15px;
        color: #333;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .trigger-form-row {
        display: flex;
        gap: 12px;
        align-items: flex-end;
        flex-wrap: wrap;
    }
    .trigger-form-row .form-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .trigger-form-row label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #888;
    }
    .trigger-form-row select,
    .trigger-form-row input {
        padding: 8px 14px;
        border: 1px solid rgba(0,0,0,0.1);
        border-radius: 10px;
        font-size: 13px;
        font-family: 'Poppins', sans-serif;
        background: white;
    }

    /* Champion Cards */
    .champ-card {
        background: rgba(255,255,255,0.75);
        backdrop-filter: blur(16px);
        border-radius: 20px;
        border: 1px solid rgba(0,0,0,0.05);
        margin-bottom: 20px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 2px 16px rgba(0,0,0,0.03);
    }
    .champ-card:hover {
        box-shadow: 0 8px 40px rgba(0,0,0,0.08);
        transform: translateY(-2px);
    }
    .champ-card-banner {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 20px 28px;
        display: flex;
        align-items: center;
        gap: 16px;
        color: white;
        position: relative;
        overflow: hidden;
    }
    .champ-card-banner::before {
        content: '🏆';
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 60px;
        opacity: 0.15;
    }
    .champ-avatar {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        overflow: hidden;
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 20px;
        flex-shrink: 0;
        border: 3px solid rgba(255,255,255,0.3);
    }
    .champ-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .champ-info { position: relative; z-index: 1; }
    .champ-month {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 1px;
        opacity: 0.8;
        margin-bottom: 2px;
    }
    .champ-name { font-size: 18px; font-weight: 700; }
    .champ-class { font-size: 12px; opacity: 0.8; }

    .champ-card-body {
        padding: 20px 28px;
    }
    .champ-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 12px;
        margin-bottom: 16px;
    }
    .champ-stat {
        background: rgba(0,0,0,0.02);
        border-radius: 12px;
        padding: 14px 16px;
        text-align: center;
    }
    .champ-stat-label {
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #888;
        margin-bottom: 4px;
    }
    .champ-stat-value {
        font-size: 18px;
        font-weight: 700;
        color: #333;
    }
    .champ-stat-value.points { color: #667eea; }
    .champ-stat-value.reward { color: #2e7d32; }
    .champ-stat-value.pending-val { color: #e65100; }

    .champ-ewallet {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        background: rgba(102,126,234,0.05);
        border-radius: 10px;
        font-size: 12px;
        color: #667eea;
        margin-bottom: 16px;
    }
    .champ-ewallet.not-set {
        background: rgba(229,57,53,0.05);
        color: #e53935;
    }

    /* Reward Form */
    .champ-form {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 10px;
        align-items: end;
        padding-top: 16px;
        border-top: 1px solid rgba(0,0,0,0.04);
    }
    .champ-form .form-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .champ-form label {
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #888;
    }
    .champ-form input,
    .champ-form select,
    .champ-form textarea {
        padding: 8px 12px;
        border: 1px solid rgba(0,0,0,0.1);
        border-radius: 10px;
        font-size: 12px;
        font-family: 'Poppins', sans-serif;
        background: rgba(255,255,255,0.8);
        transition: all 0.2s;
        width: 100%;
    }
    .champ-form input:focus,
    .champ-form select:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
    }

    .btn-save-champ {
        background: linear-gradient(135deg, #2e7d32, #43a047);
        color: white;
        border: none;
        padding: 9px 18px;
        border-radius: 10px;
        cursor: pointer;
        font-size: 12px;
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        transition: all 0.25s;
        display: flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
    }
    .btn-save-champ:hover {
        box-shadow: 0 4px 16px rgba(46,125,50,0.3);
        transform: translateY(-1px);
    }
    .btn-give-reward {
        background: linear-gradient(135deg, #f9a825, #ff8f00);
        color: white;
        border: none;
        padding: 9px 18px;
        border-radius: 10px;
        cursor: pointer;
        font-size: 12px;
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        transition: all 0.25s;
        display: flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
    }
    .btn-give-reward:hover {
        box-shadow: 0 4px 16px rgba(249,168,37,0.3);
        transform: translateY(-1px);
    }
    .champ-form-buttons {
        display: flex;
        gap: 8px;
        grid-column: 1 / -1;
        margin-top: 4px;
    }

    .empty-state {
        text-align: center;
        padding: 80px 40px;
        color: #aaa;
    }
    .empty-state .icon { font-size: 56px; margin-bottom: 16px; opacity: 0.6; }
    .empty-state p { font-size: 15px; }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Dark Mode */
    body.dark-theme .champ-header h2 {
        background: linear-gradient(135deg, #fdd835, #ffb300);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    body.dark-theme .trigger-panel {
        background: rgba(30,30,35,0.85);
        border-color: rgba(129,140,248,0.15);
    }
    body.dark-theme .trigger-panel h3 { color: #eee; }
    body.dark-theme .trigger-form-row select,
    body.dark-theme .trigger-form-row input {
        background: rgba(255,255,255,0.06);
        border-color: rgba(255,255,255,0.1);
        color: #ddd;
    }
    body.dark-theme .trigger-form-row label { color: #777; }
    body.dark-theme .champ-card {
        background: rgba(30,30,35,0.8);
        border-color: rgba(255,255,255,0.05);
        box-shadow: 0 2px 16px rgba(0,0,0,0.2);
    }
    body.dark-theme .champ-card:hover {
        box-shadow: 0 8px 40px rgba(0,0,0,0.4);
    }
    body.dark-theme .champ-card-banner {
        background: linear-gradient(135deg, #4c51bf 0%, #6d28d9 100%);
    }
    body.dark-theme .champ-stat {
        background: rgba(255,255,255,0.03);
    }
    body.dark-theme .champ-stat-label { color: #666; }
    body.dark-theme .champ-stat-value { color: #eee; }
    body.dark-theme .champ-stat-value.points { color: #818cf8; }
    body.dark-theme .champ-stat-value.reward { color: #66bb6a; }
    body.dark-theme .champ-stat-value.pending-val { color: #ffb74d; }
    body.dark-theme .champ-ewallet {
        background: rgba(129,140,248,0.08);
        color: #818cf8;
    }
    body.dark-theme .champ-ewallet.not-set {
        background: rgba(229,57,53,0.08);
        color: #ef5350;
    }
    body.dark-theme .champ-form {
        border-top-color: rgba(255,255,255,0.04);
    }
    body.dark-theme .champ-form input,
    body.dark-theme .champ-form select,
    body.dark-theme .champ-form textarea {
        background: rgba(255,255,255,0.06);
        border-color: rgba(255,255,255,0.1);
        color: #ddd;
    }
    body.dark-theme .champ-form label { color: #666; }
    body.dark-theme .empty-state { color: #555; }
</style>

<div class="champ-header">
    <h2>🏆 Monthly Champions</h2>
    <div class="champ-actions-top">
        <button type="button" class="btn-trigger" onclick="document.getElementById('trigger-panel').classList.toggle('show')">
            ⚡ Generate Champion
        </button>
    </div>
</div>

{{-- Trigger Champion Panel --}}
<div class="trigger-panel" id="trigger-panel">
    <h3>⚡ Generate Champion dari Leaderboard</h3>
    <form action="{{ route('admin.champions.trigger') }}" method="POST">
        @csrf
        <div class="trigger-form-row">
            <div class="form-group">
                <label>Bulan</label>
                <select name="month">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $m == $currentMonth ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::createFromDate(2026, $m, 1)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="form-group">
                <label>Tahun</label>
                <select name="year">
                    @for($y = 2025; $y <= 2027; $y++)
                        <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="btn-trigger">🏆 Generate</button>
        </div>
        <p style="font-size: 11px; color: #888; margin-top: 10px;">
            * Otomatis memilih user dengan poin tertinggi di bulan yang dipilih dari leaderboard.
        </p>
    </form>
</div>

@if($champions->count() > 0)
    @foreach($champions as $i => $champ)
    <div class="champ-card" style="animation: fadeInUp 0.4s ease {{ $i * 0.1 }}s both;">
        {{-- Banner --}}
        <div class="champ-card-banner">
            <div class="champ-avatar">
                @if($champ->user && $champ->user->photo)
                    <img src="{{ asset('storage/' . $champ->user->photo) }}">
                @else
                    {{ strtoupper(substr($champ->user->name ?? '?', 0, 1)) }}
                @endif
            </div>
            <div class="champ-info">
                <div class="champ-month">Champion {{ \Carbon\Carbon::createFromDate($champ->year, $champ->month, 1)->translatedFormat('F Y') }}</div>
                <div class="champ-name">{{ $champ->user->name ?? 'Deleted User' }}</div>
                <div class="champ-class">{{ $champ->user->kelas ?? '' }}</div>
            </div>
        </div>

        {{-- Body --}}
        <div class="champ-card-body">
            <div class="champ-stats">
                <div class="champ-stat">
                    <div class="champ-stat-label">Poin</div>
                    <div class="champ-stat-value points">{{ $champ->points }} <small style="font-size:11px;font-weight:500;">pts</small></div>
                </div>
                <div class="champ-stat">
                    <div class="champ-stat-label">Hadiah</div>
                    <div class="champ-stat-value {{ $champ->reward_amount ? 'reward' : 'pending-val' }}">
                        {{ $champ->reward_amount ? 'Rp ' . number_format($champ->reward_amount, 0, ',', '.') : 'Belum diset' }}
                    </div>
                </div>
                <div class="champ-stat">
                    <div class="champ-stat-label">Status</div>
                    <div class="champ-stat-value">
                        @if($champ->reward_status === 'paid')
                            <span style="color:#2e7d32; font-size:13px;">✅ Paid</span>
                            @if($champ->paid_at)
                                <br><small style="font-size:10px; color:#888;">{{ $champ->paid_at->format('d-m-Y') }}</small>
                            @endif
                        @else
                            <span style="color:#e65100; font-size:13px;">⏳ Pending</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- E-Wallet Info --}}
            @if($champ->user)
                @if($champ->user->ewallet_type && $champ->user->ewallet_number)
                    <div class="champ-ewallet">
                        💳 {{ $champ->user->ewallet_type }}: {{ $champ->user->ewallet_number }}
                    </div>
                @else
                    <div class="champ-ewallet not-set">
                        ⚠️ User belum mengisi info e-wallet di profile
                    </div>
                @endif
            @endif

            {{-- Edit Form --}}
            <form action="{{ route('admin.champions.update', $champ->id) }}" method="POST" class="champ-form">
                @csrf
                <div class="form-group">
                    <label>Jumlah Hadiah</label>
                    <input type="text" name="reward_amount" value="{{ $champ->reward_amount ? 'Rp ' . number_format($champ->reward_amount, 0, ',', '.') : '' }}" placeholder="Rp 100.000">
                </div>
                <div class="form-group">
                    <label>Status Bayar</label>
                    <select name="reward_status">
                        <option value="pending" {{ $champ->reward_status === 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                        <option value="paid" {{ $champ->reward_status === 'paid' ? 'selected' : '' }}>✅ Paid</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Catatan</label>
                    <input type="text" name="notes" value="{{ $champ->notes }}" placeholder="Catatan admin...">
                </div>
                <div class="champ-form-buttons">
                    <button type="submit" class="btn-save-champ">💾 Simpan</button>
                </div>
            </form>

            {{-- Give Reward Button --}}
            <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid rgba(0,0,0,0.04);">
                <form action="{{ route('admin.champions.giveReward', $champ->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Kirim pesan selamat & info hadiah ke {{ $champ->user->name ?? 'user' }}?')">
                    @csrf
                    <button type="submit" class="btn-give-reward">🎁 Beri Hadiah & Kirim Pesan Selamat</button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
@else
<div class="card empty-state">
    <div class="icon">🏆</div>
    <p>Belum ada data champion. Gunakan tombol "Generate Champion" untuk memilih champion dari leaderboard.</p>
</div>
@endif

@endsection
