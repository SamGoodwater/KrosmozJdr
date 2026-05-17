<?php

declare(strict_types=1);

namespace App\Http\Requests\Effect;

use App\Services\Effect\EffectTextSanitizer;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEffectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->verifyRole('game_master') ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $effect = $this->route('effect');

        return [
            'name' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:64|unique:effects,slug,'.($effect?->id ?? 0),
            'description' => 'nullable|string|max:65535',
            'target_type' => 'nullable|string|in:direct,trap,glyph',
        ];
    }

    protected function passedValidation(): void
    {
        if ($this->filled('description')) {
            $this->merge(['description' => (new EffectTextSanitizer)->sanitize((string) $this->description)]);
        }
    }
}
