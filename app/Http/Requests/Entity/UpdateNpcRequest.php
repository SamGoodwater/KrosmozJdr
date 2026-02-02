<?php

namespace App\Http\Requests\Entity;

use Illuminate\Foundation\Http\FormRequest;

/**
 * FormRequest pour la mise à jour d'un Npc.
 *
 * Valide les champs principaux d'un NPC.
 */
class UpdateNpcRequest extends FormRequest
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
            'creature_id' => ['sometimes', 'required', 'integer', 'exists:creatures,id'],
            'story' => ['nullable', 'string'],
            'historical' => ['nullable', 'string'],
            'age' => ['nullable', 'string', 'max:255'],
            'size' => ['nullable', 'string', 'max:255'],
            'breed_id' => ['nullable', 'integer', 'exists:breeds,id'],
            'specialization_id' => ['nullable', 'integer', 'exists:specializations,id'],
        ];
    }
}
