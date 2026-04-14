<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyChampion extends Model
{
    protected $fillable = [
        'user_id',
        'month',
        'year',
        'points',
        'reward_amount',
        'reward_status',
        'paid_at',
        'notes'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
