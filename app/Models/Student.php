<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'nisn',
        'name',
        'kelas',
        'is_registered',
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
