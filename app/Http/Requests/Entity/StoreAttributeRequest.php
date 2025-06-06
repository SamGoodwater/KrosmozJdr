<?php

namespace App\Http\Requests\Entity;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttributeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Seuls les admins ou super_admin peuvent créer un attribut
        return $this->user() && in_array($this->user()->role, ['admin', 'super_admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'usable' => ['nullable', 'integer', 'min:0', 'max:1'],
            'is_visible' => ['nullable', 'string', 'in:guest,user,player,game_master,admin,super_admin'],
            'image' => ['nullable', 'string', 'max:255'],
        ];
    }
}
