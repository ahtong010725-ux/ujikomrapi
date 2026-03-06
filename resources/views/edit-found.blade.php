<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Found Item | I FOUND</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
<link rel="stylesheet" href="{{ asset('css/reportlost.css') }}">
</head>
<body>

@include('components.navbar')

<a href="/found" class="back-btn">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M19 12H5"></path>
        <path d="M12 19l-7-7 7-7"></path>
    </svg>
    Back
</a>

<section class="report-container fade-in">

    <h1 class="title">
        Edit <span>Found Item</span>
    </h1>

    <form class="report-box" method="POST" action="/found/{{ $item->id }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-row">
            <label>Your Name</label>
            <input type="text" name="name" value="{{ $item->name }}" placeholder="Enter your name">
        </div>

        <div class="form-row">
            <label>Item Name</label>
            <input type="text" name="item_name" value="{{ $item->item_name }}" placeholder="Item name">
        </div>

        <div class="form-row">
            <label>Location</label>
            <input type="text" name="location" value="{{ $item->location }}" placeholder="Location">
        </div>

        <div class="form-row">
            <label>Date</label>
            <input type="date" name="date" value="{{ $item->date }}">
        </div>

        <div class="form-row">
            <label>Description</label>
            <textarea name="description" placeholder="Describe the item...">{{ $item->description }}</textarea>
        </div>

        <div class="form-row">
            <label>Change Photo</label>
            <input type="file" name="photo" accept="image/*">
        </div>

        @if($item->photo)
            <div style="margin-bottom: 20px;">
                <p style="font-size:13px; color:#666; margin-bottom:8px;">Current Photo:</p>
                <img src="{{ asset('storage/'.$item->photo) }}" width="200" style="border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.08);">
            </div>
        @endif

        <div class="actions">
            <button type="submit" class="submit-btn">Update Item</button>
        </div>

    </form>

</section>

<footer>
    <div><h4>Site</h4>Lost<br>Report Lost<br>Found<br>Report Found</div>
    <div><h4>Help</h4>Customer Support<br>Terms & Conditions<br>Privacy Policy</div>
    <div><h4>Links</h4>LinkedIn<br>Facebook<br>YouTube<br>About Us</div>
    <div><h4>Contact</h4>Tel: +94 716520690<br>Email: talkprojects@wenix.com</div>
</footer>

<script src="{{ asset('js/home.js') }}"></script>
</body>
</html>
