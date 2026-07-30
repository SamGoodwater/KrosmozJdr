<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AdminActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Écriture centralisée du journal admin.
 *
 * @example app(AdminActivityLogger::class)->logEntity($spell, 'deleted', $user)
 */
class AdminActivityLogger
{
    /** @param array<string, mixed> $properties */
    public function logEntity(Model $subject, string $action, ?User $actor, array $properties = []): void
    {
        AdminActivityLog::query()->create([
            'domain' => 'entity',
            'action' => $action,
            'subject_type' => $subject::class,
            'subject_id' => $subject->getKey(),
            'subject_label' => $subject->name ?? $subject->title ?? '#'.$subject->getKey(),
            'actor_id' => $actor?->id,
            'status' => 'success',
            'properties' => $properties,
        ]);
    }
}
