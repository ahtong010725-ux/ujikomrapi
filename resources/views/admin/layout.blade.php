<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | I FOUND</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #fdfbfb 0%, #e2ebf0 100%);
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(20px);
            height: 100vh;
            position: fixed;
            padding: 28px 20px;
            border-right: 1px solid rgba(0,0,0,0.06);
            display: flex;
            flex-direction: column;
            z-index: 10;
        }

        .sidebar-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 0 4px;
        }

        .sidebar-top h2 {
            font-size: 20px;
            color: #222;
            background: linear-gradient(135deg, #2e7d32, #43a047);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: #555;
            text-decoration: none;
            border-radius: 12px;
            margin-bottom: 6px;
            transition: all 0.25s ease;
            font-size: 14px;
            font-weight: 500;
        }

        .sidebar a:hover {
            background: rgba(46,125,50,0.08);
            color: #2e7d32;
        }

        .sidebar a.active {
            background: linear-gradient(135deg, #2e7d32, #43a047);
            color: #fff;
            box-shadow: 0 4px 12px rgba(46,125,50,0.2);
        }

        .sidebar-bottom {
            margin-top: auto;
        }

        .sidebar-bottom a {
            color: #888;
            border: 1px solid rgba(0,0,0,0.08);
        }

        .sidebar-bottom a:hover {
            background: rgba(0,0,0,0.04);
            color: #333;
        }

        /* Main */
        .main-content {
            margin-left: 260px;
            padding: 36px 40px;
            min-height: 100vh;
        }

        /* Cards */
        .card {
            background: rgba(255,255,255,0.7);
            backdrop-filter: blur(15px);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.04);
            border: 1px solid rgba(255,255,255,0.6);
        }

        /* Status badges */
        .status-badge {
            color: white;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }

        /* Buttons */
        .btn-danger {
            background: #e53935;
            color: white;
            border: none;
            padding: 7px 14px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-danger:hover { background: #c62828; transform: translateY(-1px); }

        .btn-approve {
            background: linear-gradient(135deg, #2e7d32, #43a047);
            color: white;
            border: none;
            padding: 7px 14px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-approve:hover { box-shadow: 0 4px 12px rgba(46,125,50,0.25); transform: translateY(-1px); }

        .btn-reject {
            background: #ffc107;
            color: black;
            border: none;
            padding: 7px 14px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-reject:hover { background: #ffb300; transform: translateY(-1px); }

        /* Table */
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 14px 12px; text-align: left; border-bottom: 1px solid rgba(0,0,0,0.05); font-size: 13px; }
        th { background: rgba(0,0,0,0.02); font-weight: 600; color: #555; text-transform: uppercase; font-size: 11px; letter-spacing: 0.5px; }
        tr:hover { background: rgba(46,125,50,0.02); }

        /* Alert */
        .alert {
            padding: 14px 20px;
            margin-bottom: 20px;
            border-radius: 12px;
            color: #fff;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-success { background: linear-gradient(135deg, #2e7d32, #43a047); }
        .alert-error { background: linear-gradient(135deg, #c62828, #e53935); }

        /* Dark Mode */
        body.dark-theme { background: linear-gradient(135deg, #0d0d0d 0%, #1a1a24 100%); color: #f1f1f1; }
        body.dark-theme .sidebar { background: rgba(20,20,20,0.85); border-right: 1px solid rgba(255,255,255,0.06); }
        body.dark-theme .sidebar-top h2 { color: #66bb6a; -webkit-text-fill-color: #66bb6a; }
        body.dark-theme .sidebar a { color: #aaa; }
        body.dark-theme .sidebar a:hover { background: rgba(255,255,255,0.05); color: #fff; }
        body.dark-theme .sidebar a.active { background: linear-gradient(135deg, #2e7d32, #43a047); color: #fff; }
        body.dark-theme .sidebar-bottom a { color: #888; border-color: rgba(255,255,255,0.1); }
        body.dark-theme .card { background: rgba(25,25,25,0.7); border: 1px solid rgba(255,255,255,0.08); box-shadow: 0 4px 16px rgba(0,0,0,0.3); }
        body.dark-theme th { background: rgba(255,255,255,0.03); color: #aaa; }
        body.dark-theme td { border-bottom-color: rgba(255,255,255,0.06); color: #ddd; }
        body.dark-theme tr:hover { background: rgba(255,255,255,0.02); }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { width: 100%; height: auto; position: relative; flex-direction: row; flex-wrap: wrap; padding: 16px; gap: 6px; }
            .sidebar-top { width: 100%; margin-bottom: 10px; }
            .sidebar a { padding: 8px 12px; font-size: 13px; margin: 0; }
            .sidebar-bottom { margin-top: 0; }
            .main-content { margin-left: 0; padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-top">
            <h2>I FOUND</h2>
            <button id="theme-toggle" style="background: none; border: none; font-size: 18px; cursor: pointer; padding: 5px;" aria-label="Toggle Dark Mode">🌙</button>
        </div>

        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            Dashboard
        </a>
        <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
            Manage Users
        </a>
        <a href="{{ route('admin.items') }}" class="{{ request()->routeIs('admin.items') ? 'active' : '' }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>
            Manage Items
        </a>

        <div class="sidebar-bottom">
            <a href="/">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
                Back to App
            </a>
        </div>
    </div>

    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </div>
    <script src="{{ asset('js/theme.js') }}"></script>
</body>
</html>
