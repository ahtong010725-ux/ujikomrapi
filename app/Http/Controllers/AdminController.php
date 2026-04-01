<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LostItem;
use App\Models\FoundItem;
use App\Models\Claim;
use App\Models\UserPoint;
use App\Models\Badge;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $stats = [
            'users' => User::count(),
            'pending_users' => User::where('registration_status', 'pending')->count(),
            'lost_items' => LostItem::count(),
            'found_items' => FoundItem::count(),
            'resolved_lost' => LostItem::where('status', 'resolved')->count(),
            'resolved_found' => FoundItem::where('status', 'resolved')->count(),
            'pending_claims' => Claim::where('status', 'pending')->count(),
        ];

        // Student data with class filter
        $kelasFilter = $request->get('kelas', '');
        $studentsQuery = User::where('role', '!=', 'admin')->orderBy('kelas')->orderBy('name');

        if ($kelasFilter) {
            $studentsQuery->where('kelas', $kelasFilter);
        }

        $students = $studentsQuery->get();

        // Get distinct classes for filter dropdown
        $kelasList = User::where('role', '!=', 'admin')
            ->whereNotNull('kelas')
            ->distinct()
            ->pluck('kelas')
            ->sort();

        return view('admin.dashboard', compact('stats', 'students', 'kelasList', 'kelasFilter'));
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
        $lostItems = LostItem::with('user')->latest()->get();
        $foundItems = FoundItem::with('user')->latest()->get();
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

    // ===================== CLAIMS MANAGEMENT =====================

    public function claims()
    {
        $claims = Claim::with('claimer')->latest()->get()->map(function ($claim) {
            $claim->item_model = $claim->getItemModel();
            return $claim;
        });

        return view('admin.claims', compact('claims'));
    }

    public function approveClaim($id)
    {
        $claim = Claim::findOrFail($id);

        if (!in_array($claim->status, ['pending', 'flagged'])) {
            return back()->with('error', 'Klaim ini sudah diproses.');
        }

        $item = $claim->getItemModel();
        if (!$item) {
            $claim->update(['status' => 'approved', 'owner_confirmed' => true, 'confirmed_at' => now()]);
            return back()->with('success', 'Klaim disetujui (item sudah dihapus, no points).');
        }

        // Points go to the found item poster
        $finderId = $item->user_id;
        $claimerId = $claim->claimer_id;
        $pointsAwarded = 10;
        $month = now()->month;
        $year = now()->year;

        // === ANTI-CHEAT CHECKS ===

        // 1. Self-claiming check (claimer == poster)
        if ($finderId == $claimerId) {
            $claim->update(['status' => 'rejected', 'admin_notes' => 'Auto-reject: self-claim detected.']);
            return back()->with('error', '❌ Klaim ditolak: poster dan claimer adalah orang yang sama.');
        }

        // 2. Monthly points cap (max 50 per month)
        $userPoint = UserPoint::where('user_id', $finderId)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        if ($userPoint && $userPoint->points >= 50) {
            $claim->update(['status' => 'approved', 'owner_confirmed' => true, 'confirmed_at' => now()]);
            $item->update(['status' => 'resolved']);
            return back()->with('warning', '⚠️ Klaim disetujui, tapi penemu sudah mencapai batas 50 poin bulan ini. Tidak ada poin ditambah.');
        }

        // 3. Same user pair can only earn points 1x per month
        $pairInteractionsThisMonth = Claim::where('item_type', 'found')
            ->where('claimer_id', $claimerId)
            ->where('status', 'approved')
            ->whereMonth('confirmed_at', $month)
            ->whereYear('confirmed_at', $year)
            ->get()
            ->filter(function ($c) use ($finderId) {
                $cItem = $c->getItemModel();
                return $cItem && $cItem->user_id == $finderId;
            })
            ->count();

        if ($pairInteractionsThisMonth >= 1) {
            $claim->update(['status' => 'approved', 'owner_confirmed' => true, 'confirmed_at' => now()]);
            $item->update(['status' => 'resolved']);
            return back()->with('warning', '⚠️ Klaim disetujui, tapi pasangan user ini sudah pernah berinteraksi bulan ini. Poin tidak diberikan.');
        }

        // 4. Rapid post-then-claim detection (< 5 min between post and claim = suspicious)
        $minutesBetween = $item->created_at->diffInMinutes($claim->created_at);
        if ($minutesBetween < 5) {
            $claim->update(['status' => 'approved', 'owner_confirmed' => true, 'confirmed_at' => now(), 'flag_reason' => 'Klaim dikirim < 5 menit setelah item diposting.']);
            $item->update(['status' => 'resolved']);
            return back()->with('warning', '⚠️ Klaim disetujui, tapi terdeteksi mencurigakan (klaim < 5 menit). Poin tidak diberikan.');
        }

        // === ALL CHECKS PASSED — AWARD POINTS ===
        $claim->update(['status' => 'approved', 'owner_confirmed' => true, 'confirmed_at' => now()]);

        $userPointRecord = UserPoint::firstOrCreate(
            ['user_id' => $finderId, 'month' => $month, 'year' => $year],
            ['points' => 0, 'total_earned' => 0]
        );
        $userPointRecord->increment('points', $pointsAwarded);
        $userPointRecord->increment('total_earned', $pointsAwarded);

        // Mark item as resolved
        $item->update(['status' => 'resolved']);

        // Auto-resolve matching lost items from claimer
        if ($claimerId) {
            $claimerLostItems = LostItem::where('user_id', $claimerId)
                ->where('status', '!=', 'resolved')
                ->get();

            foreach ($claimerLostItems as $lostItem) {
                if (
                    stripos($item->item_name, $lostItem->item_name) !== false ||
                    stripos($lostItem->item_name, $item->item_name) !== false
                ) {
                    $lostItem->update(['status' => 'resolved']);
                    break;
                }
            }
        }

        $finderName = User::find($finderId)->name ?? 'User';
        return back()->with('success', '✅ Klaim disetujui! ' . $pointsAwarded . ' poin diberikan ke penemu: ' . $finderName);
    }

    public function rejectClaim(Request $request, $id)
    {
        $claim = Claim::findOrFail($id);

        if ($claim->status !== 'pending') {
            return back()->with('error', 'Klaim ini sudah diproses.');
        }

        $claim->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes ?? 'Klaim ditolak oleh admin.'
        ]);

        return back()->with('success', 'Klaim ditolak.');
    }
}
