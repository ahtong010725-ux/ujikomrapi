@foreach($messages as $msg)

    @if($msg->sender_id == auth()->id())
        <div class="chat-message sent">
            {{ $msg->message }}
            <span class="time">
                {{ $msg->created_at->format('H:i') }}
            </span>
        </div>
    @else
        <div class="chat-message received">
            {{ $msg->message }}
            <span class="time">
                {{ $msg->created_at->format('H:i') }}
            </span>
        </div>
    @endif

@endforeach