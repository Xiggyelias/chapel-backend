<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_name',
        'club_id',
        'requested_by',
        'quantity',
        'owner',
        'needed_date',
        'return_date',
        'status',
        'purpose',
        'remarks',
    ];

    protected $casts = [
        'needed_date' => 'date',
        'return_date' => 'date',
        'quantity' => 'integer',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
}
