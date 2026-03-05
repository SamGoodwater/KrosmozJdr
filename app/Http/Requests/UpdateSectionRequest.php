<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Section;
use Illuminate\Validation\Rule;
use App\Enums\SectionType;
use App\Support\SectionTemplateValidationRules;
use App\Support\SectionTemplatePayloadValidator;
use Illuminate\Validation\Validator;

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
        $template = $this->input('template') ?? $this->input('type') ?? $this->route('section')?->template;
        
        // Convertir le template en string si c'est un enum
        // Si c'est déjà un SectionType, extraire sa valeur
        if ($template instanceof SectionType) {
            $template = $template->value;
        }
        
        $rules = [
            'page_id' => ['sometimes', 'exists:pages,id'],
            'title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'order' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'template' => ['sometimes', Rule::enum(SectionType::class)],
            'type' => ['sometimes', Rule::enum(SectionType::class)],
            'settings' => ['sometimes', 'nullable', 'array'],
            'data' => ['sometimes', 'array'],
            'params' => ['sometimes', 'array'],
            'state' => ['sometimes', 'string', Rule::in([Section::STATE_RAW, Section::STATE_DRAFT, Section::STATE_PLAYABLE, Section::STATE_ARCHIVED])],
            'read_level' => ['sometimes', 'integer', 'min:0', 'max:5'],
            'write_level' => ['sometimes', 'integer', 'min:0', 'max:5', 'gte:read_level'],
        ];

        // Validation dynamique des settings et data selon le template
        // $template est maintenant garanti d'être une string ou null
        if ($template && $sectionType = SectionType::tryFrom($template)) {
            $validationRules = $this->getTemplateValidationRules($sectionType);
            if (!empty($validationRules)) {
                // Rendre toutes les règles conditionnelles pour l'update partiel.
                foreach ($validationRules as $key => $rule) {
                    $rules[$key] = array_merge(['sometimes'], $rule);
                }
            }
        }

        return $rules;
    }

    /**
     * Retourne les règles de validation pour les settings et data selon le template de section.
     * 
     * @param SectionType $template Template de section
     * @return array<string, mixed> Règles de validation
     */
    protected function getTemplateValidationRules(SectionType $template): array
    {
        return SectionTemplateValidationRules::forTemplate($template);
    }

    /**
     * @description
     * Normalise les payloads legacy {type, params} vers le format moderne {template, data}.
     */
    protected function passedValidation(): void
    {
        $type = $this->input('type');
        $template = $this->input('template');
        $params = $this->input('params');
        $data = $this->input('data');

        $this->merge([
            'template' => $template ?? $type,
            'type' => $type ?? $template,
            'data' => $data ?? $params,
            'params' => $params ?? $data,
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $templateRaw = $this->input('template') ?? $this->input('type') ?? $this->route('section')?->template;
            if ($templateRaw instanceof SectionType) {
                $templateRaw = $templateRaw->value;
            }

            $template = is_string($templateRaw) ? SectionType::tryFrom($templateRaw) : null;
            if (!$template) {
                return;
            }

            if ($template === SectionType::IMAGE) {
                foreach (['data.src', 'params.src'] as $key) {
                    $value = $this->input($key);
                    if (!is_string($value) || trim($value) === '') {
                        continue;
                    }
                    $error = SectionTemplatePayloadValidator::validateImageSource($value);
                    if ($error) {
                        $validator->errors()->add($key, $error);
                    }
                }
            }

            if ($template === SectionType::VIDEO) {
                $sectionData = $this->route('section')?->data;
                $sectionVideoType = (is_array($sectionData) && isset($sectionData['type'])) ? $sectionData['type'] : '';
                $dataType = $this->input('data.type') ?? $this->input('params.type') ?? $sectionVideoType;

                $dataSrc = $this->input('data.src');
                if (is_string($dataSrc) && trim($dataSrc) !== '') {
                    $error = SectionTemplatePayloadValidator::validateVideoSource((string) ($this->input('data.type') ?? $dataType), $dataSrc);
                    if ($error) {
                        $validator->errors()->add('data.src', $error);
                    }
                }

                $paramsSrc = $this->input('params.src');
                if (is_string($paramsSrc) && trim($paramsSrc) !== '') {
                    $error = SectionTemplatePayloadValidator::validateVideoSource((string) ($this->input('params.type') ?? $dataType), $paramsSrc);
                    if ($error) {
                        $validator->errors()->add('params.src', $error);
                    }
                }
            }
        });
    }
}
