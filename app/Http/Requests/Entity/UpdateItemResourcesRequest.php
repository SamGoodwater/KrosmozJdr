<?php

namespace App\Http\Requests\Entity;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Entity\Resource;

/**
 * FormRequest pour la mise à jour des ressources d'un Item.
 *
 * Valide les relations many-to-many entre items et resources avec quantités.
 */
class UpdateItemResourcesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // `present` pour supporter le cas "vider toutes les ressources" (array vide).
            'resources' => [
                'present',
                'array',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if (!is_array($value)) return;
                    foreach ($value as $resourceId => $pivotData) {
                        $id = is_numeric((string) $resourceId) ? (int) $resourceId : null;
                        if (!$id || !Resource::whereKey($id)->exists()) {
                            $fail("La ressource {$resourceId} n'existe pas.");
                        }
                    }
                },
            ],
            'resources.*' => ['array'],
            'resources.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Normaliser le format des ressources
        // Format attendu : { resource_id: { quantity: value } }
        $resources = $this->input('resources');
        if ($resources === null) {
            // Le "resources => []" en form-data peut être envoyé comme champ absent.
            // On force la présence pour permettre le "clear all".
            $this->merge(['resources' => []]);
            return;
        }
        if (!is_array($resources)) {
            // Laisser la validation `array` produire une erreur au lieu de throw.
            return;
        }

        $normalized = [];

        foreach ($resources as $resourceId => $pivotData) {
            $resourceId = (int) $resourceId;
            
            if (is_array($pivotData) && isset($pivotData['quantity']) && $pivotData['quantity'] > 0) {
                $normalized[$resourceId] = [
                    'quantity' => (int) $pivotData['quantity']
                ];
            }
        }

        $this->merge(['resources' => $normalized]);
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'resources.required' => 'Les ressources sont requises.',
            'resources.array' => 'Les ressources doivent être un tableau.',
            'resources.*.quantity.required' => 'La quantité est requise pour chaque ressource.',
            'resources.*.quantity.integer' => 'La quantité doit être un nombre entier.',
            'resources.*.quantity.min' => 'La quantité doit être au moins 1.',
        ];
    }
}

