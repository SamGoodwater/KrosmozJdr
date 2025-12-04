<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Section;
use Illuminate\Validation\Rule;
use App\Enums\PageState;
use App\Enums\Visibility;
use App\Enums\SectionType;

/**
 * FormRequest pour la création d'une section dynamique.
 *
 * Valide les champs principaux d'une section et vérifie l'autorisation via la policy.
 */
class StoreSectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\Section::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $type = $this->input('type');
        
        $rules = [
            'page_id' => ['required', 'exists:pages,id'],
            'order' => ['required', 'integer', 'min:0'],
            'type' => ['required', Rule::enum(SectionType::class)],
            'params' => ['required', 'array'],
            'is_visible' => ['sometimes', Rule::enum(Visibility::class)],
            'state' => ['sometimes', Rule::enum(PageState::class)],
        ];

        // Validation dynamique des params selon le type
        if ($type && $sectionType = SectionType::tryFrom($type)) {
            $paramsRules = $this->getParamsValidationRules($sectionType);
            if (!empty($paramsRules)) {
                // Fusionner les règles de params avec les règles existantes
                $rules = array_merge($rules, $paramsRules);
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
        $data = $this->all();
        
        if (!isset($data['is_visible'])) {
            $this->merge([
                'is_visible' => Visibility::GUEST->value,
            ]);
        }
        
        // Ne pas définir params à un tableau vide si il n'est pas présent
        // Cela permet à la validation de détecter si params est manquant
        // if (!isset($data['params'])) {
        //     $this->merge([
        //         'params' => [],
        //     ]);
        // }
        
        if (!isset($data['state'])) {
            $this->merge([
                'state' => PageState::DRAFT->value,
            ]);
        }
    }
}
