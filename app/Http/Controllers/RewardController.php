<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Claim;
use App\Models\LostItem;
use App\Models\FoundItem;
use App\Models\UserPoint;
use App\Models\Badge;
use App\Models\User;
use Carbon\Carbon;

class RewardController extends Controller
{
    public function leaderboard(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $users = UserPoint::with('user')
            ->where('month', $month)
            ->where('year', $year)
            ->orderByDesc('points')
            ->take(50)
            ->get();

        $badges = Badge::orderBy('points_required')->get();

        $monthName = Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');

        return view('leaderboard', compact('users', 'badges', 'month', 'year', 'monthName'));
    }

    /**
     * Someone sees a found post and claims it's their lost item.
     * Creates a claim for admin to review. Points go to the found item poster on approval.
     */
    public function claimItem(Request $request, $type, $id)
    {
        $request->validate([
            'proof' => 'required|string|max:500',
            'proof_photo' => 'nullable|image|max:5120'
        ]);

        if ($type !== 'found') {
            return back()->with('error', 'Hanya barang yang ditemukan (found) yang bisa diklaim.');
        }

        $item = FoundItem::findOrFail($id);

        if ($item->user_id == auth()->id()) {
            return back()->with('error', 'Kamu tidak bisa mengklaim barang yang kamu posting sendiri.');
        }

        $existingClaim = Claim::where('claimer_id', auth()->id())
            ->where('item_id', $id)
            ->where('item_type', 'found')
            ->first();

        if ($existingClaim) {
            return back()->with('error', 'Kamu sudah mengklaim barang ini. Status: ' . ucfirst($existingClaim->status));
        }

        $todayClaims = Claim::where('claimer_id', auth()->id())
            ->whereDate('created_at', today())
            ->count();

        if ($todayClaims >= 3) {
            return back()->with('error', 'Kamu sudah mencapai batas klaim hari ini (max 3/hari). Coba lagi besok.');
        }

        if ($item->status === 'resolved') {
            return back()->with('error', 'Barang ini sudah di-resolve.');
        }

        $proofPhotoPath = null;
        if ($request->hasFile('proof_photo')) {
            $proofPhotoPath = $request->file('proof_photo')->store('proof_photos', 'public');
        }

        Claim::create([
            'claimer_id' => auth()->id(),
            'item_id' => $id,
            'item_type' => 'found',
            'status' => 'pending',
            'proof' => $request->proof,
            'proof_photo' => $proofPhotoPath,
        ]);

        return back()->with('success', 'Klaim berhasil dikirim! Menunggu verifikasi admin. Jika disetujui, penemu akan mendapat 10 poin.');
    }
}

