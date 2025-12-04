<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Page;
use App\Enums\PageState;
use App\Enums\Visibility;

/**
 * FormRequest pour la création d'une page dynamique.
 *
 * Valide les champs principaux d'une page et vérifie l'autorisation via la policy.
 */
class StorePageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\Page::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:pages,slug', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'is_visible' => ['sometimes', Rule::enum(Visibility::class)],
            'can_edit_role' => ['sometimes', Rule::enum(Visibility::class)],
            'in_menu' => ['sometimes', 'boolean'],
            'state' => ['sometimes', Rule::enum(PageState::class)],
            'parent_id' => ['nullable', 'exists:pages,id'],
            'menu_order' => ['sometimes', 'integer'],
        ];
    }

    protected function prepareForValidation()
    {
        $data = $this->all();
        if (isset($data['title']) && !isset($data['slug'])) {
            $this->merge([
                'slug' => \Str::slug($data['title']),
            ]);
        }
        // Ne pas convertir is_visible en booléen, c'est un enum string
        if (isset($data['in_menu'])) {
            $this->merge([
                'in_menu' => filter_var($data['in_menu'], FILTER_VALIDATE_BOOLEAN),
            ]);
        }
        if (!isset($data['state'])) {
            $this->merge([
                'state' => PageState::DRAFT->value,
            ]);
        }
        if (!isset($data['can_edit_role'])) {
            $this->merge([
                'can_edit_role' => Visibility::ADMIN->value,
            ]);
        }
        if (!isset($data['is_visible'])) {
            $this->merge([
                'is_visible' => Visibility::GUEST->value,
            ]);
        }
    }
}
