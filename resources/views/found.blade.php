<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Found Items | I FOUND</title>

    <!-- GOOGLE FONT -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    <link rel="stylesheet" href="{{ asset('css/lost.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>
<body>

<!-- NAVBAR -->
@include('components.navbar')

<!-- HERO -->
<section class="lost-hero">
    <h1>Found Items</h1>

    <div class="lost-action" style="display: flex; align-items: center; gap: 10px; width: 100%; max-width: 600px;">
        <form action="/found" method="GET" style="display: flex; width: 100%; gap: 10px;">
            <input type="text" name="search" placeholder="Search item..." value="{{ request('search') }}" style="flex: 1; padding: 10px; border-radius: 8px; border: 1px solid #ccc;">
            <button type="submit" class="report-btn" style="border: none; cursor: pointer; border-radius: 8px; font-weight: bold;">Search</button>
        </form>

        <a href="/report-found" class="report-btn" style="white-space: nowrap;">
            Report
            <img src="/images/report-icon.png" style="margin-left: 5px;">
        </a>
    </div>
</section>

<!-- ITEM LIST -->
<section class="lost-list">

@foreach($items as $item)
<div class="item-card">
    <div class="card-head">
        <div class="avatar">
            <img src="/images/anonymous.jpg" alt="anonymous">
        </div>
        <div>
            <strong>Anonymous</strong>
            <small>{{ $item->created_at->format('d M Y') }}</small>
        </div>
    </div>

    @if($item->photo)
    <div class="card-img">
        <img src="{{ asset('storage/'.$item->photo) }}" alt="">
    </div>
    @endif

    <h4>{{ $item->item_name }} @if($item->status == 'resolved') <span style="color: green; font-size: 0.8em; border: 1px solid green; padding: 2px 6px; border-radius: 12px; margin-left: 5px;">Resolved</span> @endif</h4>
    <p class="location">{{ $item->location }}</p>
    <p class="desc">{{ $item->description }}</p>

    <div class="action-buttons" style="display: flex; gap: 5px; flex-wrap: wrap; margin-top: 10px;">

    @if(auth()->check())

        @if(auth()->id() == $item->user_id)

            <!-- Owner -->
            <a href="/found/{{ $item->id }}/edit" class="contact-btn">
                Edit
            </a>

            <form action="/found/{{ $item->id }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="contact-btn delete-btn">
                    Delete
                </button>
            </form>

            <a href="/inbox" class="contact-btn">
                Inbox
            </a>

            @if($item->status != 'resolved')
            <form action="{{ route('found.status', $item->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="contact-btn" style="background-color: #28a745; color: white;" onclick="return confirm('Tandai sebagai selesai?')">
                    Resolved
                </button>
            </form>
            @endif

        @else

            <!-- User lain -->
            <a href="/chat/{{ $item->user_id }}" class="contact-btn">
                Chat
            </a>
            
            <form action="{{ route('bookmarks.toggle') }}" method="POST">
                @csrf
                <input type="hidden" name="item_id" value="{{ $item->id }}">
                <input type="hidden" name="item_type" value="found">
                @php $isBookmarked = auth()->user()->bookmarks()->where('bookmarkable_id', $item->id)->where('bookmarkable_type', \App\Models\FoundItem::class)->exists(); @endphp
                <button type="submit" class="contact-btn" style="background-color: {{ $isBookmarked ? '#dc3545' : '#ffc107' }}; color: {{ $isBookmarked ? 'white' : 'black' }};">
                    {{ $isBookmarked ? 'Unsave' : 'Save' }}
                </button>
            </form>

        @endif

    @endif

    </div>
</div>
@endforeach
</section>

<!-- FOOTER -->
<footer>
    <div>
        <h4>Site</h4>
        Lost<br>Report Lost<br>Found<br>Report Found
    </div>
    <div>
        <h4>Help</h4>
        Customer Support<br>Terms & Conditions<br>Privacy Policy
    </div>
    <div>
        <h4>Links</h4>
        LinkedIn<br>Facebook<br>YouTube<br>About Us
    </div>
    <div>
        <h4>Contact</h4>
        Tel: +94 716520690<br>
        Email: talkprojects@wenix.com
    </div>
</footer>

<script src="{{ asset('js/lost.js') }}"></script>
<script src="{{ asset('js/home.js') }}"></script>
</body>
</html>
