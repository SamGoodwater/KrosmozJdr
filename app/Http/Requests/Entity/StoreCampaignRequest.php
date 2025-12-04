<?php

namespace App\Http\Requests\Entity;

use Illuminate\Foundation\Http\FormRequest;

class StoreCampaignRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'slug' => ['required', 'string', 'max:255', 'unique:campaigns,slug'],
            'keyword' => ['nullable', 'string', 'max:255'],
            'is_public' => ['required', 'boolean'],
            'state' => ['required', 'integer', 'in:0,1,2,3'],
            'usable' => ['nullable', 'integer', 'min:0', 'max:1'],
            'is_visible' => ['nullable', 'string', 'in:guest,user,player,game_master,admin,super_admin'],
            'image' => ['nullable', 'string', 'max:255'],
        ];
    }
}
