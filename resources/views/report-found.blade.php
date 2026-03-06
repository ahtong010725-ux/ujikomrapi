<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Report Found Item | I FOUND</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
<link rel="stylesheet" href="{{ asset('css/reportlost.css') }}">
</head>

<body>

@include('components.navbar')

<!-- BACK BUTTON -->
<a href="/found" class="back-btn">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M19 12H5"></path>
        <path d="M12 19l-7-7 7-7"></path>
    </svg>
    Back
</a>

<!-- MAIN -->
<section class="report-container fade-in">

    <h1 class="title">
        Report <span>Found Item</span>
    </h1>

<form class="report-box" method="POST" action="/report-found" enctype="multipart/form-data">
    @csrf

    <div class="form-row">
        <label>Your Name</label>
        <input type="text" name="name" value="{{ old('name') }}" placeholder="Enter your name">
        @error('name')
            <small style="color:#e53935; font-size:12px; margin-top:4px; display:block;">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-row">
        <label>Item Name</label>
        <input type="text" name="item_name" value="{{ old('item_name') }}" placeholder="What did you find?">
        @error('item_name')
            <small style="color:#e53935; font-size:12px; margin-top:4px; display:block;">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-row">
        <label>Location</label>
        <input type="text" name="location" value="{{ old('location') }}" placeholder="Where did you find it?">
        @error('location')
            <small style="color:#e53935; font-size:12px; margin-top:4px; display:block;">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-row">
        <label>Date</label>
        <input type="date" name="date" value="{{ old('date') }}">
        @error('date')
            <small style="color:#e53935; font-size:12px; margin-top:4px; display:block;">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-row">
        <label>Description</label>
        <textarea name="description" placeholder="Describe the item in detail...">{{ old('description') }}</textarea>
        @error('description')
            <small style="color:#e53935; font-size:12px; margin-top:4px; display:block;">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-row">
        <label>Upload Photo</label>
        <input type="file" name="photo" accept="image/*">
        @error('photo')
            <small style="color:#e53935; font-size:12px; margin-top:4px; display:block;">{{ $message }}</small>
        @enderror
    </div>

    <div class="actions">
        <button type="submit" class="submit-btn">Submit Report</button>
        <button type="reset" class="reset-btn">Reset</button>
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
<script src="{{ asset('js/theme.js') }}"></script>
</body>
</html>
