<?php

namespace App\Http\Requests\Entity;

use App\Models\Entity\BreedElementOrientation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * FormRequest pour la mise à jour d'une Breed (affichée « Classe »).
 */
class UpdateBreedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description_fast' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'evolution' => ['nullable', 'string'],
            'life_dice' => ['nullable', 'string', 'max:255'],
            'specificity' => ['nullable', 'string'],
            'dofus_version' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'in:raw,draft,playable,archived'],
            'read_level' => ['nullable', 'integer', 'min:0', 'max:5'],
            'write_level' => ['nullable', 'integer', 'min:0', 'max:5', 'gte:read_level'],
            'image' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
            'auto_update' => ['nullable', 'boolean'],
            'official_id' => ['nullable', 'string', 'max:255'],
            'dofusdb_id' => ['nullable', 'string', 'max:255'],
        ], $this->elementOrientationRules());
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    private function elementOrientationRules(): array
    {
        $allowedKeys = config('breed_element_orientations.allowed_orientation_keys', []);
        $rules = [
            'element_orientations' => ['nullable', 'array'],
        ];
        foreach (BreedElementOrientation::ELEMENTS as $el) {
            $rules["element_orientations.{$el}"] = ['nullable', 'string', 'max:64', Rule::in($allowedKeys)];
        }

        return $rules;
    }
}
