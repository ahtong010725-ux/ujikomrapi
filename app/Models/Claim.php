<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    protected $fillable = [
        'claimer_id',
        'finder_id',
        'item_id',
        'item_type',
        'status',
        'owner_confirmed',
        'finder_confirmed',
        'confirmed_at',
        'flag_reason',
        'admin_notes',
        'proof',
        'proof_photo'
    ];

    protected $casts = [
        'owner_confirmed' => 'boolean',
        'finder_confirmed' => 'boolean',
        'confirmed_at' => 'datetime',
    ];

    public function claimer()
    {
        return $this->belongsTo(User::class, 'claimer_id');
    }

    public function finder()
    {
        return $this->belongsTo(User::class, 'finder_id');
    }

    public function item()
    {
        $modelClass = $this->item_type === 'lost' ? LostItem::class : FoundItem::class;
        return $this->belongsTo($modelClass, 'item_id');
    }

    public function getItemModel()
    {
        $modelClass = $this->item_type === 'lost' ? LostItem::class : FoundItem::class;
        return $modelClass::find($this->item_id);
    }
}
