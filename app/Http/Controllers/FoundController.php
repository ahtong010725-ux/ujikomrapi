<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FoundItem;

class FoundController extends Controller
{
    public function index(Request $request)
    {
        $query = FoundItem::latest();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
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
        $request->validate([
            'name' => 'required',
            'item_name' => 'required',
            'location' => 'required',
            'date' => 'required',
            'description' => 'required',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $photoPath = null;

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')
                ->store('found_photos', 'public');
        }

        FoundItem::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'item_name' => $request->item_name,
            'location' => $request->location,
            'date' => $request->date,
            'description' => $request->description,
            'photo' => $photoPath
        ]);

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
            'name' => $request->name,
            'item_name' => $request->item_name,
            'location' => $request->location,
            'date' => $request->date,
            'description' => $request->description,
            'photo' => $photoPath
        ]);

        return redirect('/found')->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        $item = FoundItem::findOrFail($id);

        if ($item->user_id != auth()->id()) {
            abort(403);
        }

        $item->delete();

        return redirect('/found');
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
