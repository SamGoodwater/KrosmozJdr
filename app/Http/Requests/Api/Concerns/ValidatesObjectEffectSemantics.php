<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Concerns;

use App\Enums\ObjectEffectAction;
use App\Models\ObjectEffect;
use Illuminate\Validation\Validator;

trait ValidatesObjectEffectSemantics
{
    /**
     * Règles métier : téléport sans cible, invocation avec monstre et sans valeur, etc.
     */
    protected function validateObjectEffectSemantics(Validator $validator, ?ObjectEffect $current = null): void
    {
        $validator->after(function (Validator $v) use ($current): void {
            $actionStr = $this->input('action');
            if (($actionStr === null || $actionStr === '') && $current !== null) {
                $actionStr = $current->action->value;
            }
            $action = ObjectEffectAction::tryFrom((string) $actionStr);
            if ($action === null) {
                return;
            }

            $charRaw = $this->input('characteristic_id', $current?->characteristic_id);
            $monRaw = $this->input('monster_id', $current?->monster_id);
            $valRaw = $this->input('value', $current?->value);

            $hasChar = $charRaw !== null && $charRaw !== '';
            $hasMon = $monRaw !== null && $monRaw !== '';

            if ($action === ObjectEffectAction::Teleport) {
                if ($hasChar || $hasMon) {
                    $v->errors()->add('action', 'Pour « Téléporter », ne renseignez ni caractéristique ni monstre.');
                }
            }

            if ($action === ObjectEffectAction::Invoke) {
                if (! $hasMon) {
                    $v->errors()->add('monster_id', 'Pour « Invoquer », sélectionnez un monstre.');
                }
                if ($hasChar) {
                    $v->errors()->add('characteristic_id', 'Pour « Invoquer », ne renseignez pas de caractéristique.');
                }
                if ($valRaw !== null && $valRaw !== '') {
                    $v->errors()->add('value', 'Pour « Invoquer », laissez la valeur vide.');
                }
            }
        });
    }
}
