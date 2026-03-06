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
    'last_seen'
];

public function messages()
{
    return $this->hasMany(Message::class, 'sender_id')
        ->orWhere('receiver_id', $this->id);
}

public function bookmarks()
{
    return $this->hasMany(Bookmark::class);
}
}
