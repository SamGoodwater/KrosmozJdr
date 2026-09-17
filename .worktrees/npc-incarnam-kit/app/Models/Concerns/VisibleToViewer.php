<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\User;
use App\Services\EntityDisplay\EntityDisplayVisibilityService;
use Illuminate\Database\Eloquent\Builder;

/**
 * Scope liste aligné sur {@see \App\Policies\Entity\BaseEntityPolicy::view}.
 *
 * @method static Builder<static> visibleToUser(?User $user)
 */
trait VisibleToViewer
{
    /**
     * @param  Builder<static>  $query
     */
    public function scopeVisibleToUser(Builder $query, ?User $user): void
    {
        $service = app(EntityDisplayVisibilityService::class);
        $key = $service->permissionKeyForModel($query->getModel());

        if ($key === null) {
            return;
        }

        $service->constrainQueryToViewer($query, $user, $key);
    }
}
