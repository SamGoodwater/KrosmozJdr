<?php

declare(strict_types=1);

namespace App\Http\Requests\Concerns;

use App\Support\Entity\KrosmozItemEffectValidator;
use Illuminate\Validation\Validator;

/**
 * Valide le champ `effect` (JSON plat Krosmoz) sur les FormRequest Item.
 */
trait ValidatesKrosmozItemEffect
{
    protected function validateKrosmozItemEffect(Validator $validator): void
    {
        if ($validator->errors()->isNotEmpty()) {
            return;
        }
        if (! $this->has('effect')) {
            return;
        }

        $effect = $this->input('effect');
        if ($effect === null || $effect === '') {
            return;
        }
        if (! is_string($effect)) {
            $validator->errors()->add('effect', 'effect : chaîne JSON attendue.');

            return;
        }

        $itemTypeId = $this->input('item_type_id');
        $typeId = is_numeric($itemTypeId) ? (int) $itemTypeId : null;

        $errors = app(KrosmozItemEffectValidator::class)->validateFlatEffectJson($effect, $typeId);
        foreach ($errors as $message) {
            $validator->errors()->add('effect', $message);
        }
    }
}
