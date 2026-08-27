<?php

namespace App\Http\Requests\Entity;

use App\Enums\EntityState;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * FormRequest pour la création d'un Npc.
 *
 * Valide les champs principaux d'un NPC.
 */
class StoreNpcRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'creature_id' => ['required', 'integer', 'exists:creatures,id'],
            'story' => ['nullable', 'string'],
            'historical' => ['nullable', 'string'],
            'age' => ['nullable', 'string', 'max:255'],
            'size' => ['nullable', 'string', 'max:255'],
            'breed_id' => ['nullable', 'integer', 'exists:breeds,id'],
            'specialization_id' => ['nullable', 'integer', 'exists:specializations,id'],
            'state' => ['nullable', 'string', EntityState::rule()],
            'read_level' => ['nullable', 'integer', 'min:0', 'max:4'],
            'write_level' => ['nullable', 'integer', 'min:0', 'max:4'],
        ];
    }
}
