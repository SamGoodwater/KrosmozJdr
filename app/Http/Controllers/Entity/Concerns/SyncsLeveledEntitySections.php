<?php

namespace App\Http\Controllers\Entity\Concerns;

use App\Http\Requests\Entity\Concerns\ValidatesLeveledRelationSync;
use App\Services\Entity\EntityLeveledSectionsService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;

/**
 * Action PATCH partagée pour lier des sections CMS à une entité (breed, specialization, …).
 */
trait SyncsLeveledEntitySections
{
    /**
     * @param  FormRequest&ValidatesLeveledRelationSync  $request
     */
    protected function syncLeveledEntitySections(
        Model $entity,
        FormRequest $request,
        string $successMessage,
    ): RedirectResponse {
        app(EntityLeveledSectionsService::class)->sync(
            $entity,
            $request->validatedLeveledSyncPayload(),
        );

        return redirect()->back()->with('success', $successMessage);
    }
}
