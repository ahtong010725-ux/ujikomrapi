<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bookmark;
use App\Models\LostItem;
use App\Models\FoundItem;

class BookmarkController extends Controller
{
    public function index()
    {
        $bookmarks = auth()->user()->bookmarks()->with('bookmarkable')->latest()->get();
        return view('bookmarks.index', compact('bookmarks'));
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer',
            'item_type' => 'required|in:lost,found'
        ]);

        $modelClass = $request->item_type === 'lost' ? LostItem::class : FoundItem::class;
        $item = $modelClass::findOrFail($request->item_id);

        $existing = Bookmark::where('user_id', auth()->id())
            ->where('bookmarkable_id', $item->id)
            ->where('bookmarkable_type', $modelClass)
            ->first();

        if ($existing) {
            $existing->delete();
            return back()->with('success', 'Dihapus dari bookmarks.');
        }

        Bookmark::create([
            'user_id' => auth()->id(),
            'bookmarkable_id' => $item->id,
            'bookmarkable_type' => $modelClass,
        ]);

        return back()->with('success', 'Ditambahkan ke bookmarks.');
    }
}
