<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Page;

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
            'in_menu' => ['sometimes', 'boolean'],
            'state' => ['sometimes', 'string', Rule::in([Page::STATE_RAW, Page::STATE_DRAFT, Page::STATE_PLAYABLE, Page::STATE_ARCHIVED])],
            'read_level' => ['sometimes', 'integer', 'min:0', 'max:5'],
            'write_level' => ['sometimes', 'integer', 'min:0', 'max:5', 'gte:read_level'],
            'parent_id' => ['nullable', 'exists:pages,id'],
            'menu_order' => ['sometimes', 'integer'],
            'menu_group' => ['nullable', 'string', 'max:100'],
            'entity_key' => ['nullable', 'string', 'max:50', Rule::in(config('entities.keys', []))],
            'icon' => ['nullable', 'string', 'max:255'],
            'page_css_classes' => ['nullable', 'string', 'max:500'],
            'title_css_classes' => ['nullable', 'string', 'max:500'],
            'menu_item_css_classes' => ['nullable', 'string', 'max:500'],
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
        if (isset($data['in_menu'])) {
            $this->merge([
                'in_menu' => filter_var($data['in_menu'], FILTER_VALIDATE_BOOLEAN),
            ]);
        }
        if (!isset($data['state'])) {
            $this->merge([
                'state' => Page::STATE_DRAFT,
            ]);
        }
        if (!isset($data['read_level'])) {
            $this->merge(['read_level' => \App\Models\User::ROLE_GUEST]);
        }
        if (!isset($data['write_level'])) {
            $this->merge(['write_level' => \App\Models\User::ROLE_ADMIN]);
        }
        if (array_key_exists('menu_group', $data)) {
            $group = trim((string) $data['menu_group']);
            $this->merge(['menu_group' => $group === '' ? null : $group]);
        }
        if (array_key_exists('entity_key', $data)) {
            $key = trim((string) $data['entity_key']);
            $this->merge(['entity_key' => $key === '' ? null : $key]);
        }
        foreach (['page_css_classes', 'title_css_classes', 'menu_item_css_classes'] as $field) {
            if (array_key_exists($field, $data)) {
                $val = trim((string) $data[$field]);
                $this->merge([$field => $val === '' ? null : $val]);
            }
        }
    }
}
