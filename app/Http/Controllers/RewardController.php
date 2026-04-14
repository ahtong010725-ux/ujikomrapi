<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Claim;
use App\Models\LostItem;
use App\Models\FoundItem;
use App\Models\UserPoint;
use App\Models\Badge;
use App\Models\User;
use App\Models\UserReport;
use App\Models\MonthlyChampion;
use Carbon\Carbon;

class RewardController extends Controller
{
    public function leaderboard(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        // Get all non-admin users with their points for this month
        $allUsers = User::where('role', '!=', 'admin')
            ->where('registration_status', 'approved')
            ->get();

        $users = $allUsers->map(function ($user) use ($month, $year) {
                $userPoint = UserPoint::where('user_id', $user->id)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->first();

                return (object) [
                    'user' => $user,
                    'points' => $userPoint ? $userPoint->points : 0,
                    'total_earned' => $userPoint ? $userPoint->total_earned : 0,
                ];
            })
            ->sortByDesc('points')
            ->values();

        $badges = Badge::orderBy('points_required')->get();

        $monthName = Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');

        // Auto-generate champion for previous month if not exists
        $this->generateChampion();

        // Get champion for this month (if viewing past month)
        $champion = MonthlyChampion::with('user')
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        return view('leaderboard', compact('users', 'badges', 'month', 'year', 'monthName', 'champion'));
    }

    /**
     * Auto-generate champion record for previous month
     */
    private function generateChampion()
    {
        $prevMonth = now()->subMonth();
        $m = $prevMonth->month;
        $y = $prevMonth->year;

        // Skip if already exists
        if (MonthlyChampion::where('month', $m)->where('year', $y)->exists()) {
            return;
        }

        // Find top 1 of previous month
        $topUser = UserPoint::where('month', $m)
            ->where('year', $y)
            ->orderByDesc('points')
            ->first();

        if ($topUser && $topUser->points > 0) {
            MonthlyChampion::create([
                'user_id' => $topUser->user_id,
                'month' => $m,
                'year' => $y,
                'points' => $topUser->points,
            ]);
        }
    }

    /**
     * Someone sees a found post and claims it's their lost item.
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

        // Allow re-claim if previous was rejected
        $existingClaim = Claim::where('claimer_id', auth()->id())
            ->where('item_id', $id)
            ->where('item_type', 'found')
            ->whereIn('status', ['pending', 'approved'])
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

    /**
     * Report a user
     */
    public function reportUser(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:1000'
        ]);

        $reportedUser = User::findOrFail($id);

        if ($reportedUser->id == auth()->id()) {
            return back()->with('error', 'Kamu tidak bisa melaporkan dirimu sendiri.');
        }

        // Check if already reported this user recently (within 24h)
        $existing = UserReport::where('reporter_id', auth()->id())
            ->where('reported_user_id', $id)
            ->where('created_at', '>=', now()->subDay())
            ->exists();

        if ($existing) {
            return back()->with('error', 'Kamu sudah melaporkan user ini dalam 24 jam terakhir.');
        }

        UserReport::create([
            'reporter_id' => auth()->id(),
            'reported_user_id' => $id,
            'reason' => $request->reason,
        ]);

        return back()->with('success', 'Laporan berhasil dikirim. Admin akan meninjau laporan kamu.');
    }
}
