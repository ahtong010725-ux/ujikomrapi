<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

<nav class="navbar">
  <h2>I FOUND</h2>
  <div>
    <a href="/">Home</a>
    <a href="/lost">Lost</a>
    <a href="/found">Found</a>
    <a href="/login">Login</a>
  </div>
</nav>

<div class="page">
@yield('content')
</div>

<footer class="footer">
  © 2026 I FOUND
</footer>

</body>
</html>
