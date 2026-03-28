<?php

declare(strict_types=1);

namespace App\Http\Requests\Effect;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEffectUsageRequest extends FormRequest
{
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
            'effect_id' => 'sometimes|integer|exists:effects,id',
            'required_creature_level' => 'nullable|integer|min:0',
        ];
    }
}
