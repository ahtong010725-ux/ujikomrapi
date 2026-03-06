<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css'])

    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
</head>
<body>

<div class="profile-page">
    @yield('content')
</div>

</body>
</html>
