<?php

namespace App\Http\Requests\Entity;

use App\Http\Requests\Concerns\RestrictsAccessLevelMutation;
use App\Enums\EntityState;
use App\Http\Requests\Concerns\GuardsPlayableState;
use App\Models\Entity\Npc;
use App\Support\Creature\CreatureSize;
use App\Support\Npc\NpcRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Création d’un PNJ (crée une Creature si `name` est fourni sans `creature_id`).
 */
class StoreNpcRequest extends FormRequest
{
    use RestrictsAccessLevelMutation;

    use GuardsPlayableState;

    public function authorize(): bool
    {
        return $this->user()?->can('create', Npc::class) === true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'creature_id' => ['nullable', 'integer', 'exists:creatures,id', 'required_without:name'],
            'name' => ['required_without:creature_id', 'string', 'max:255'],
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

    protected function playableModelClass(): string
    {
        return Npc::class;
    }

    protected function prepareForValidation(): void
    {
        $this->stripAccessLevelsUnlessAdmin();
    }
}
