<?php

namespace App\Http\Requests\Entity;

use App\Enums\EntityState;
use App\Support\Creature\CreatureSize;
use App\Support\Npc\NpcRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Mise à jour d’un PNJ (champs coquille + identité Creature).
 */
class UpdateNpcRequest extends FormRequest
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
        return [
            'creature_id' => ['sometimes', 'nullable', 'integer', 'exists:creatures,id'],
            'name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'level' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'hostility' => ['nullable', 'integer', 'min:0', 'max:4'],
            'story' => ['nullable', 'string'],
            'historical' => ['nullable', 'string'],
            'age' => ['nullable', 'string', 'max:255'],
            'size' => ['nullable', 'integer', CreatureSize::rule()],
            'npc_role' => ['nullable', 'string', NpcRole::rule()],
            'breed_id' => ['nullable', 'integer', 'exists:breeds,id'],
            'specialization_id' => ['nullable', 'integer', 'exists:specializations,id'],
            'state' => ['nullable', 'string', EntityState::rule()],
            'read_level' => ['nullable', 'integer', 'min:0', 'max:4'],
            'write_level' => ['nullable', 'integer', 'min:0', 'max:4'],
        ];
    }
}
