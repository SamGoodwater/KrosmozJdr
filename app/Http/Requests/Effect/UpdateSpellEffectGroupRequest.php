<?php

declare(strict_types=1);

namespace App\Http\Requests\Effect;

use App\Models\Effect;
use App\Models\Entity\Spell;
use App\Services\Effect\EffectGroupEditorDataService;

/**
 * Même charge utile que {@see UpdateEffectGroupRequest}, depuis la fiche sort.
 */
class UpdateSpellEffectGroupRequest extends UpdateEffectGroupRequest
{
    public function authorize(): bool
    {
        /** @var Spell $spell */
        $spell = $this->route('spell');
        /** @var Effect $effect */
        $effect = $this->route('effect');

        if (! $this->user()?->can('update', $spell)) {
            return false;
        }

        return app(EffectGroupEditorDataService::class)->spellLinksToEffect($spell, $effect);
    }
}
