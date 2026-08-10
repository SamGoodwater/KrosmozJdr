<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Suivi d’un nettoyage de fichiers MediaLibrary orphelins (dry-run ou suppression).
 *
 * @property string $id
 * @property string $status
 * @property string $mode
 * @property int|null $requested_by
 * @property array<array-key, mixed>|null $payload
 * @property array<array-key, mixed>|null $summary
 * @property int $progress_done
 * @property int $progress_total
 * @property string|null $error
 * @property Carbon|null $started_at
 * @property Carbon|null $finished_at
 * @property Carbon|null $cancelled_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $user
 *
 * @example MediaCleanupJob::query()->create(['status' => MediaCleanupJob::STATUS_QUEUED, 'mode' => MediaCleanupJob::MODE_DRY_RUN]);
 */
class MediaCleanupJob extends Model
{
    public const STATUS_QUEUED = 'queued';

    public const STATUS_RUNNING = 'running';

    public const STATUS_SUCCEEDED = 'succeeded';

    public const STATUS_FAILED = 'failed';

    public const STATUS_CANCELLED = 'cancelled';

    public const MODE_DRY_RUN = 'dry_run';

    public const MODE_DELETE = 'delete';

    protected $table = 'media_cleanup_jobs';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'status',
        'mode',
        'requested_by',
        'payload',
        'summary',
        'progress_done',
        'progress_total',
        'error',
        'started_at',
        'finished_at',
        'cancelled_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'summary' => 'array',
        'progress_done' => 'integer',
        'progress_total' => 'integer',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $job): void {
            if (! $job->id) {
                $job->id = (string) Str::uuid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function isTerminal(): bool
    {
        return in_array($this->status, [
            self::STATUS_SUCCEEDED,
            self::STATUS_FAILED,
            self::STATUS_CANCELLED,
        ], true);
    }

    public function isDeleteMode(): bool
    {
        return $this->mode === self::MODE_DELETE;
    }

    /**
     * @return array{
     *   id: string,
     *   status: string,
     *   mode: string,
     *   progress_done: int,
     *   progress_total: int,
     *   summary: array<array-key, mixed>|null,
     *   error: string|null,
     *   started_at: string|null,
     *   finished_at: string|null,
     *   cancelled_at: string|null,
     *   created_at: string|null
     * }
     */
    public function toStatusPayload(): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'mode' => $this->mode,
            'progress_done' => $this->progress_done,
            'progress_total' => $this->progress_total,
            'summary' => $this->summary,
            'error' => $this->error,
            'started_at' => $this->started_at?->toIso8601String(),
            'finished_at' => $this->finished_at?->toIso8601String(),
            'cancelled_at' => $this->cancelled_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
