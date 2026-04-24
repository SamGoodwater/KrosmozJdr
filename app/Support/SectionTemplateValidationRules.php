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
                // Permet de créer une section texte vide puis d'éditer le contenu ensuite.
                'data.content' => ['sometimes', 'nullable', 'string'],
                'params.content' => ['sometimes', 'nullable', 'string'],
                'settings.align' => ['sometimes', 'string', Rule::in(['left', 'center', 'right'])],
                'settings.size' => ['sometimes', 'string', Rule::in(['sm', 'md', 'lg', 'xl'])],
                'settings.enableRichReferences' => ['sometimes', 'boolean'],
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
                // Autorise une création "à vide" puis configuration dans l'éditeur.
                'data.src' => ['sometimes', 'nullable', 'string', 'max:2048'],
                'data.type' => ['sometimes', 'nullable', 'string', Rule::in(['youtube', 'vimeo', 'direct'])],
                'params.src' => ['sometimes', 'nullable', 'string', 'max:2048'],
                'params.type' => ['sometimes', 'nullable', 'string', Rule::in(['youtube', 'vimeo', 'direct'])],
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
            SectionType::LEGAL_MARKDOWN => [
                // Accepte les chemins relatifs same-origin (ex: /storage/legal/cgu.md).
                'data.sourceUrl' => ['sometimes', 'nullable', 'string', 'max:2048'],
                'params.sourceUrl' => ['sometimes', 'nullable', 'string', 'max:2048'],
                'data.title' => ['sometimes', 'nullable', 'string', 'max:255'],
                'params.title' => ['sometimes', 'nullable', 'string', 'max:255'],
            ],
            SectionType::CHARACTERISTIC_NORMS => [
                'settings.characteristic_key' => ['sometimes', 'nullable', 'string', 'max:128'],
                'settings.group' => ['sometimes', 'nullable', 'string', Rule::in(['creature', 'object', 'spell'])],
                'settings.entity' => ['sometimes', 'nullable', 'string', 'max:32'],
            ],
            SectionType::CHARACTERISTIC_NORMS_CATALOG => [
                'settings.group' => ['sometimes', 'nullable', 'string', Rule::in(['creature', 'object', 'spell'])],
                'settings.entity' => ['sometimes', 'nullable', 'string', 'max:32'],
                'settings.characteristic_keys' => ['sometimes', 'nullable', 'array'],
                'settings.characteristic_keys.*' => ['string', 'max:128'],
            ],
            SectionType::CHARACTERISTIC_REFERENCE_TABLE => [
                'settings.group' => ['sometimes', 'nullable', 'string', Rule::in(['all', 'creature', 'object', 'spell'])],
                'settings.entity' => ['sometimes', 'nullable', 'string', 'max:32'],
                'settings.search' => ['sometimes', 'nullable', 'string', 'max:255'],
                'settings.sort_by' => ['sometimes', 'nullable', 'string', Rule::in(['group', 'entity', 'name', 'key', 'equipment_max_bonus', 'forgemagie_max'])],
                'settings.sort_dir' => ['sometimes', 'nullable', 'string', Rule::in(['asc', 'desc'])],
                'settings.status_filter' => ['sometimes', 'nullable', 'string', Rule::in(['all', 'a_valider', 'en_cours_de_validation', 'validee'])],
                'settings.show_prices' => ['sometimes', 'boolean'],
                'settings.show_only_with_equipment' => ['sometimes', 'boolean'],
            ],
            default => [],
        };
    }
}
