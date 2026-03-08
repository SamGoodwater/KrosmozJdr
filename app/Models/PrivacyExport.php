<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrivacyExport extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_READY = 'ready';
    public const STATUS_FAILED = 'failed';
    public const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'user_id',
        'data_subject_request_id',
        'status',
        'path',
        'checksum',
        'expires_at',
        'downloaded_at',
        'meta',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'downloaded_at' => 'datetime',
        'meta' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dataSubjectRequest(): BelongsTo
    {
        return $this->belongsTo(DataSubjectRequest::class);
    }
}

