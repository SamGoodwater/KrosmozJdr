<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\ObjectEffectAction;
use App\Http\Requests\Api\Concerns\ValidatesObjectEffectSemantics;
use App\Models\ObjectEffect;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreObjectEffectRequest extends FormRequest
{
    use ValidatesObjectEffectSemantics;

    public function authorize(): bool
    {
        return $this->user()?->verifyRole('game_master') ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'entity_type' => ['required', 'string', Rule::in(['item', 'consumable', 'resource'])],
            'entity_id' => ['required', 'integer', 'min:1'],
            'action' => ['required', Rule::enum(ObjectEffectAction::class)],
            'characteristic_id' => ['nullable', 'integer', 'exists:characteristics,id'],
            'monster_id' => ['nullable', 'integer', 'exists:monsters,id'],
            'value' => ['nullable', 'integer'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            $short = $this->input('entity_type');
            if (! is_string($short)) {
                return;
            }
            $class = ObjectEffect::entityTypeToClass($short);
            if ($class === null) {
                $v->errors()->add('entity_type', 'Type d’entité non pris en charge.');

                return;
            }
            $id = (int) $this->input('entity_id');
            if (! $class::query()->whereKey($id)->exists()) {
                $v->errors()->add('entity_id', 'Entité introuvable.');
            }
        });

        $this->validateObjectEffectSemantics($validator, null);
    }
}
