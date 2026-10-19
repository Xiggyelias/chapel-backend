<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'club_id',
        'folder',
        'year',
        'title',
        'standardized_filename',
        'file_path',
        'file_size',
        'file_extension',
        'version',
        'uploaded_by',
        'is_archived',
        'archived_at',
        'archived_by',
        'notes',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
        'archived_at' => 'datetime',
        'year' => 'integer',
        'version' => 'integer',
    ];

    public static function folderLabels(): array
    {
        return [
            'constitution' => 'Constitution & Bye-laws',
            'executive_contacts' => 'Executive & Contacts',
            'minutes' => 'Minutes of Meetings',
            'programmes_events' => 'Programmes & Events',
            'reports' => 'Activity & Annual Reports',
            'finance_documents' => 'Finance & Budget Documents',
            'posters_media' => 'Posters & Media Assets',
            'approvals_correspondence' => 'Approvals & Official Correspondence',
            'handover_notes' => 'Leadership Handover Notes',
        ];
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function archiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'archived_by');
    }
}
