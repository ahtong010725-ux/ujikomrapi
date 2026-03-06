@extends('admin.layout')

@section('content')
    <h1 style="margin-top:0;">Dashboard Overview</h1>
    <div style="display: flex; gap: 20px; margin-top: 30px; flex-wrap: wrap;">
        <div class="card" style="flex: 1; min-width: 200px; text-align: center;">
            <h3>Total Users</h3>
            <h1 style="color: #4a6ee0; font-size: 3em; margin: 10px 0;">{{ $stats['users'] }}</h1>
        </div>
        <div class="card" style="flex: 1; min-width: 200px; text-align: center;">
            <h3>Lost Items</h3>
            <h1 style="color: #dc3545; font-size: 3em; margin: 10px 0;">{{ $stats['lost_items'] }}</h1>
            <small>{{ $stats['resolved_lost'] }} Resolved</small>
        </div>
        <div class="card" style="flex: 1; min-width: 200px; text-align: center;">
            <h3>Found Items</h3>
            <h1 style="color: #28a745; font-size: 3em; margin: 10px 0;">{{ $stats['found_items'] }}</h1>
            <small>{{ $stats['resolved_found'] }} Resolved</small>
        </div>
    </div>
@endsection
