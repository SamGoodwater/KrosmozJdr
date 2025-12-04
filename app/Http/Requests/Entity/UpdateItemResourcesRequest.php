<?php

namespace App\Http\Requests\Entity;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'resources' => ['required', 'array'],
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
        $resources = $this->input('resources', []);
        $normalized = [];

        foreach ($resources as $resourceId => $pivotData) {
            $resourceId = (int) $resourceId;
            
            // Valider que l'ID de ressource existe
            if (!\App\Models\Entity\Resource::where('id', $resourceId)->exists()) {
                continue;
            }

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

