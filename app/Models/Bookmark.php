<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    protected $fillable = [
        'user_id',
        'bookmarkable_id',
        'bookmarkable_type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookmarkable()
    {
        return $this->morphTo();
    }
}
