<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'user_id',
        'user_name',
        'action',
        'old_status',
        'new_status',
        'notes',
        'ip_address',
    ];

    /**
     * Append an entry to the institutional audit ledger on behalf of the signed-in user.
     */
    public static function record(string $action, string $notes, ?Request $chapelRequest = null, ?string $oldStatus = null, ?string $newStatus = null): self
    {
        $user = auth()->user();

        return static::create([
            'request_id' => $chapelRequest?->id,
            'user_id' => $user?->id,
            'user_name' => $user?->name ?? 'System',
            'action' => $action,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'notes' => $notes,
            'ip_address' => request()?->ip(),
        ]);
    }

    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
