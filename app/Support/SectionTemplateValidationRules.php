<?php

namespace App\Support;

use App\Enums\SectionType;
use Illuminate\Validation\Rule;

/**
 * Règles de validation métier des templates de section.
 *
 * Centralise les contraintes spécifiques pour garantir la cohérence
 * entre StoreSectionRequest et UpdateSectionRequest.
 */
class SectionTemplateValidationRules
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public static function forTemplate(SectionType $template): array
    {
        $entityOptions = [
            'spells',
            'monsters',
            'npcs',
            'campaigns',
            'scenarios',
            'shops',
            'breeds',
            'specializations',
            'attributes',
            'capabilities',
            'consumables',
            'items',
            'resources',
            'panoplies',
        ];

        return match ($template) {
            SectionType::TEXT => [
                'data.content' => ['required_without:params.content', 'nullable', 'string'],
                'params.content' => ['required_without:data.content', 'nullable', 'string'],
                'settings.align' => ['sometimes', 'string', Rule::in(['left', 'center', 'right'])],
                'settings.size' => ['sometimes', 'string', Rule::in(['sm', 'md', 'lg', 'xl'])],
            ],
            SectionType::IMAGE => [
                'data.src' => ['nullable', 'string', 'max:2048'],
                'data.alt' => ['nullable', 'string', 'max:255'],
                'params.src' => ['nullable', 'string', 'max:2048'],
                'params.alt' => ['nullable', 'string', 'max:255'],
                'data.caption' => ['sometimes', 'nullable', 'string', 'max:1000'],
                'settings.align' => ['sometimes', 'string', Rule::in(['left', 'center', 'right'])],
                'settings.size' => ['sometimes', 'string', Rule::in(['sm', 'md', 'lg', 'xl', 'full'])],
                'settings.zoom' => ['sometimes', 'integer', 'min:10', 'max:500'],
                'settings.lazyLoad' => ['sometimes', 'boolean'],
                'settings.documentDisplayMode' => ['sometimes', 'string', Rule::in(['preview', 'download'])],
            ],
            SectionType::GALLERY => [
                'data.images' => ['sometimes', 'array'],
                'data.images.*.src' => ['required_with:data.images.*', 'string', 'max:2048'],
                'data.images.*.alt' => ['required_with:data.images.*', 'string', 'max:255'],
                'data.images.*.caption' => ['sometimes', 'nullable', 'string', 'max:1000'],
                'settings.columns' => ['sometimes', 'integer', Rule::in([2, 3, 4])],
                'settings.gap' => ['sometimes', 'string', Rule::in(['sm', 'md', 'lg'])],
            ],
            SectionType::VIDEO => [
                'data.src' => ['required_without:params.src', 'nullable', 'string', 'max:2048'],
                'data.type' => ['required_without:params.type', 'nullable', 'string', Rule::in(['youtube', 'vimeo', 'direct'])],
                'params.src' => ['required_without:data.src', 'nullable', 'string', 'max:2048'],
                'params.type' => ['required_without:data.type', 'nullable', 'string', Rule::in(['youtube', 'vimeo', 'direct'])],
                'settings.autoplay' => ['sometimes', 'boolean'],
                'settings.controls' => ['sometimes', 'boolean'],
                'settings.directVideoDisplayMode' => ['sometimes', 'string', Rule::in(['preview', 'download'])],
            ],
            SectionType::ENTITY_TABLE => [
                'settings.entity' => ['required_without_all:data.entity,params.entity', 'string', Rule::in($entityOptions)],
                'data.entity' => ['required_without_all:settings.entity,params.entity', 'nullable', 'string', Rule::in($entityOptions)],
                'params.entity' => ['required_without_all:settings.entity,data.entity', 'nullable', 'string', Rule::in($entityOptions)],
                'settings.filters' => ['sometimes', 'array'],
                'data.filters' => ['sometimes', 'array'],
                'params.filters' => ['sometimes', 'array'],
                'settings.limit' => ['sometimes', 'integer', 'min:1', 'max:500'],
                'data.limit' => ['sometimes', 'integer', 'min:1', 'max:500'],
                'params.limit' => ['sometimes', 'integer', 'min:1', 'max:500'],
                'data.columns' => ['sometimes', 'array'],
                'params.columns' => ['sometimes', 'array'],
            ],
        };
    }
}

