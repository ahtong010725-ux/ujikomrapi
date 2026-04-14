<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LostItem;
use App\Models\FoundItem;
use App\Models\Claim;
use App\Models\UserPoint;
use App\Models\Badge;
use App\Models\Message;
use App\Models\UserReport;
use App\Models\MonthlyChampion;
use App\Models\Student;

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

        // Reset student registration status if linked
        if ($user->student_id) {
            Student::where('id', $user->student_id)->update(['is_registered' => false]);
        }

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

        // 5. Admin exemption — admin poster doesn't earn points
        $finderUser = User::find($finderId);
        if ($finderUser && $finderUser->role === 'admin') {
            $claim->update(['status' => 'approved', 'owner_confirmed' => true, 'confirmed_at' => now()]);
            $item->update(['status' => 'resolved']);
            return back()->with('success', '✅ Klaim disetujui! Admin tidak mendapat poin.');
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

        if (!in_array($claim->status, ['pending', 'flagged'])) {
            return back()->with('error', 'Klaim ini sudah diproses.');
        }

        $reason = $request->admin_notes ?? 'Klaim ditolak oleh admin.';

        $claim->update([
            'status' => 'rejected',
            'admin_notes' => $reason
        ]);

        // Send rejection notification via chat
        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $claim->claimer_id,
            'message' => '❌ Klaim kamu untuk barang "' . ($claim->getItemModel()->item_name ?? 'Unknown') . '" ditolak.' . "\n\nAlasan: " . $reason . "\n\nKamu bisa mengajukan klaim lagi dengan bukti yang lebih jelas.",
            'is_read' => false
        ]);

        return back()->with('success', 'Klaim ditolak. Notifikasi telah dikirim ke user.');
    }

    // ===================== USER REPORTS =====================

    public function reports()
    {
        $reports = UserReport::with(['reporter', 'reportedUser'])->latest()->get();
        return view('admin.reports', compact('reports'));
    }

    public function resolveReport(Request $request, $id)
    {
        $report = UserReport::findOrFail($id);
        $report->update([
            'status' => 'resolved',
            'admin_notes' => $request->admin_notes ?? 'Ditinjau oleh admin.'
        ]);

        // Auto-send message to reported user asking for clarification
        $reportedUser = $report->reportedUser;
        $reporter = $report->reporter;
        if ($reportedUser) {
            Message::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $reportedUser->id,
                'message' => "⚠️ Kamu telah dilaporkan oleh pengguna lain.\n\nAlasan: " . $report->reason . "\n\nCatatan Admin: " . ($request->admin_notes ?? 'Tidak ada catatan.') . "\n\nAdmin meminta keterangan lebih lanjut. Silakan balas pesan ini untuk memberikan penjelasan.",
                'is_read' => false
            ]);
        }

        return back()->with('success', 'Laporan resolved. Pesan otomatis telah dikirim ke user yang dilaporkan.');
    }

    public function banFromReport(Request $request, $id)
    {
        $report = UserReport::findOrFail($id);
        $user = $report->reportedUser;

        if (!$user) {
            return back()->with('error', 'User tidak ditemukan.');
        }

        $banType = $request->ban_type ?? 'soft';
        $duration = $request->ban_duration; // in days, null = until admin lifts
        $reason = $request->ban_reason ?? $report->reason;

        $expiresAt = null;
        if ($duration && $banType === 'hard') {
            $expiresAt = now()->addDays((int) $duration);
        }

        $user->update([
            'ban_type' => $banType,
            'banned_at' => now(),
            'ban_expires_at' => $expiresAt,
            'ban_reason' => $reason,
        ]);

        // Mark report as resolved
        $report->update([
            'status' => 'resolved',
            'admin_notes' => ($banType === 'hard' ? '🔴 Hard Banned' : '🟡 Soft Banned') . ($duration ? " ({$duration} hari)" : ' (sampai dicabut)') . ' — ' . $reason,
        ]);

        // Notify user via chat
        $banMsg = $banType === 'hard'
            ? "🚫 Akun kamu telah di-HARD BAN.\n\nAlasan: {$reason}\n" . ($duration ? "Durasi: {$duration} hari (sampai " . $expiresAt->format('d-m-Y H:i') . ")" : "Durasi: Sampai admin mencabut ban.") . "\n\nKamu tidak bisa login selama ban berlaku."
            : "⚠️ Akun kamu telah di-SOFT BAN.\n\nAlasan: {$reason}\n\nKamu masih bisa melihat halaman, tapi tidak bisa melakukan aksi (post, klaim, chat) sampai ban dicabut.";

        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $user->id,
            'message' => $banMsg,
            'is_read' => false
        ]);

        return back()->with('success', ($banType === 'hard' ? '🔴 Hard Ban' : '🟡 Soft Ban') . ' diterapkan ke ' . $user->name);
    }

    public function banUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->role === 'admin') {
            return back()->with('error', 'Tidak bisa mem-ban admin.');
        }

        $banType = $request->ban_type ?? 'soft';
        $duration = $request->ban_duration;
        $reason = $request->ban_reason ?? 'Dikenakan oleh admin.';

        $expiresAt = null;
        if ($duration && $banType === 'hard') {
            $expiresAt = now()->addDays((int) $duration);
        }

        $user->update([
            'ban_type' => $banType,
            'banned_at' => now(),
            'ban_expires_at' => $expiresAt,
            'ban_reason' => $reason,
        ]);

        return back()->with('success', ($banType === 'hard' ? '🔴 Hard Ban' : '🟡 Soft Ban') . ' diterapkan ke ' . $user->name);
    }

    public function unbanUser($id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'ban_type' => null,
            'banned_at' => null,
            'ban_expires_at' => null,
            'ban_reason' => null,
        ]);

        // Notify
        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $user->id,
            'message' => "✅ Ban kamu telah dicabut. Kamu sekarang bisa menggunakan platform secara normal kembali.",
            'is_read' => false
        ]);

        return back()->with('success', '✅ Ban dicabut untuk ' . $user->name);
    }

    // ===================== STUDENT MANAGEMENT =====================

    public function students(Request $request)
    {
        $kelasFilter = $request->get('kelas', '');
        $search = $request->get('search', '');

        $query = Student::orderBy('kelas')->orderBy('name');

        if ($kelasFilter) {
            $query->where('kelas', $kelasFilter);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nisn', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $students = $query->get();
        $kelasList = Student::distinct()->pluck('kelas')->sort();

        return view('admin.students', compact('students', 'kelasList', 'kelasFilter', 'search'));
    }

    public function storeStudent(Request $request)
    {
        $request->validate([
            'nisn' => 'required|unique:students,nisn',
            'name' => 'required|string|max:255',
            'kelas' => 'required|string|max:50',
        ]);

        Student::create([
            'nisn' => $request->nisn,
            'name' => $request->name,
            'kelas' => $request->kelas,
        ]);

        return back()->with('success', 'Siswa berhasil ditambahkan!');
    }

    public function updateStudent(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'kelas' => 'required|string|max:50',
        ]);

        $student->update([
            'name' => $request->name,
            'kelas' => $request->kelas,
        ]);

        // If student has a registered user, also update user's name & kelas
        if ($student->is_registered) {
            User::where('student_id', $student->id)->update([
                'name' => $request->name,
                'kelas' => $request->kelas,
            ]);
        }

        return back()->with('success', 'Data siswa berhasil diupdate!');
    }

    public function destroyStudent($id)
    {
        $student = Student::findOrFail($id);

        if ($student->is_registered) {
            return back()->with('error', 'Siswa ini sudah memiliki akun. Hapus akun user-nya terlebih dahulu.');
        }

        $student->delete();
        return back()->with('success', 'Data siswa berhasil dihapus.');
    }

    // ===================== MONTHLY CHAMPIONS =====================

    public function champions()
    {
        $champions = MonthlyChampion::with('user')->orderByDesc('year')->orderByDesc('month')->get();

        // Get users for the trigger form (top users of each month)
        $currentMonth = now()->month;
        $currentYear = now()->year;

        return view('admin.champions', compact('champions', 'currentMonth', 'currentYear'));
    }

    public function triggerChampion(Request $request)
    {
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;

        // Check if already exists
        if (MonthlyChampion::where('month', $month)->where('year', $year)->exists()) {
            return back()->with('error', 'Champion untuk bulan ini sudah ada.');
        }

        // Find top user of that month
        $topUser = \App\Models\UserPoint::where('month', $month)
            ->where('year', $year)
            ->orderByDesc('points')
            ->first();

        if (!$topUser || $topUser->points <= 0) {
            return back()->with('error', 'Tidak ada user dengan poin di bulan tersebut.');
        }

        MonthlyChampion::create([
            'user_id' => $topUser->user_id,
            'month' => $month,
            'year' => $year,
            'points' => $topUser->points,
        ]);

        $monthName = \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');
        return back()->with('success', "🏆 Champion {$monthName} berhasil di-generate!");
    }

    public function updateChampionReward(Request $request, $id)
    {
        $champion = MonthlyChampion::with('user')->findOrFail($id);
        $wasPaid = $champion->reward_status === 'paid';

        $champion->update([
            'reward_amount' => $request->reward_amount ? preg_replace('/[^0-9]/', '', $request->reward_amount) : null,
            'reward_status' => $request->reward_status ?? $champion->reward_status,
            'paid_at' => $request->reward_status === 'paid' ? now() : $champion->paid_at,
            'notes' => $request->notes ?? $champion->notes,
        ]);

        // Auto-send notification when reward is marked as paid
        if (!$wasPaid && $request->reward_status === 'paid' && $champion->user) {
            $monthName = \Carbon\Carbon::createFromDate($champion->year, $champion->month, 1)->translatedFormat('F Y');
            $amount = $champion->reward_amount ? 'Rp ' . number_format($champion->reward_amount, 0, ',', '.') : '';

            Message::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $champion->user->id,
                'message' => "💸 Hadiah Champion {$monthName} sebesar {$amount} telah dikirim! Silakan cek e-wallet kamu. Terima kasih telah aktif di I FOUND! 🎉",
                'is_read' => false
            ]);
        }

        return back()->with('success', 'Data champion berhasil diupdate.' . (!$wasPaid && $request->reward_status === 'paid' ? ' Notifikasi pembayaran telah dikirim.' : ''));
    }

    public function giveReward($id)
    {
        $champion = MonthlyChampion::with('user')->findOrFail($id);
        $user = $champion->user;

        if (!$user) {
            return back()->with('error', 'User tidak ditemukan.');
        }

        $monthName = \Carbon\Carbon::createFromDate($champion->year, $champion->month, 1)->translatedFormat('F Y');
        $rewardAmount = $champion->reward_amount ? 'Rp ' . number_format($champion->reward_amount, 0, ',', '.') : 'Belum ditentukan';

        // Build congratulations message
        $msg = "🎉🏆 SELAMAT! Kamu adalah CHAMPION bulan {$monthName}!\n\n";
        $msg .= "📊 Poin: {$champion->points} pts\n";
        $msg .= "💰 Hadiah: {$rewardAmount}\n\n";

        if ($champion->reward_amount && $user->ewallet_type && $user->ewallet_number) {
            $msg .= "Hadiah akan dikirim ke {$user->ewallet_type} kamu ({$user->ewallet_number}).\n";
            $msg .= "Silakan tunggu proses transfer dari admin.";
        } elseif ($champion->reward_amount) {
            $msg .= "⚠️ Kamu belum mengisi info e-wallet di profile. Silakan update profile kamu dengan nomor e-wallet (Dana/GoPay/OVO/ShopeePay) agar hadiah bisa dikirim.";
        } else {
            $msg .= "Admin akan segera menentukan jumlah hadiah kamu. Stay tuned! 🚀";
        }

        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $user->id,
            'message' => $msg,
            'is_read' => false
        ]);

        return back()->with('success', "🎉 Pesan selamat telah dikirim ke {$user->name}!");
    }
}

