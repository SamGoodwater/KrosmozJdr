<?php

declare(strict_types=1);

namespace App\Http\Requests\Effect;

use App\Services\Effect\EffectTextSanitizer;
use Illuminate\Foundation\Http\FormRequest;

class StoreSubEffectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->verifyRole('admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'slug' => 'required|string|max:64|unique:sub_effects,slug',
            'type_slug' => 'required|string|max:64',
            'template_text' => 'nullable|string|max:65535',
            'formula' => 'nullable|string|max:65535',
            'variables_allowed' => 'nullable|array',
            'variables_allowed.*' => 'string|max:64',
            'dofusdb_effect_id' => 'nullable|integer|min:0',
        ];
    }

    protected function passedValidation(): void
    {
        $s = new EffectTextSanitizer();
        if ($this->filled('template_text')) {
            $this->merge(['template_text' => $s->sanitize((string) $this->template_text)]);
        }
        if ($this->filled('formula')) {
            $this->merge(['formula' => $s->sanitize((string) $this->formula)]);
        }
    }
}
