<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Enums\ObjectEffectAction;
use App\Http\Requests\Api\Concerns\ValidatesObjectEffectSemantics;
use App\Models\ObjectEffect;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateObjectEffectRequest extends FormRequest
{
    use ValidatesObjectEffectSemantics;

    public function authorize(): bool
    {
        return $this->user()?->verifyRole('game_master') ?? false;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'action' => ['sometimes', 'required', Rule::enum(ObjectEffectAction::class)],
            'characteristic_id' => ['nullable', 'integer', 'exists:characteristics,id'],
            'monster_id' => ['nullable', 'integer', 'exists:monsters,id'],
            'value' => ['nullable', 'integer'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $effect = $this->route('object_effect');
        if (! $effect instanceof ObjectEffect) {
            return;
        }

        $this->validateObjectEffectSemantics($validator, $effect);
    }
}
