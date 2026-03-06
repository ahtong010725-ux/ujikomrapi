<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

<meta charset="UTF-8">
<title>I FOUND</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
<link rel="stylesheet" href="{{ asset('css/responsive.css') }}">

</head>

<body>

@include('components.navbar')


<section class="hero">

    @guest

        <div class="hero-text">
            <h1>
                Find &<br>
                Recover<br>
                <span class="gradient-text">With Ease</span>
            </h1>
            <p>Experience effortless recovery with our dedicated lost and found service.</p>
        </div>

        <div class="hero-action">
            <a href="{{ route('login') }}" class="btn-lost action-btn">
                Report Lost
                <img src="/images/lost-icon.png">
            </a>

            <a href="{{ route('login') }}" class="btn-found action-btn">
                Report Found
                <img src="/images/found-icon.png">
            </a>
        </div>

    @endguest


    @auth

        <div class="hero-text">
            <h1>
                Welcome Back,<br>
                <span class="gradient-text">
                    {{ auth()->user()->name }}
                </span>
            </h1>
            <p>Ready to manage your lost & found reports?</p>
        </div>

        <div class="hero-action">
            <a href="/lost" class="btn-lost action-btn">
                Report Lost
                <img src="/images/lost-icon.png">
            </a>

            <a href="/found" class="btn-found action-btn">
                Report Found
                <img src="/images/found-icon.png">
            </a>
        </div>

    @endauth

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
        Tel: +94 715260980<br>Email: talkprojects@wewin.com
    </div>
</footer>
<script src="{{ asset('js/home.js') }}"></script>
</body>
</html>
