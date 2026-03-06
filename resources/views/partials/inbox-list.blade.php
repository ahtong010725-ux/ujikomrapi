@php
    $admins = $users->where('role', 'admin');
    $regulars = $users->where('role', '!=', 'admin');
@endphp

@if($admins->count() > 0)
    <div style="padding: 10px 28px; background: rgba(0,0,0,0.03); font-weight: bold; font-size: 14px; border-bottom: 1px solid rgba(0,0,0,0.05); color: #555;">
        🛡️ Admin Support
    </div>
    @foreach($admins as $user)
    <a href="/chat/{{ $user->id }}" class="inbox-item">
        <div class="inbox-avatar" style="background: linear-gradient(135deg, #007bff, #00d2ff);">A</div>
        <div class="inbox-user-info">
            <strong>
                {{ $user->name }}
                <span style="background: #007bff; color: white; padding: 2px 6px; border-radius: 8px; font-size: 10px; margin-left: 5px;">Admin</span>
            </strong>
            @if($user->unread_count > 0)
                <small style="color:#2e7d32;font-weight:600;">Pesan baru</small>
            @else
                <small>Tidak ada pesan baru</small>
            @endif
        </div>
        @if($user->unread_count > 0)
            <div class="unread-badge">{{ $user->unread_count }}</div>
        @endif
    </a>
    @endforeach
@endif

<div style="padding: 10px 28px; background: rgba(0,0,0,0.03); font-weight: bold; font-size: 14px; border-bottom: 1px solid rgba(0,0,0,0.05); {{ $admins->count() > 0 ? 'border-top: 1px solid rgba(0,0,0,0.05);' : '' }} color: #555;">
    📨 Anonymous Inbox
</div>
@forelse($regulars as $user)
    <a href="/chat/{{ $user->id }}" class="inbox-item">
        <div class="inbox-avatar">A</div>
        <div class="inbox-user-info">
            <strong>Anonymous</strong>
            @if($user->unread_count > 0)
                <small style="color:#2e7d32;font-weight:600;">Pesan baru</small>
            @else
                <small>Tidak ada pesan baru</small>
            @endif
        </div>
        @if($user->unread_count > 0)
            <div class="unread-badge">{{ $user->unread_count }}</div>
        @endif
    </a>
@empty
    <div class="empty-state">
        Belum ada percakapan 😴
    </div>
@endforelse