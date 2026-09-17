<?php

declare(strict_types=1);

namespace App\Models;

use App\Support\ProjectSchedule\ProjectScheduleCatalog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Tâche planifiée Laravel (cron + activation) configurable en base.
 *
 * @property string $task_key Identifiant stable (voir {@see ProjectScheduleCatalog})
 * @property bool $enabled Exécuter via `schedule:run`
 * @property string $cron_expression Expression cron (5 segments)
 * @property bool $without_overlapping Empêcher les ré-entrées
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectScheduleTask newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectScheduleTask newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectScheduleTask query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectScheduleTask whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectScheduleTask whereCronExpression($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectScheduleTask whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectScheduleTask whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectScheduleTask whereTaskKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectScheduleTask whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectScheduleTask whereWithoutOverlapping($value)
 * @mixin \Eloquent
 */
class ProjectScheduleTask extends Model
{
    /** {@inheritdoc} */
    protected $table = 'project_schedule_tasks';

    /** @var list<string> */
    protected $fillable = [
        'task_key',
        'enabled',
        'cron_expression',
        'without_overlapping',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'without_overlapping' => 'boolean',
        ];
    }
}
