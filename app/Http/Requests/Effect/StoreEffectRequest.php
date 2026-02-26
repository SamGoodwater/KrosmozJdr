<?php

declare(strict_types=1);

namespace App\Http\Requests\Effect;

use App\Services\Effect\EffectTextSanitizer;
use Illuminate\Foundation\Http\FormRequest;

class StoreEffectRequest extends FormRequest
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
            'name' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:64|unique:effects,slug',
            'description' => 'nullable|string|max:65535',
            'effect_group_id' => 'nullable|integer|exists:effect_groups,id',
            'degree' => 'nullable|integer|min:0|max:255',
        ];
    }

    protected function passedValidation(): void
    {
        if ($this->filled('description')) {
            $this->merge(['description' => (new EffectTextSanitizer())->sanitize((string) $this->description)]);
        }
    }
}
