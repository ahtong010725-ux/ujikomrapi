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
        $query = LostItem::with('user')->latest();

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
        return view('lost', compact('items'));
    }

    public function create()
    {
        return view('report-lost');
    }

    public function store(Request $request)
    {
        // Soft ban check
        if (auth()->user()->isSoftBanned()) {
            return back()->with('error', '⚠️ Akun kamu sedang di-soft ban. Kamu tidak bisa membuat laporan. Alasan: ' . (auth()->user()->ban_reason ?? 'Tidak ada alasan.'));
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
                ->store('lost_photos', 'public');
        }

        LostItem::create([
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

    public function getItemJson($id)
    {
        $item = LostItem::findOrFail($id);

        if ($item->user_id != auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($item);
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

        return redirect('/lost')->with('success','Data berhasil diupdate');
    }

    public function destroy($id)
    {
        $item = LostItem::findOrFail($id);

        if ($item->user_id != auth()->id()) {
            abort(403);
        }

        $item->delete();

        return redirect('/lost')->with('success', 'Item berhasil dihapus.');
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

        // Mark messages as read
        Message::where('sender_id', $userId)
            ->where('receiver_id', $loginId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Get all messages
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

        // Get users for sidebar - include all chat partners + always include admins
        $chatUserIds = Message::where('sender_id', $loginId)
            ->orWhere('receiver_id', $loginId)
            ->get()
            ->flatMap(function($msg){
                return [$msg->sender_id, $msg->receiver_id];
            })
            ->unique()
            ->filter(fn($id)=>$id != $loginId);

        // Always include admin users
        $adminIds = User::where('role', 'admin')->pluck('id');
        $allIds = $chatUserIds->merge($adminIds)->unique();

        $users = User::whereIn('id', $allIds)->get();

        return view('chat', compact('receiver','messages','users'));
    }

    public function sendMessage(Request $request, $userId)
    {
        // Soft ban check
        if (auth()->user()->isSoftBanned()) {
            return response()->json(['error' => 'Akun kamu sedang di-soft ban. Kamu tidak bisa mengirim pesan.'], 403);
        }

        $request->validate([
            'message' => 'nullable|required_without:image',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:5120'
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('chat_photos', 'public');
        }

        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $userId,
            'message' => $request->message ?? '',
            'image' => $imagePath,
            'is_read' => false
        ]);

        return response()->json(['success' => true]);
    }

    public function fetchMessages($userId)
    {
        $loginId = auth()->id();

        // Mark messages as read
        Message::where('sender_id', $userId)
            ->where('receiver_id', $loginId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

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

    public function fetchUserStatus($userId)
    {
        $loginId = auth()->id();
        $receiver = User::find($userId);

        if (!$receiver) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Build receiver status HTML
        $receiverHtml = $this->buildStatusHtml($receiver);

        // Get sidebar user statuses
        $chatUserIds = Message::where('sender_id', $loginId)
            ->orWhere('receiver_id', $loginId)
            ->get()
            ->flatMap(function($msg){
                return [$msg->sender_id, $msg->receiver_id];
            })
            ->unique()
            ->filter(fn($id)=>$id != $loginId);

        $adminIds = User::where('role', 'admin')->pluck('id');
        $allIds = $chatUserIds->merge($adminIds)->unique();

        $users = User::whereIn('id', $allIds)->get();

        $sidebar = $users->map(function($user) {
            return [
                'id' => $user->id,
                'html' => $this->buildStatusHtml($user),
            ];
        })->values();

        return response()->json([
            'receiver' => [
                'id' => $receiver->id,
                'html' => $receiverHtml,
            ],
            'sidebar' => $sidebar,
        ]);
    }

    private function buildStatusHtml($user)
    {
        if ($user->is_online && $user->last_seen && Carbon::parse($user->last_seen)->diffInMinutes(now()) < 5) {
            return '<span class="online-dot"></span> Online';
        } elseif ($user->last_seen) {
            return 'Last seen ' . Carbon::parse($user->last_seen)->diffForHumans();
        } else {
            return 'Offline';
        }
    }

    public function inbox(Request $request)
    {
        $loginId = auth()->id();
        $isAdmin = auth()->user()->role === 'admin';

        if ($isAdmin) {
            // Admin sees ALL users
            $allIds = User::where('id', '!=', $loginId)->pluck('id');
        } else {
            $chatUserIds = Message::where('sender_id', $loginId)
                ->orWhere('receiver_id', $loginId)
                ->get()
                ->flatMap(function($msg) use ($loginId){
                    return $msg->sender_id == $loginId
                        ? [$msg->receiver_id]
                        : [$msg->sender_id];
                })
                ->unique();

            // Always include admin users even without messages
            $adminIds = User::where('role', 'admin')->pluck('id');
            $allIds = $chatUserIds->merge($adminIds)->unique()->filter(fn($id) => $id != $loginId);
        }

        $users = User::whereIn('id', $allIds)
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

                // Get last message preview
                $lastMsg = Message::where(function($q) use ($loginId, $user){
                    $q->where('sender_id', $loginId)
                      ->where('receiver_id', $user->id);
                })->orWhere(function($q) use ($loginId, $user){
                    $q->where('sender_id', $user->id)
                      ->where('receiver_id', $loginId);
                })->latest()->first();

                $user->last_message_preview = $lastMsg
                    ? ($lastMsg->image ? '📷 Photo' : \Illuminate\Support\Str::limit($lastMsg->message, 30))
                    : 'Belum ada pesan';

                return $user;
            })
            ->sortByDesc('last_message_time');

        return view('inbox', compact('users'));
    }

    public function fetchInbox(Request $request)
    {
        $loginId = auth()->id();
        $isAdmin = auth()->user()->role === 'admin';

        if ($isAdmin) {
            $allIds = User::where('id', '!=', $loginId)->pluck('id');
        } else {
            $chatUserIds = Message::where('sender_id', $loginId)
                ->orWhere('receiver_id', $loginId)
                ->get()
                ->flatMap(function($msg) use ($loginId){
                    return $msg->sender_id == $loginId
                        ? [$msg->receiver_id]
                        : [$msg->sender_id];
                })
                ->unique();

            $adminIds = User::where('role', 'admin')->pluck('id');
            $allIds = $chatUserIds->merge($adminIds)->unique()->filter(fn($id) => $id != $loginId);
        }

        $users = User::whereIn('id', $allIds)
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

                $lastMsg = Message::where(function($q) use ($loginId, $user){
                    $q->where('sender_id', $loginId)
                      ->where('receiver_id', $user->id);
                })->orWhere(function($q) use ($loginId, $user){
                    $q->where('sender_id', $user->id)
                      ->where('receiver_id', $loginId);
                })->latest()->first();

                $user->last_message_preview = $lastMsg
                    ? ($lastMsg->image ? '📷 Photo' : \Illuminate\Support\Str::limit($lastMsg->message, 30))
                    : 'Belum ada pesan';

                return $user;
            })
            ->sortByDesc('last_message_time');

        return view('partials.inbox-list', compact('users'));
    }
}
