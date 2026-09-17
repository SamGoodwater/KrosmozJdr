<?php

declare(strict_types=1);

namespace App\Http\Requests\Effect;

use App\Models\Effect;
use App\Rules\ValidAreaNotation;
use App\Services\Effect\EffectTextSanitizer;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEffectRequest extends FormRequest
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
        return [
            'name' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:64|unique:effects,slug',
            'description' => 'nullable|string|max:65535',
            'target_type' => 'nullable|string|in:direct,trap,glyph',
            'initial_area' => ['nullable', 'string', 'max:64', new ValidAreaNotation],
            'initial_degree_slug' => 'nullable|string|max:64',
            'initial_required_creature_level' => 'nullable|integer|min:0',
        ];
    }

    protected function passedValidation(): void
    {
        if ($this->filled('description')) {
            $this->merge(['description' => (new EffectTextSanitizer)->sanitize((string) $this->description)]);
        }
        if (! $this->filled('target_type')) {
            $this->merge(['target_type' => Effect::TARGET_DIRECT]);
        }
    }
}
