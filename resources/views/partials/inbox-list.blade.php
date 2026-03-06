@php
    $isAdmin = auth()->check() && auth()->user()->role === 'admin';
    $admins = $users->where('role', 'admin');
    $regulars = $users->where('role', '!=', 'admin');
@endphp

@if($admins->count() > 0)
    <div class="inbox-section-label">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        Admin Support
    </div>
    @foreach($admins as $user)
    <a href="/chat/{{ $user->id }}" class="inbox-item">
        <div class="inbox-avatar admin-avatar">
            @if($user->photo)
                <img src="{{ asset('storage/' . $user->photo) }}" alt="">
            @else
                A
            @endif
        </div>
        <div class="inbox-user-info">
            <strong>
                {{ $user->name }}
                <span class="badge-admin">Admin</span>
            </strong>
            <small class="inbox-preview">
                @if($user->unread_count > 0)
                    <span style="color:#2e7d32;font-weight:600;">{{ $user->unread_count }} pesan baru</span>
                @else
                    {{ $user->last_message_preview ?? 'Tidak ada pesan baru' }}
                @endif
            </small>
        </div>
        <div class="inbox-meta">
            @if($user->last_message_time)
                <small class="inbox-time">{{ \Carbon\Carbon::parse($user->last_message_time)->diffForHumans(null, true, true) }}</small>
            @endif
            @if($user->unread_count > 0)
                <div class="unread-badge">{{ $user->unread_count }}</div>
            @endif
        </div>
    </a>
    @endforeach
@endif

<div class="inbox-section-label">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
    Messages
</div>
@forelse($regulars as $user)
    <a href="/chat/{{ $user->id }}" class="inbox-item">
        <div class="inbox-avatar">
            @if($isAdmin && $user->photo)
                <img src="{{ asset('storage/' . $user->photo) }}" alt="">
            @else
                {{ strtoupper(substr($user->name, 0, 1)) }}
            @endif
        </div>
        <div class="inbox-user-info">
            <strong>{{ $isAdmin ? $user->name : 'Anonymous' }}</strong>
            <small class="inbox-preview">
                @if($user->unread_count > 0)
                    <span style="color:#2e7d32;font-weight:600;">{{ $user->unread_count }} pesan baru</span>
                @else
                    {{ $user->last_message_preview ?? 'Tidak ada pesan baru' }}
                @endif
            </small>
        </div>
        <div class="inbox-meta">
            @if($user->last_message_time)
                <small class="inbox-time">{{ \Carbon\Carbon::parse($user->last_message_time)->diffForHumans(null, true, true) }}</small>
            @endif
            @if($user->unread_count > 0)
                <div class="unread-badge">{{ $user->unread_count }}</div>
            @endif
        </div>
    </a>
@empty
    <div class="empty-state">
        Belum ada percakapan 😴
    </div>
@endforelse