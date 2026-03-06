@extends('admin.layout')

@section('content')
    <h1 style="margin-top:0; font-size: 24px; color: #222; margin-bottom: 24px;">Dashboard Overview</h1>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">

        <div class="card" style="text-align: center; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -10px; right: -10px; width: 70px; height: 70px; background: rgba(46,125,50,0.06); border-radius: 50%;"></div>
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#2e7d32" stroke-width="2" style="margin-bottom: 8px;"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
            <h3 style="font-size: 13px; color: #888; font-weight: 500; margin-bottom: 4px;">Total Users</h3>
            <h1 style="color: #2e7d32; font-size: 2.5em; margin: 4px 0; font-weight: 700;">{{ $stats['users'] }}</h1>
            @if($stats['pending_users'] > 0)
                <span class="status-badge" style="background: #ffc107; color: black; font-size: 11px;">{{ $stats['pending_users'] }} pending</span>
            @endif
        </div>

        <div class="card" style="text-align: center; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -10px; right: -10px; width: 70px; height: 70px; background: rgba(229,57,53,0.06); border-radius: 50%;"></div>
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#e53935" stroke-width="2" style="margin-bottom: 8px;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <h3 style="font-size: 13px; color: #888; font-weight: 500; margin-bottom: 4px;">Lost Items</h3>
            <h1 style="color: #e53935; font-size: 2.5em; margin: 4px 0; font-weight: 700;">{{ $stats['lost_items'] }}</h1>
            <small style="color: #999;">{{ $stats['resolved_lost'] }} Resolved</small>
        </div>

        <div class="card" style="text-align: center; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -10px; right: -10px; width: 70px; height: 70px; background: rgba(46,125,50,0.06); border-radius: 50%;"></div>
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#43a047" stroke-width="2" style="margin-bottom: 8px;"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <h3 style="font-size: 13px; color: #888; font-weight: 500; margin-bottom: 4px;">Found Items</h3>
            <h1 style="color: #43a047; font-size: 2.5em; margin: 4px 0; font-weight: 700;">{{ $stats['found_items'] }}</h1>
            <small style="color: #999;">{{ $stats['resolved_found'] }} Resolved</small>
        </div>

    </div>
@endsection
