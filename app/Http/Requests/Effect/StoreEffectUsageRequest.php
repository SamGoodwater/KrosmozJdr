<?php

declare(strict_types=1);

namespace App\Http\Requests\Effect;

use App\Models\EffectUsage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreEffectUsageRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** Aligné sur le middleware `role:game_master` des routes API d’écriture. */
        return $this->user()?->verifyRole('game_master') ?? false;
    }

    public function rules(): array
    {
        return [
            'entity_type' => 'required|string|in:item,consumable,resource',
            'entity_id' => 'required|integer|min:1',
            'effect_degree_id' => 'required|integer|exists:effect_degrees,id',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            $short = $this->input('entity_type');
            if (! is_string($short) || $short === '') {
                return;
            }
            $class = EffectUsage::entityTypeToClass($short);
            if ($class === null) {
                $v->errors()->add('entity_type', 'Type d’entité non pris en charge pour les usages.');
            }
        });
    }
}
