<?php

namespace App\Http\Requests\Entity;

use Illuminate\Foundation\Http\FormRequest;

/**
 * FormRequest pour la création d'un Monster.
 *
 * Valide les champs principaux d'un monstre.
 */
class StoreMonsterRequest extends FormRequest
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
            'creature_id' => ['required', 'integer', 'exists:creatures,id'],
            'official_id' => ['nullable', 'string', 'max:255'],
            'dofusdb_id' => ['nullable', 'string', 'max:255'],
            'dofus_version' => ['nullable', 'string', 'max:255'],
            'auto_update' => ['nullable', 'boolean'],
            'size' => ['nullable', 'integer', 'min:0', 'max:5'],
            'is_boss' => ['nullable', 'boolean'],
            'boss_pa' => ['nullable', 'integer', 'min:0'],
            'monster_race_id' => ['nullable', 'integer', 'exists:type_monster_races,id'],
        ];
    }
}
