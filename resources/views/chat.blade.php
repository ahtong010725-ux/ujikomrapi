<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat | I FOUND</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/chat.css') }}">
</head>
<body>

@include('components.navbar')

<div class="chat-wrapper">
<div class="chat-layout">

    <!-- SIDEBAR -->
    <div class="chat-sidebar">
        <div class="sidebar-header">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle; margin-right:8px;">
                <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"></path>
            </svg>
            Conversations
        </div>

        @foreach($users as $user)
            <a href="/chat/{{ $user->id }}" class="chat-user {{ $user->id == $receiver->id ? 'active-user' : '' }}">
                <div class="chat-user-avatar">A</div>
                <div class="chat-user-info">
                    <strong>
                        {{ $user->name }}
                        @if($user->role === 'admin')
                            <span style="background: #007bff; color: white; padding: 2px 6px; border-radius: 8px; font-size: 10px; margin-left: 5px;">Admin</span>
                        @endif
                    </strong>
                    <small>
                        @if($user->is_online)
                            <span class="online-dot"></span> Online
                        @elseif($user->last_seen)
                            Last seen {{ \Carbon\Carbon::parse($user->last_seen)->format('d M H:i') }}
                        @else
                            Offline
                        @endif
                    </small>
                </div>
            </a>
        @endforeach
    </div>

    <!-- MAIN -->
    <div class="chat-main">

        <div class="chat-header">
            <div class="chat-header-info">
                <div class="chat-header-avatar">A</div>
                <div>
                    <strong>
                        {{ $receiver->name }}
                        @if($receiver->role === 'admin')
                            <span style="background: #007bff; color: white; padding: 2px 6px; border-radius: 8px; font-size: 10px; margin-left: 5px;">Admin</span>
                        @endif
                    </strong>
                    <span class="chat-status">
                        @if($receiver->is_online)
                            <span class="online-dot"></span> Online
                        @elseif($receiver->last_seen)
                            Last seen {{ \Carbon\Carbon::parse($receiver->last_seen)->format('d M H:i') }}
                        @else
                            Offline
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <div class="chat-box" id="chatBox">
            @foreach($messages as $msg)
                @if($msg->sender_id == auth()->id())
                    <div class="chat-message sent">
                        <div class="msg-text">{{ $msg->message }}</div>
                        <span class="time">
                            {{ $msg->created_at->format('H:i') }}
                            @if($msg->is_read)
                                <span style="color:#34b7f1;">✔✔</span>
                            @else
                                ✔
                            @endif
                        </span>
                    </div>
                @else
                    <div class="chat-message received">
                        <div class="msg-text">{{ $msg->message }}</div>
                        <span class="time">
                            {{ $msg->created_at->format('H:i') }}
                        </span>
                    </div>
                @endif
            @endforeach
        </div>

        <div class="chat-input-area">
            <form id="chatForm">
                @csrf
                <input type="text" name="message" id="messageInput" placeholder="Type a message..." autocomplete="off">
                <button type="submit">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 2L11 13"></path>
                        <path d="M22 2L15 22L11 13L2 9L22 2Z"></path>
                    </svg>
                </button>
            </form>
        </div>

    </div>
</div>
</div>

<script>
window.receiverId = {{ $receiver->id }};
window.csrfToken = "{{ csrf_token() }}";
</script>

<script src="{{ asset('js/chat.js') }}"></script>
<script src="{{ asset('js/home.js') }}"></script>

</body>
</html>
