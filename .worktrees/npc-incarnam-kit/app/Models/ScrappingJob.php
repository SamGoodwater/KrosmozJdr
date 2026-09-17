<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property string $id
 * @property string $kind
 * @property string $status
 * @property string|null $run_id
 * @property int|null $requested_by
 * @property array<array-key, mixed> $payload
 * @property array<array-key, mixed>|null $summary
 * @property array<array-key, mixed>|null $results
 * @property int $progress_done
 * @property int $progress_total
 * @property string|null $error
 * @property Carbon|null $started_at
 * @property Carbon|null $finished_at
 * @property Carbon|null $cancelled_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob whereCancelledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob whereError($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob whereFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob whereKind($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob whereProgressDone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob whereProgressTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob whereRequestedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob whereResults($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob whereRunId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob whereSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ScrappingJob whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ScrappingJob extends Model
{
    public const STATUS_QUEUED = 'queued';

    public const STATUS_RUNNING = 'running';

    public const STATUS_SUCCEEDED = 'succeeded';

    public const STATUS_FAILED = 'failed';

    public const STATUS_CANCELLED = 'cancelled';

    protected $table = 'scrapping_jobs';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'kind',
        'status',
        'run_id',
        'requested_by',
        'payload',
        'summary',
        'results',
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
        'results' => 'array',
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
}
