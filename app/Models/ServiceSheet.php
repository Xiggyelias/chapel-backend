<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceSheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_date',
        'service_type',
        'preacher',
        'theme',
        'scripture',
        'hymns',
        'lyrics',
        'liturgist',
        'bible_reader',
        'choir',
        'praise_worship_team',
        'ushers',
        'media_contact',
        'sign_interpreter_required',
        'sign_interpreter_name',
        'status',
        'preacher_submitted_materials',
        'media_notified',
        'order_of_service_notes',
        'created_by',
    ];

    protected $casts = [
        'service_date' => 'date',
        'preacher_submitted_materials' => 'boolean',
        'media_notified' => 'boolean',
        'sign_interpreter_required' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
