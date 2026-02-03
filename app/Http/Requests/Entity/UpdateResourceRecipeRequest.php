<?php

namespace App\Http\Requests\Entity;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Entity\Resource;

/**
 * FormRequest pour la mise à jour de la recette d'une ressource (ingrédients + quantités).
 *
 * Valide les relations many-to-many self-référentielles : resource ↔ resources (ingredients).
 */
class UpdateResourceRecipeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $resource = $this->route('resource');
        return $resource && $this->user()?->can('update', $resource);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $resourceId = $this->route('resource')?->id;

        return [
            'recipe' => [
                'present',
                'array',
                function (string $attribute, mixed $value, \Closure $fail) use ($resourceId) {
                    if (!is_array($value)) {
                        return;
                    }
                    foreach ($value as $ingredientId => $pivotData) {
                        $id = is_numeric((string) $ingredientId) ? (int) $ingredientId : null;
                        if (!$id || !Resource::whereKey($id)->exists()) {
                            $fail("La ressource ingrédient {$ingredientId} n'existe pas.");
                            return;
                        }
                        if ($resourceId !== null && (int) $id === (int) $resourceId) {
                            $fail('Une ressource ne peut pas être son propre ingrédient.');
                        }
                    }
                },
            ],
            'recipe.*' => ['array'],
            'recipe.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $recipe = $this->input('recipe');
        if ($recipe === null) {
            $this->merge(['recipe' => []]);
            return;
        }
        if (!is_array($recipe)) {
            return;
        }
        $normalized = [];
        foreach ($recipe as $ingredientId => $pivotData) {
            $ingredientId = (int) $ingredientId;
            if (is_array($pivotData) && isset($pivotData['quantity']) && $pivotData['quantity'] > 0) {
                $normalized[$ingredientId] = ['quantity' => (int) $pivotData['quantity']];
            }
        }
        $this->merge(['recipe' => $normalized]);
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'recipe.*.quantity.required' => 'La quantité est requise pour chaque ingrédient.',
            'recipe.*.quantity.integer' => 'La quantité doit être un nombre entier.',
            'recipe.*.quantity.min' => 'La quantité doit être au moins 1.',
        ];
    }
}
