<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LostController;
use App\Http\Controllers\FoundController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RewardController;


Route::get('/', function () {
    return view('home');
});

Route::get('/home', function () {
    return view('home');
})->middleware(['auth', 'verified'])->name('home');


// ===================== NISN CHECK API =====================
Route::get('/api/check-nisn/{nisn}', [AuthController::class, 'checkNisn']);

// ===================== LOST ITEMS =====================

Route::get('/lost', [LostController::class, 'index']);

Route::middleware('auth')->group(function () {
    Route::get('/report-lost', [LostController::class, 'create']);
    Route::post('/report-lost', [LostController::class, 'store']);
    Route::get('/lost/{id}/edit', [LostController::class, 'edit']);
    Route::get('/lost/{id}/json', [LostController::class, 'getItemJson']);
    Route::put('/lost/{id}', [LostController::class, 'update']);
    Route::patch('/lost/{id}/status', [LostController::class, 'updateStatus'])->name('lost.status');
    Route::delete('/lost/{id}', [LostController::class, 'destroy']);
});


// ===================== FOUND ITEMS =====================

Route::get('/found', [FoundController::class, 'index']);

Route::middleware('auth')->group(function () {
    Route::get('/report-found', [FoundController::class, 'create']);
    Route::post('/report-found', [FoundController::class, 'store']);
    Route::get('/found/{id}/edit', [FoundController::class, 'edit']);
    Route::get('/found/{id}/json', [FoundController::class, 'getItemJson']);
    Route::put('/found/{id}', [FoundController::class, 'update']);
    Route::patch('/found/{id}/status', [FoundController::class, 'updateStatus'])->name('found.status');
    Route::delete('/found/{id}', [FoundController::class, 'destroy']);
});


// ===================== PROFILE =====================

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])
        ->name('profile');

    Route::put('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

// ===================== REWARDS & LEADERBOARD =====================
Route::get('/leaderboard', [RewardController::class, 'leaderboard'])->name('leaderboard');

Route::middleware('auth')->group(function () {
    Route::post('/claim/{type}/{id}', [RewardController::class, 'claimItem'])->name('claim.item');
    Route::post('/report-user/{id}', [RewardController::class, 'reportUser'])->name('report.user');
});

// ===================== ADMIN =====================
Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/admin/users/{id}/approve', [AdminController::class, 'approveUser'])->name('admin.users.approve');
    Route::post('/admin/users/{id}/reject', [AdminController::class, 'rejectUser'])->name('admin.users.reject');
    Route::delete('/admin/users/{id}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
    Route::get('/admin/items', [AdminController::class, 'items'])->name('admin.items');
    Route::delete('/admin/items/{type}/{id}', [AdminController::class, 'destroyItem'])->name('admin.items.destroy');

    // Claims management
    Route::get('/admin/claims', [AdminController::class, 'claims'])->name('admin.claims');
    Route::post('/admin/claims/{id}/approve', [AdminController::class, 'approveClaim'])->name('admin.claims.approve');
    Route::post('/admin/claims/{id}/reject', [AdminController::class, 'rejectClaim'])->name('admin.claims.reject');

    // User reports
    Route::get('/admin/reports', [AdminController::class, 'reports'])->name('admin.reports');
    Route::post('/admin/reports/{id}/resolve', [AdminController::class, 'resolveReport'])->name('admin.reports.resolve');
    Route::post('/admin/reports/{id}/ban', [AdminController::class, 'banFromReport'])->name('admin.reports.ban');

    // Ban management
    Route::post('/admin/users/{id}/ban', [AdminController::class, 'banUser'])->name('admin.users.ban');
    Route::post('/admin/users/{id}/unban', [AdminController::class, 'unbanUser'])->name('admin.users.unban');

    // Student management
    Route::get('/admin/students', [AdminController::class, 'students'])->name('admin.students');
    Route::post('/admin/students', [AdminController::class, 'storeStudent'])->name('admin.students.store');
    Route::put('/admin/students/{id}', [AdminController::class, 'updateStudent'])->name('admin.students.update');
    Route::delete('/admin/students/{id}', [AdminController::class, 'destroyStudent'])->name('admin.students.destroy');

    // Monthly champions
    Route::get('/admin/champions', [AdminController::class, 'champions'])->name('admin.champions');
    Route::post('/admin/champions/trigger', [AdminController::class, 'triggerChampion'])->name('admin.champions.trigger');
    Route::post('/admin/champions/{id}/update', [AdminController::class, 'updateChampionReward'])->name('admin.champions.update');
    Route::post('/admin/champions/{id}/give-reward', [AdminController::class, 'giveReward'])->name('admin.champions.giveReward');
});


// ===================== CHAT & INBOX =====================

Route::middleware('auth')->group(function () {
    Route::get('/chat/status/{id}', [LostController::class, 'fetchUserStatus']);
    Route::get('/chat/fetch/{id}', [LostController::class, 'fetchMessages']);
    Route::post('/chat/{user}', [LostController::class, 'sendMessage']);
    Route::get('/chat/{user}', [LostController::class, 'chat'])->name('chat');
});

Route::get('/inbox/fetch', [LostController::class, 'fetchInbox']);
Route::get('/inbox', [LostController::class, 'inbox'])->middleware('auth');


require __DIR__.'/auth.php';
