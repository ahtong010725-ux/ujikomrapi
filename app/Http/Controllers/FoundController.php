<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FoundItem;

class FoundController extends Controller
{
    public function index(Request $request)
    {
        $query = FoundItem::with('user')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('brand_name', 'like', "%{$search}%");
            });
        }

        $items = $query->get();
        return view('found', compact('items'));
    }

    public function create()
    {
        return view('report-found');
    }

    public function store(Request $request)
    {
        // Anti-cheat: Max 5 found posts per day
        $todayPosts = FoundItem::where('user_id', auth()->id())
            ->whereDate('created_at', today())
            ->count();

        if ($todayPosts >= 5) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => 'Kamu sudah mencapai batas posting hari ini (max 5/hari). Coba lagi besok.'], 429);
            }
            return back()->with('error', 'Kamu sudah mencapai batas posting hari ini (max 5/hari). Coba lagi besok.');
        }

        $request->validate([
            'brand_name' => 'nullable',
            'item_name' => 'required',
            'item_type' => 'nullable',
            'location' => 'required',
            'date' => 'required',
            'description' => 'required',
            'reward_offered' => 'nullable',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $photoPath = null;

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')
                ->store('found_photos', 'public');
        }

        FoundItem::create([
            'user_id' => auth()->id(),
            'name' => auth()->user()->name,
            'brand_name' => $request->brand_name,
            'item_name' => $request->item_name,
            'item_type' => $request->item_type,
            'location' => $request->location,
            'date' => $request->date,
            'description' => $request->description,
            'reward_offered' => $request->reward_offered ? preg_replace('/[^0-9]/', '', $request->reward_offered) : null,
            'photo' => $photoPath
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Report berhasil dikirim']);
        }

        return redirect('/found')->with('success', 'Report berhasil dikirim');
    }

    public function edit($id)
    {
        $item = FoundItem::findOrFail($id);

        if ($item->user_id != auth()->id()) {
            abort(403);
        }

        return view('edit-found', compact('item'));
    }

    public function getItemJson($id)
    {
        $item = FoundItem::findOrFail($id);

        if ($item->user_id != auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($item);
    }

    public function update(Request $request, $id)
    {
        $item = FoundItem::findOrFail($id);

        if ($item->user_id != auth()->id()) {
            abort(403);
        }

        $photoPath = $item->photo;

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')
                ->store('found_photos', 'public');
        }

        $item->update([
            'brand_name' => $request->brand_name,
            'item_name' => $request->item_name,
            'item_type' => $request->item_type,
            'location' => $request->location,
            'date' => $request->date,
            'description' => $request->description,
            'reward_offered' => $request->reward_offered ? preg_replace('/[^0-9]/', '', $request->reward_offered) : null,
            'photo' => $photoPath
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Data berhasil diupdate']);
        }

        return redirect('/found')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        $item = FoundItem::findOrFail($id);

        if ($item->user_id != auth()->id()) {
            abort(403);
        }

        $item->delete();

        return redirect('/found')->with('success', 'Item berhasil dihapus.');
    }

    public function updateStatus($id)
    {
        $item = FoundItem::findOrFail($id);

        if ($item->user_id != auth()->id()) {
            abort(403);
        }

        $item->update(['status' => 'resolved']);

        return back()->with('success', 'Status berhasil diperbarui menjadi diselesaikan (resolved).');
    }
}
