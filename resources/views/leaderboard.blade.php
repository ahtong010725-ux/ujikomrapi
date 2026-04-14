<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Leaderboard | I FOUND</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/home.css') }}">

<style>
.lb-container {
    max-width: 800px;
    margin: 30px auto;
    padding: 0 20px;
    animation: fadeInUp 0.5s ease;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(16px); }
    to { opacity: 1; transform: translateY(0); }
}

.lb-header {
    text-align: center;
    margin-bottom: 30px;
}

.lb-header h1 {
    font-size: 32px;
    background: linear-gradient(135deg, #1a1a2e, #4a4a6a);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 8px;
}

.lb-header p {
    color: #888;
    font-size: 14px;
}

/* Badges section */
.badges-section {
    background: rgba(255,255,255,0.7);
    backdrop-filter: blur(12px);
    border-radius: 20px;
    padding: 24px;
    margin-bottom: 24px;
    border: 1px solid rgba(255,255,255,0.6);
    box-shadow: 0 4px 24px rgba(0,0,0,0.04);
}

.badges-section h3 {
    font-size: 16px;
    color: #333;
    margin-bottom: 16px;
}

.badge-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 12px;
}

.badge-card {
    background: rgba(102,126,234,0.04);
    border-radius: 14px;
    padding: 16px;
    text-align: center;
    border: 1px solid rgba(102,126,234,0.08);
    transition: all 0.3s ease;
}

.badge-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(102,126,234,0.1);
}

.badge-card .badge-icon {
    font-size: 32px;
    margin-bottom: 8px;
}

.badge-card .badge-name {
    font-weight: 600;
    font-size: 13px;
    color: #333;
}

.badge-card .badge-req {
    font-size: 11px;
    color: #888;
    margin-top: 4px;
}

/* Leaderboard table */
.lb-table {
    background: rgba(255,255,255,0.7);
    backdrop-filter: blur(12px);
    border-radius: 20px;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.6);
    box-shadow: 0 4px 24px rgba(0,0,0,0.04);
}

.lb-table-header {
    background: linear-gradient(135deg, #1a1a2e, #2d2d4e);
    color: white;
    padding: 16px 24px;
    font-weight: 600;
    font-size: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.lb-row {
    display: flex;
    align-items: center;
    padding: 14px 24px;
    border-bottom: 1px solid rgba(0,0,0,0.04);
    transition: background 0.2s;
    gap: 16px;
}

.lb-row:hover {
    background: rgba(249,168,37,0.04);
}

.lb-rank {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 14px;
    flex-shrink: 0;
}

.lb-rank.gold { background: linear-gradient(135deg, #667eea, #764ba2); color: white; box-shadow: 0 4px 12px rgba(102,126,234,0.3); }
.lb-rank.silver { background: linear-gradient(135deg, #94a3b8, #64748b); color: white; }
.lb-rank.bronze { background: linear-gradient(135deg, #a8a29e, #78716c); color: white; }
.lb-rank.normal { background: rgba(0,0,0,0.04); color: rgba(26,26,46,0.5); }

.lb-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
    background: #111;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 16px;
}

.lb-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.lb-user-info {
    flex: 1;
    min-width: 0;
}

.lb-user-info strong {
    font-size: 14px;
    color: #222;
    display: flex;
    align-items: center;
    gap: 6px;
}

.lb-user-info small {
    font-size: 11px;
    color: #888;
}

.lb-points {
    font-weight: 700;
    font-size: 18px;
    color: #111;
    flex-shrink: 0;
}

.lb-points small {
    font-size: 11px;
    font-weight: 500;
    color: #aaa;
}

.lb-user-badge {
    font-size: 10px;
    padding: 2px 8px;
    border-radius: 8px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    font-weight: 600;
}

.lb-empty {
    padding: 60px 40px;
    text-align: center;
    color: #999;
    font-size: 15px;
}

/* Dark Mode */
body.dark-theme .badges-section { background: rgba(255,255,255,0.03); border-color: rgba(255,255,255,0.06); }
body.dark-theme .badges-section h3 { color: #eee; }
body.dark-theme .badge-card { background: rgba(129,140,248,0.06); border-color: rgba(129,140,248,0.1); }
body.dark-theme .badge-card .badge-name { color: #eee; }
body.dark-theme .lb-table { background: rgba(255,255,255,0.03); border-color: rgba(255,255,255,0.06); }
body.dark-theme .lb-row { border-bottom-color: rgba(255,255,255,0.04); }
body.dark-theme .lb-row:hover { background: rgba(129,140,248,0.04); }
body.dark-theme .lb-user-info strong { color: #eee; }
body.dark-theme .lb-header h1 { background: linear-gradient(135deg, #818cf8, #a78bfa); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
body.dark-theme .lb-table-header { background: linear-gradient(135deg, #12121a, #1e1e30); }
</style>
</head>
<body>

@include('components.navbar')

<div class="lb-container">

    <div class="lb-header">
        <h1>🏆 Leaderboard</h1>
        <p>Poin didapat dari menemukan & melaporkan barang orang lain di halaman Found</p>
        <div style="margin-top: 12px; display: flex; gap: 8px; justify-content: center; align-items: center;">
            <a href="/leaderboard?month={{ $month - 1 < 1 ? 12 : $month - 1 }}&year={{ $month - 1 < 1 ? $year - 1 : $year }}" style="padding: 6px 14px; border-radius: 10px; background: rgba(102,126,234,0.1); color: #667eea; font-size: 13px; text-decoration: none; font-weight: 500;">← Prev</a>
            <span style="font-size: 13px; color: #888;">{{ $monthName }}</span>
            @if(!($month == now()->month && $year == now()->year))
            <a href="/leaderboard?month={{ $month + 1 > 12 ? 1 : $month + 1 }}&year={{ $month + 1 > 12 ? $year + 1 : $year }}" style="padding: 6px 14px; border-radius: 10px; background: rgba(102,126,234,0.1); color: #667eea; font-size: 13px; text-decoration: none; font-weight: 500;">Next →</a>
            @endif
        </div>
    </div>

    {{-- Champion Banner --}}
    @if($champion)
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px; padding: 24px 28px; margin-bottom: 24px; color: white; display: flex; align-items: center; gap: 20px; box-shadow: 0 8px 32px rgba(102,126,234,0.3); animation: fadeInUp 0.5s ease;">
        <div style="font-size: 48px;">🏆</div>
        <div style="flex:1;">
            <div style="font-size: 12px; text-transform: uppercase; letter-spacing: 1px; opacity: 0.8; margin-bottom: 4px;">Champion {{ $monthName }}</div>
            <div style="font-size: 20px; font-weight: 700;">{{ $champion->user->name ?? 'User' }}</div>
            <div style="font-size: 13px; opacity: 0.9; margin-top: 2px;">{{ $champion->points }} poin</div>
        </div>
        <div style="text-align: right;">
            @if($champion->reward_amount)
                <div style="font-size: 11px; opacity: 0.8;">Hadiah</div>
                <div style="font-size: 18px; font-weight: 700;">Rp {{ number_format($champion->reward_amount, 0, ',', '.') }}</div>
                @if($champion->reward_status === 'paid')
                    <div style="font-size: 11px; background: rgba(255,255,255,0.2); padding: 2px 10px; border-radius: 8px; margin-top: 4px;">✅ Sudah dibayar</div>
                @else
                    <div style="font-size: 11px; background: rgba(255,255,255,0.2); padding: 2px 10px; border-radius: 8px; margin-top: 4px;">⏳ Menunggu</div>
                @endif
            @else
                <div style="font-size: 12px; opacity: 0.7;">Hadiah segera diumumkan</div>
            @endif
        </div>
    </div>
    @endif

    @if($badges->count() > 0)
    <div class="badges-section">
        <h3>🎖️ Available Badges</h3>
        <div class="badge-grid">
            @foreach($badges as $badge)
            <div class="badge-card">
                <div class="badge-icon">{{ $badge->icon }}</div>
                <div class="badge-name">{{ $badge->name }}</div>
                <div class="badge-req">{{ $badge->points_required }} poin</div>
                @if($badge->description)
                <div style="font-size:11px; color:#999; margin-top:4px;">{{ $badge->description }}</div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="lb-table">
        <div class="lb-table-header">
            🏆 Top Rankings — {{ $monthName }}
        </div>

        @forelse($users as $index => $userPoint)
        <div class="lb-row">
            <div class="lb-rank {{ $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : 'normal')) }}">
                {{ $index + 1 }}
            </div>

            <div class="lb-avatar">
                @if($userPoint->user->photo)
                    <img src="{{ asset('storage/' . $userPoint->user->photo) }}" alt="">
                @else
                    {{ strtoupper(substr($userPoint->user->name, 0, 1)) }}
                @endif
            </div>

            <div class="lb-user-info">
                <strong>
                    {{ $userPoint->user->name }}
                    @php $highestBadge = $userPoint->user->getHighestBadge(); @endphp
                    @if($highestBadge)
                        <span class="lb-user-badge">{{ $highestBadge->icon }} {{ $highestBadge->name }}</span>
                    @endif
                </strong>
                <small>{{ $userPoint->user->kelas ?? '' }} • {{ $userPoint->total_earned }} total poin</small>
            </div>

            <div class="lb-points">
                {{ $userPoint->points }}
                <small>pts</small>
            </div>
        </div>
        @empty
        <div class="lb-empty">
            Belum ada data leaderboard. Jadilah yang pertama! 🚀
        </div>
        @endforelse
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
// Auto-refresh leaderboard every 30 seconds
setInterval(function() {
    fetch(window.location.href)
        .then(res => res.text())
        .then(html => {
            let parser = new DOMParser();
            let doc = parser.parseFromString(html, 'text/html');
            let newTable = doc.querySelector('.lb-table');
            if (newTable) {
                document.querySelector('.lb-table').innerHTML = newTable.innerHTML;
            }
        });
}, 30000);
</script>

</body>
</html>
