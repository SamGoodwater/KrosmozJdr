<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Section;
use Illuminate\Validation\Rule;
use App\Enums\PageState;
use App\Enums\Visibility;
use App\Enums\SectionType;

/**
 * FormRequest pour la mise à jour d'une section dynamique.
 *
 * Valide les champs modifiables d'une section et vérifie l'autorisation via la policy.
 */
class UpdateSectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $section = $this->route('section');
        return $this->user()?->can('update', $section) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $type = $this->input('type') ?? $this->route('section')?->type;
        
        $rules = [
            'page_id' => ['sometimes', 'exists:pages,id'],
            'order' => ['sometimes', 'integer', 'min:0'],
            'type' => ['sometimes', Rule::enum(SectionType::class)],
            'params' => ['sometimes', 'array'],
            'is_visible' => ['sometimes', Rule::enum(Visibility::class)],
            'state' => ['sometimes', Rule::enum(PageState::class)],
        ];

        // Validation dynamique des params selon le type
        if ($type && $sectionType = SectionType::tryFrom($type)) {
            $paramsRules = $this->getParamsValidationRules($sectionType);
            if (!empty($paramsRules)) {
                $rules['params'] = array_merge(['sometimes', 'array'], $paramsRules);
            }
        }

        return $rules;
    }

    /**
     * Retourne les règles de validation pour les params selon le type de section.
     * 
     * @param SectionType $type Type de section
     * @return array<string, mixed> Règles de validation
     */
    protected function getParamsValidationRules(SectionType $type): array
    {
        return match($type) {
            SectionType::TEXT => [
                'params.content' => ['required', 'string'],
                'params.align' => ['sometimes', 'string', Rule::in(['left', 'center', 'right'])],
                'params.size' => ['sometimes', 'string', Rule::in(['sm', 'md', 'lg', 'xl'])],
            ],
            SectionType::IMAGE => [
                'params.src' => ['required', 'string'],
                'params.alt' => ['required', 'string'],
                'params.caption' => ['sometimes', 'string', 'nullable'],
                'params.align' => ['sometimes', 'string', Rule::in(['left', 'center', 'right'])],
                'params.size' => ['sometimes', 'string', Rule::in(['sm', 'md', 'lg', 'xl', 'full'])],
            ],
            SectionType::GALLERY => [
                'params.images' => ['required', 'array', 'min:1'],
                'params.images.*.src' => ['required', 'string'],
                'params.images.*.alt' => ['required', 'string'],
                'params.images.*.caption' => ['sometimes', 'string', 'nullable'],
                'params.columns' => ['sometimes', 'integer', Rule::in([2, 3, 4])],
                'params.gap' => ['sometimes', 'string', Rule::in(['sm', 'md', 'lg'])],
            ],
            SectionType::VIDEO => [
                'params.src' => ['required', 'string'],
                'params.type' => ['required', 'string', Rule::in(['youtube', 'vimeo', 'direct'])],
                'params.autoplay' => ['sometimes', 'boolean'],
                'params.controls' => ['sometimes', 'boolean'],
            ],
            SectionType::ENTITY_TABLE => [
                'params.entity' => ['required', 'string'],
                'params.filters' => ['sometimes', 'array'],
                'params.columns' => ['sometimes', 'array'],
            ],
        };
    }

    protected function prepareForValidation()
    {
        // Pas de préparation nécessaire pour l'update
        // Les valeurs par défaut sont gérées par le modèle
    }
}
