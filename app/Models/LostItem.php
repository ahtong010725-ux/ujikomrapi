<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LostItem extends Model
{
protected $fillable = [
    'user_id',
    'name',
    'item_name',
    'location',
    'date',
    'description',
    'photo',
    'status'
];

public function user()
{
    return $this->belongsTo(User::class);
}

public function bookmarks()
{
    return $this->morphMany(Bookmark::class, 'bookmarkable');
}

}
