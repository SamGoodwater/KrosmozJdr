<?php

namespace App\Http\Requests;

use App\Enums\EntityState;
use App\Models\Page;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        return $this->user()?->can('create', Page::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:pages,slug', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'in_menu' => ['sometimes', 'boolean'],
            'state' => ['sometimes', 'string', EntityState::rule()],
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
            'settings' => ['nullable', 'array'],
            'settings.show_rules_breadcrumb' => ['sometimes', 'boolean'],
            'settings.menu_collapsible' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation()
    {
        $data = $this->all();
        if (isset($data['title']) && ! isset($data['slug'])) {
            $this->merge([
                'slug' => \Str::slug($data['title']),
            ]);
        }
        if (isset($data['in_menu'])) {
            $this->merge([
                'in_menu' => filter_var($data['in_menu'], FILTER_VALIDATE_BOOLEAN),
            ]);
        }
        if (! isset($data['state'])) {
            $this->merge([
                'state' => Page::STATE_DRAFT,
            ]);
        }
        if (! isset($data['read_level'])) {
            $this->merge(['read_level' => User::ROLE_GUEST]);
        }
        if (! isset($data['write_level'])) {
            // MJ et au-dessus alignés décision Q6 (réglages fins par page possibles ensuite).
            $this->merge(['write_level' => User::ROLE_GAME_MASTER]);
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

        $settings = $data['settings'] ?? [];
        if (! is_array($settings)) {
            $settings = [];
        }
        if (! array_key_exists('show_rules_breadcrumb', $settings)) {
            $settings['show_rules_breadcrumb'] = true;
        } else {
            $settings['show_rules_breadcrumb'] = filter_var($settings['show_rules_breadcrumb'], FILTER_VALIDATE_BOOLEAN);
        }
        if (array_key_exists('menu_collapsible', $settings)) {
            $settings['menu_collapsible'] = filter_var($settings['menu_collapsible'], FILTER_VALIDATE_BOOLEAN);
        }
        $this->merge(['settings' => $settings]);
    }
}
