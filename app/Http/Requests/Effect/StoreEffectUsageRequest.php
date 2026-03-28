<?php

declare(strict_types=1);

namespace App\Http\Requests\Effect;

use Illuminate\Foundation\Http\FormRequest;

class StoreEffectUsageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->verifyRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'entity_type' => 'required|string|in:spell,item,consumable,resource',
            'entity_id' => 'required|integer|min:1',
            'effect_id' => 'required|integer|exists:effects,id',
            'required_creature_level' => 'nullable|integer|min:0',
        ];
    }
}
