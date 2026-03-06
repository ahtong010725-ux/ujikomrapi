<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7f6; margin: 0; padding: 0; }
        .sidebar { width: 250px; background: #fff; height: 100vh; position: fixed; padding: 20px; box-shadow: 2px 0 5px rgba(0,0,0,0.05); }
        .sidebar h2 { color: #333; margin-bottom: 20px; font-weight: 700; }
        .sidebar a { display: block; padding: 10px 15px; color: #555; text-decoration: none; border-radius: 8px; margin-bottom: 10px; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: #4a6ee0; color: #fff; }
        .main-content { margin-left: 270px; padding: 40px; }
        .card { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .btn-danger { background: #dc3545; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 8px; color: #fff; background: #28a745; }
        .alert-error { background: #dc3545; }

        /* Dark Mode Overrides */
        body.dark-theme { background: #121212; color: #f1f1f1; }
        body.dark-theme .sidebar { background: #1e1e1e; border-right: 1px solid #333; box-shadow: none; }
        body.dark-theme .sidebar h2, body.dark-theme .sidebar a { color: #ddd; }
        body.dark-theme .sidebar a:hover, body.dark-theme .sidebar a.active { background: #4a6ee0; color: #fff; }
        body.dark-theme .card { background: #1e1e1e; box-shadow: 0 4px 6px rgba(0,0,0,0.3); border: 1px solid #333; }
        body.dark-theme th { background: #2c2c2c; color: #fff; border-bottom: 1px solid #444; }
        body.dark-theme td { border-bottom: 1px solid #333; color: #ddd; }
        body.dark-theme table { color: #f1f1f1; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0;">Admin Panel</h2>
            <button id="theme-toggle" style="background: none; border: none; font-size: 20px; cursor: pointer; padding: 5px;" aria-label="Toggle Dark Mode">🌙</button>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
        <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">Manage Users</a>
        <a href="{{ route('admin.items') }}" class="{{ request()->routeIs('admin.items') ? 'active' : '' }}">Manage Items</a>
        <a href="/" style="margin-top: 50px; border: 1px solid #ddd;">Back to App</a>
    </div>
    <div class="main-content">
        @if(session('success'))
            <div class="alert">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>
    <script src="{{ asset('js/theme.js') }}"></script>
</body>
</html>
