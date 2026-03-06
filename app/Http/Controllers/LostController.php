<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LostItem;
use App\Models\User;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class LostController extends Controller
{
    public function index(Request $request)
    {
        $query = LostItem::latest();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }
        
        $items = $query->get();
        return view('lost', compact('items'));
    }

    public function create()
    {
        return view('report-lost');
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
                ->store('lost_photos', 'public');
        }

        LostItem::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'item_name' => $request->item_name,
            'location' => $request->location,
            'date' => $request->date,
            'description' => $request->description,
            'photo' => $photoPath
        ]);

        return redirect('/lost')->with('success','Report berhasil dikirim');
    }

    public function edit($id)
    {
        $item = LostItem::findOrFail($id);

        if ($item->user_id != auth()->id()) {
            abort(403);
        }

        return view('edit-lost', compact('item'));
    }

 public function update(Request $request, $id)
{
    $item = LostItem::findOrFail($id);

    if ($item->user_id != auth()->id()) {
        abort(403);
    }

    $photoPath = $item->photo;

    if ($request->hasFile('photo')) {
        $photoPath = $request->file('photo')
            ->store('lost_photos','public');
    }

    $item->update([
        'name' => $request->name,
        'item_name' => $request->item_name,
        'location' => $request->location,
        'date' => $request->date,
        'description' => $request->description,
        'photo' => $photoPath
    ]);

    return redirect('/lost')->with('success','Data berhasil diupdate');
}


    public function destroy($id)
    {
        $item = LostItem::findOrFail($id);

        if ($item->user_id != auth()->id()) {
            abort(403);
        }

        $item->delete();
        


        return redirect('/lost');
    }

    public function updateStatus($id)
    {
        $item = LostItem::findOrFail($id);

        if ($item->user_id != auth()->id()) {
            abort(403);
        }

        $item->update(['status' => 'resolved']);

        return back()->with('success', 'Status berhasil diperbarui menjadi diselesaikan (resolved).');
    }

public function chat($userId)
{
    $receiver = User::findOrFail($userId);
    $loginId = auth()->id();

    // Tandai pesan sebagai sudah dibaca
    Message::where('sender_id', $userId)
        ->where('receiver_id', $loginId)
        ->where('is_read', false)
        ->update(['is_read' => true]);

    // Ambil semua pesan
    $messages = Message::where(function($q) use ($loginId, $userId) {
        $q->where('sender_id', $loginId)
          ->where('receiver_id', $userId);
    })
    ->orWhere(function($q) use ($loginId, $userId) {
        $q->where('sender_id', $userId)
          ->where('receiver_id', $loginId);
    })
    ->orderBy('created_at','asc')
    ->get();

    // Ambil user untuk sidebar
    $chatUserIds = Message::where('sender_id', $loginId)
        ->orWhere('receiver_id', $loginId)
        ->get()
        ->flatMap(function($msg){
            return [$msg->sender_id, $msg->receiver_id];
        })
        ->unique()
        ->filter(fn($id)=>$id != $loginId);

    $users = User::whereIn('id', $chatUserIds)->get();

    return view('chat', compact('receiver','messages','users'));
}


public function sendMessage(Request $request, $userId)
{
    $request->validate([
        'message' => 'required'
    ]);

    Message::create([
        'sender_id' => auth()->id(),
        'receiver_id' => $userId,
        'message' => $request->message,
        'is_read' => false
    ]);

    return response()->json(['success'=>true]);
}

public function fetchMessages($userId)
{
    $loginId = auth()->id();

    $messages = Message::where(function($q) use ($loginId, $userId) {
        $q->where('sender_id', $loginId)
          ->where('receiver_id', $userId);
    })
    ->orWhere(function($q) use ($loginId, $userId) {
        $q->where('sender_id', $userId)
          ->where('receiver_id', $loginId);
    })
    ->orderBy('created_at','asc')
    ->get();

    return view('partials.chat-messages', compact('messages'));
}

public function inbox()
{
    $loginId = auth()->id();

    $chatUserIds = Message::where('sender_id', $loginId)
        ->orWhere('receiver_id', $loginId)
        ->get()
        ->flatMap(function($msg) use ($loginId){
            return $msg->sender_id == $loginId
                ? [$msg->receiver_id]
                : [$msg->sender_id];
        })
        ->unique();

    $users = User::whereIn('id', $chatUserIds)
        ->get()
        ->map(function($user) use ($loginId){

            $user->unread_count = Message::where('sender_id', $user->id)
                ->where('receiver_id', $loginId)
                ->where('is_read', false)
                ->count();

            $user->last_message_time = Message::where(function($q) use ($loginId, $user){
                $q->where('sender_id', $loginId)
                  ->where('receiver_id', $user->id);
            })->orWhere(function($q) use ($loginId, $user){
                $q->where('sender_id', $user->id)
                  ->where('receiver_id', $loginId);
            })->latest()->value('created_at');

            return $user;
        })
        ->sortByDesc('last_message_time');

    return view('inbox', compact('users'));
}

public function fetchInbox()
{
    $loginId = auth()->id();

    $chatUserIds = Message::where('sender_id', $loginId)
        ->orWhere('receiver_id', $loginId)
        ->get()
        ->flatMap(function($msg) use ($loginId){
            return $msg->sender_id == $loginId
                ? [$msg->receiver_id]
                : [$msg->sender_id];
        })
        ->unique();

    $users = User::whereIn('id', $chatUserIds)
        ->get()
        ->map(function($user) use ($loginId){

            $user->unread_count = Message::where('sender_id', $user->id)
                ->where('receiver_id', $loginId)
                ->where('is_read', false)
                ->count();

            $user->last_message_time = Message::where(function($q) use ($loginId, $user){
                $q->where('sender_id', $loginId)
                  ->where('receiver_id', $user->id);
            })->orWhere(function($q) use ($loginId, $user){
                $q->where('sender_id', $user->id)
                  ->where('receiver_id', $loginId);
            })->latest()->value('created_at');

            return $user;
        })
        ->sortByDesc('last_message_time');

    return view('partials.inbox-list', compact('users'));
}

}
