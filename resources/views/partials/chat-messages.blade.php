@foreach($messages as $msg)
    @if($msg->sender_id == auth()->id())
        <div class="chat-message sent">
            @if($msg->image)
                <div class="msg-image">
                    <img src="{{ asset('storage/' . $msg->image) }}" alt="Photo" onclick="openImageModal(this.src)">
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ asset('storage/' . $msg->image) }}" download class="download-btn" title="Download">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        </a>
                    @endif
                </div>
            @endif
            @if($msg->message)
                <div class="msg-text">{{ $msg->message }}</div>
            @endif
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
            @if($msg->image)
                <div class="msg-image">
                    <img src="{{ asset('storage/' . $msg->image) }}" alt="Photo" onclick="openImageModal(this.src)">
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ asset('storage/' . $msg->image) }}" download class="download-btn" title="Download">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        </a>
                    @endif
                </div>
            @endif
            @if($msg->message)
                <div class="msg-text">{{ $msg->message }}</div>
            @endif
            <span class="time">
                {{ $msg->created_at->format('H:i') }}
            </span>
        </div>
    @endif
@endforeach