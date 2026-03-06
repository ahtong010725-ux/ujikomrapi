@foreach($messages as $msg)
<div style="display:flex; margin-bottom:10px; 
    justify-content: {{ $msg->sender_id == auth()->id() ? 'flex-end' : 'flex-start' }};">

    <div style="
        max-width:65%;
        padding:10px 14px;
        border-radius:15px;
        background-color: {{ $msg->sender_id == auth()->id() ? '#dcf8c6' : '#ffffff' }};
        box-shadow:0 2px 5px rgba(0,0,0,0.1);
        position:relative;
    ">

        <div style="font-size:14px;">
            {{ $msg->message }}
        </div>

        <div style="font-size:11px; text-align:right; margin-top:4px; color:gray;">
            {{ $msg->created_at->format('H:i') }}

            @if($msg->sender_id == auth()->id())
                @if($msg->is_read)
                    <span style="color:#34b7f1;">✔✔</span>
                @else
                    ✔✔
                @endif
            @endif
        </div>

    </div>
</div>
@endforeach