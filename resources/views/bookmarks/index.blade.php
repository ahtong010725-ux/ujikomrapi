<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>My Bookmarks | I FOUND</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/lost.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">  
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
</head>
<body>

@include('components.navbar')

<section class="lost-hero">
    <h1>My Bookmarks</h1>
    <p style="color: white;">Barang-barang yang kamu simpan (simpanan item hilang & ditemukan).</p>
</section>

<section class="lost-list">
    @if($bookmarks->isEmpty())
        <p style="text-align: center; width: 100%; font-size: 1.2em; color: gray;">Belum ada item yang disimpan.</p>
    @endif

    @foreach($bookmarks as $bookmark)
        @php 
            $item = $bookmark->bookmarkable; 
            if(!$item) continue;
            $type = $bookmark->bookmarkable_type === \App\Models\LostItem::class ? 'lost' : 'found';
        @endphp
        <div class="item-card" style="border-top: 4px solid {{ $type === 'lost' ? '#ff4d4d' : '#28a745' }};">
            <div style="background: {{ $type === 'lost' ? '#ff4d4d' : '#28a745' }}; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px; display: inline-block; margin-bottom: 5px;">{{ strtoupper($type) }} ITEM</div>
            
            <div class="card-head" style="margin-top: 5px;">
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
                <a href="/{{ $type }}" class="contact-btn">Lihat di {{ ucfirst($type) }}</a>
                
                <form action="{{ route('bookmarks.toggle') }}" method="POST">
                    @csrf
                    <input type="hidden" name="item_id" value="{{ $item->id }}">
                    <input type="hidden" name="item_type" value="{{ $type }}">
                    <button type="submit" class="contact-btn" style="background-color: #dc3545; color: white;">
                        Unsave
                    </button>
                </form>
            </div>
        </div>
    @endforeach
</section>

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

<script src="{{ asset('js/home.js') }}"></script>
</body>
</html>
