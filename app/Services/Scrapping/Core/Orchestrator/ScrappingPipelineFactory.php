<?php

declare(strict_types=1);

namespace App\Services\Scrapping\Core\Orchestrator;

use App\Services\Characteristic\Conversion\DofusConversionService;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Services\Characteristic\Limit\CharacteristicLimitService;
use App\Services\Scrapping\Core\Collect\CollectService;
use App\Services\Scrapping\Core\Config\ConfigLoader;
use App\Services\Scrapping\Core\Conversion\ConversionService;
use App\Services\Scrapping\Core\Conversion\FormatterApplicator;
use App\Services\Scrapping\Core\Conversion\ItemEffectsToBonusConverter;
use App\Services\Scrapping\Core\Integration\IntegrationService;
use App\Services\Scrapping\Core\Normalizer\SpellGlobalNormalizer;
use App\Services\Scrapping\Core\Conversion\SpellEffects\SpellEffectsConversionService;
use App\Services\Scrapping\Core\Relation\RelationResolutionService;

/**
 * Construit le pipeline de scrapping (Collecte → Conversion → Validation → Intégration).
 *
 * Centralise les dépendances pour que le flux soit lisible et que les tests puissent surcharger une étape.
 *
 * @see docs/50-Fonctionnalités/Scrapping/SIMPLIFICATIONS_SCRAPPING.md
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

        $itemEffectsConverter = new ItemEffectsToBonusConverter($getter, $conversionService);
        $formatterApplicator = new FormatterApplicator($conversionService, $getter, $itemEffectsConverter);

        $orchestrator = new Orchestrator(
            $configLoader,
            app(CollectService::class),
            new ConversionService($configLoader, $formatterApplicator, $conversionService),
            app(CharacteristicLimitService::class),
            new IntegrationService(),
            app(SpellEffectsConversionService::class),
            null,
            new SpellGlobalNormalizer()
        );
        $orchestrator->setRelationResolutionService(new RelationResolutionService($orchestrator));

        return $orchestrator;
    }
}
