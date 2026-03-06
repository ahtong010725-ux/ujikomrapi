@extends('admin.layout')

@section('content')
    <h1 style="margin-top:0;">Manage Items</h1>
    
    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
        <div class="card" style="flex: 1; min-width: 300px; overflow-x: auto;">
            <h3 style="margin-top:0; color: #dc3545;">Lost Items</h3>
            <table>
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>User</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lostItems as $item)
                    <tr>
                        <td>{{ $item->item_name }}</td>
                        <td>{{ $item->user->name ?? 'Unknown' }}</td>
                        <td>{{ ucfirst($item->status) }}</td>
                        <td>
                            <form action="{{ route('admin.items.destroy', ['type' => 'lost', 'id' => $item->id]) }}" method="POST" onsubmit="return confirm('Yakin hapus postingan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="card" style="flex: 1; min-width: 300px; overflow-x: auto;">
            <h3 style="margin-top:0; color: #28a745;">Found Items</h3>
            <table>
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>User</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($foundItems as $item)
                    <tr>
                        <td>{{ $item->item_name }}</td>
                        <td>{{ $item->user->name ?? 'Unknown' }}</td>
                        <td>{{ ucfirst($item->status) }}</td>
                        <td>
                            <form action="{{ route('admin.items.destroy', ['type' => 'found', 'id' => $item->id]) }}" method="POST" onsubmit="return confirm('Yakin hapus postingan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
