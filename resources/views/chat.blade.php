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
            <a href="/inbox" class="back-to-inbox">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5"></path>
                    <path d="M12 19l-7-7 7-7"></path>
                </svg>
            </a>
            Conversations
        </div>

        @foreach($users as $user)
            <a href="/chat/{{ $user->id }}" class="chat-user {{ $user->id == $receiver->id ? 'active-user' : '' }}">
                <div class="chat-user-avatar">
                    @if($user->photo)
                        <img src="{{ asset('storage/' . $user->photo) }}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                    @else
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    @endif
                </div>
                <div class="chat-user-info">
                    <strong>
                        {{ $user->name }}
                        @if($user->role === 'admin')
                            <span class="badge-admin">Admin</span>
                        @endif
                    </strong>
                    <small>
                        @if($user->is_online && $user->last_seen && \Carbon\Carbon::parse($user->last_seen)->diffInMinutes(now()) < 5)
                            <span class="online-dot"></span> Online
                        @elseif($user->last_seen)
                            Last seen {{ \Carbon\Carbon::parse($user->last_seen)->diffForHumans() }}
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
                <a href="/inbox" class="back-btn-mobile">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5"></path>
                        <path d="M12 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div class="chat-header-avatar">
                    @if($receiver->photo)
                        <img src="{{ asset('storage/' . $receiver->photo) }}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                    @else
                        {{ strtoupper(substr($receiver->name, 0, 1)) }}
                    @endif
                </div>
                <div>
                    <strong>
                        {{ $receiver->name }}
                        @if($receiver->role === 'admin')
                            <span class="badge-admin">Admin</span>
                        @endif
                    </strong>
                    <span class="chat-status">
                        @if($receiver->is_online && $receiver->last_seen && \Carbon\Carbon::parse($receiver->last_seen)->diffInMinutes(now()) < 5)
                            <span class="online-dot"></span> Online
                        @elseif($receiver->last_seen)
                            Last seen {{ \Carbon\Carbon::parse($receiver->last_seen)->diffForHumans() }}
                        @else
                            Offline
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <div class="chat-box" id="chatBox">
            @include('partials.chat-messages', ['messages' => $messages])
        </div>

        <!-- Image Preview -->
        <div id="imagePreview" class="image-preview-bar" style="display:none;">
            <img id="previewImg" src="" alt="Preview">
            <button type="button" onclick="cancelImage()" class="cancel-preview">&times;</button>
        </div>

        <div class="chat-input-area">
            <form id="chatForm">
                @csrf
                <label for="imageInput" class="attach-btn" title="Send Photo">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <polyline points="21 15 16 10 5 21"></polyline>
                    </svg>
                </label>
                <input type="file" id="imageInput" accept="image/*" style="display:none;" onchange="previewImage(event)">
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

<!-- Image Modal -->
<div id="imageModal" class="image-modal" onclick="closeImageModal()">
    <img id="modalImage" src="" alt="">
</div>

<script>
window.receiverId = {{ $receiver->id }};
window.csrfToken = "{{ csrf_token() }}";
</script>

<script src="{{ asset('js/chat.js') }}"></script>
<script src="{{ asset('js/home.js') }}"></script>
<script src="{{ asset('js/theme.js') }}"></script>

</body>
</html>
