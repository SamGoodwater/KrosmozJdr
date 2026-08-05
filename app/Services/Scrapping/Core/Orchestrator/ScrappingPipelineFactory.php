<?php

declare(strict_types=1);

namespace App\Services\Scrapping\Core\Orchestrator;

use App\Services\Characteristic\Compatibility\CharacteristicCompatibilityService;
use App\Services\Characteristic\Conversion\DofusConversionService;
use App\Services\Characteristic\Formula\CharacteristicFormulaService;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Services\Characteristic\Limit\CharacteristicLimitService;
use App\Services\Scrapping\Catalog\DofusDbItemSuperTypeMappingService;
use App\Services\Scrapping\Catalog\DofusDbItemTypesCatalogService;
use App\Services\Scrapping\Core\Collect\CollectService;
use App\Services\Scrapping\Core\Config\ConfigLoader;
use App\Services\Scrapping\Core\Config\ScrappingMappingValidator;
use App\Services\Scrapping\Core\Conversion\ConversionService;
use App\Services\Scrapping\Core\Conversion\FormatterApplicator;
use App\Services\Scrapping\Core\Conversion\ItemEffectsToBonusConverter;
use App\Services\Scrapping\Core\Conversion\SpellEffects\SpellEffectsConversionService;
use App\Services\Scrapping\Core\Integration\IntegrationService;
use App\Services\Scrapping\Core\Normalizer\SpellGlobalNormalizer;
use App\Services\Scrapping\Core\Norms\NormAwareEntityProcessor;
use App\Services\Scrapping\Core\Relation\RelationResolutionService;

/**
 * Construit le pipeline de scrapping (Collecte → Conversion → Validation → Intégration).
 *
 * Centralise les dépendances pour que le flux soit lisible et que les tests puissent surcharger une étape.
 *
 * @see docs/features/scrapping/README.md
 */
final class ScrappingPipelineFactory
{
    /**
     * Crée un Orchestrator avec les services par défaut (résolus via le conteneur).
     */
    public static function createDefault(): Orchestrator
    {
        $configLoader = app(ConfigLoader::class);
        $conversionService = app(DofusConversionService::class);
        $getter = app(CharacteristicGetterService::class);

        $itemEffectsConverter = new ItemEffectsToBonusConverter($getter, $conversionService, app(CharacteristicCompatibilityService::class));
        $formatterApplicator = new FormatterApplicator(
            $conversionService,
            $getter,
            $itemEffectsConverter,
            null,
            app(DofusDbItemTypesCatalogService::class),
            app(DofusDbItemSuperTypeMappingService::class)
        );

        $orchestrator = new Orchestrator(
            $configLoader,
            app(CollectService::class),
            new ConversionService(
                $configLoader,
                $formatterApplicator,
                $conversionService,
                new ScrappingMappingValidator(
                    $formatterApplicator,
                    $getter,
                    app(CharacteristicFormulaService::class),
                ),
            ),
            app(CharacteristicLimitService::class),
            app(IntegrationService::class),
            app(SpellEffectsConversionService::class),
            null,
            new SpellGlobalNormalizer,
            app(NormAwareEntityProcessor::class)
        );
        $orchestrator->setRelationResolutionService(new RelationResolutionService($orchestrator));

        return $orchestrator;
    }
}
