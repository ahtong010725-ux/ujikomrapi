<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LostItem;
use App\Models\FoundItem;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'users' => User::count(),
            'pending_users' => User::where('registration_status', 'pending')->count(),
            'lost_items' => LostItem::count(),
            'found_items' => FoundItem::count(),
            'resolved_lost' => LostItem::where('status', 'resolved')->count(),
            'resolved_found' => FoundItem::where('status', 'resolved')->count(),
        ];
        return view('admin.dashboard', compact('stats'));
    }

    public function users()
    {
        $users = User::orderByRaw("FIELD(registration_status, 'pending', 'approved', 'rejected')")->latest()->get();
        return view('admin.users', compact('users'));
    }

    public function approveUser($id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'registration_status' => 'approved',
            'rejection_reason' => null
        ]);
        return back()->with('success', 'User berhasil disetujui.');
    }

    public function rejectUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'registration_status' => 'rejected',
            'rejection_reason' => $request->rejection_reason ?? 'Tidak memenuhi syarat.'
        ]);
        return back()->with('success', 'User ditolak.');
    }

    public function destroyUser($id)
    {
        if (auth()->id() == $id) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $user = User::findOrFail($id);
        $user->delete();

        return back()->with('success', 'User berhasil dihapus.');
    }

    public function items()
    {
        $lostItems = LostItem::latest()->get();
        $foundItems = FoundItem::latest()->get();
        return view('admin.items', compact('lostItems', 'foundItems'));
    }

    public function destroyItem($type, $id)
    {
        if ($type === 'lost') {
            LostItem::findOrFail($id)->delete();
        } else {
            FoundItem::findOrFail($id)->delete();
        }

        return back()->with('success', 'Item berhasil dihapus.');
    }
}
