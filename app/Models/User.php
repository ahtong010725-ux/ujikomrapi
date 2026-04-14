<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
   protected $fillable = [
    'nisn',
    'name',
    'kelas',
    'phone',
    'tanggal_lahir',
    'jenis_kelamin',
    'photo',
    'password',
    'role',
    'registration_status',
    'rejection_reason',
    'is_online',
    'last_seen',
    'ban_type',
    'banned_at',
    'ban_expires_at',
    'ban_reason',
    'ewallet_type',
    'ewallet_number',
    'student_id',
];

protected $casts = [
    'banned_at' => 'datetime',
    'ban_expires_at' => 'datetime',
];

/**
 * Check if user is currently banned (considers expiration for hard bans)
 */
public function isBanned()
{
    if (!$this->ban_type) {
        return false;
    }

    // If hard ban with expiration, check if expired
    if ($this->ban_type === 'hard' && $this->ban_expires_at && $this->ban_expires_at->isPast()) {
        // Auto-lift expired ban
        $this->update([
            'ban_type' => null,
            'banned_at' => null,
            'ban_expires_at' => null,
            'ban_reason' => null,
        ]);
        return false;
    }

    return true;
}

/**
 * Check if user is soft-banned (can view but can't act)
 */
public function isSoftBanned()
{
    return $this->isBanned() && $this->ban_type === 'soft';
}

/**
 * Check if user is hard-banned (can't login at all)
 */
public function isHardBanned()
{
    return $this->isBanned() && $this->ban_type === 'hard';
}

public function messages()
{
    return $this->hasMany(Message::class, 'sender_id')
        ->orWhere('receiver_id', $this->id);
}

public function student()
{
    return $this->belongsTo(Student::class);
}

public function points()
{
    return $this->hasOne(UserPoint::class);
}

public function claims()
{
    return $this->hasMany(Claim::class, 'claimer_id');
}

public function getPointsCount()
{
    return $this->points ? $this->points->points : 0;
}

public function getTotalEarned()
{
    return $this->points ? $this->points->total_earned : 0;
}

public function getEarnedBadges()
{
    $totalEarned = $this->getTotalEarned();
    return Badge::where('points_required', '<=', $totalEarned)->orderBy('points_required', 'desc')->get();
}

public function getHighestBadge()
{
    $totalEarned = $this->getTotalEarned();
    return Badge::where('points_required', '<=', $totalEarned)->orderBy('points_required', 'desc')->first();
}
}
