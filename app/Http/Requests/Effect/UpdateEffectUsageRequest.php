<?php

declare(strict_types=1);

namespace App\Http\Requests\Effect;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEffectUsageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->verifyRole('admin') ?? false;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'effect_id' => 'sometimes|integer|exists:effects,id',
            'level_min' => 'nullable|integer|min:0',
            'level_max' => 'nullable|integer|min:0|gte:level_min',
        ];
    }
}
