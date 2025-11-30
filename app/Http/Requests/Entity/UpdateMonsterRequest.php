<?php

namespace App\Http\Requests\Entity;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMonsterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'size' => ['nullable', 'integer', 'min:0'],
            'is_boss' => ['nullable', 'boolean'],
            'boss_pa' => ['nullable', 'integer', 'min:0'],
            'monster_race_id' => ['nullable', 'integer', 'exists:type_monster_races,id'],
            'dofus_version' => ['nullable', 'string', 'max:255'],
            'auto_update' => ['nullable', 'boolean'],
        ];
    }
}
