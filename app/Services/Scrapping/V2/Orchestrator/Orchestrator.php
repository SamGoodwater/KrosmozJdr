<?php

namespace App\Services\Scrapping\V2\Orchestrator;

use App\Services\Scrapping\V2\Collect\CollectService;
use App\Services\Scrapping\V2\Config\ConfigLoader;
use App\Services\Scrapping\V2\Conversion\ConversionService;
use App\Services\Scrapping\V2\Conversion\DofusDbConversionFormulas;
use App\Services\Scrapping\V2\Conversion\FormatterApplicator;
use App\Services\Scrapping\V2\Integration\IntegrationResult;
use App\Services\Scrapping\V2\Integration\IntegrationService;
use App\Services\Scrapping\V2\Validation\ValidationService;

/**
 * Orchestrateur V2 : enchaîne Collecte → Conversion → Validation → Intégration.
 *
 * Un run peut porter sur un objet (runOne) ou une liste (runMany).
 * Options : lang, validate, integrate, dry_run, force_update.
 */
final class Orchestrator
{
    public function __construct(
        private ConfigLoader $configLoader,
        private CollectService $collectService,
        private ConversionService $conversionService,
        private ValidationService $validationService,
        private IntegrationService $integrationService
    ) {
    }

    /**
     * Crée une instance avec les services par défaut.
     */
    public static function default(): self
    {
        $configLoader = ConfigLoader::default();

        $conversionFormulas = app(DofusDbConversionFormulas::class);

        return new self(
            $configLoader,
            new CollectService($configLoader),
            new ConversionService(
                $configLoader,
                new FormatterApplicator($conversionFormulas),
                $conversionFormulas
            ),
            app(ValidationService::class),
            new IntegrationService()
        );
    }

    /**
     * Options pour runOne / runMany.
     *
     * @param array{
     *   lang?: string,
     *   validate?: bool,
     *   integrate?: bool,
     *   dry_run?: bool,
     *   force_update?: bool
     * } $options
     */
    private function contextFromOptions(array $options): array
    {
        return [
            'lang' => (string) ($options['lang'] ?? 'fr'),
        ];
    }

    private function integrationOptions(array $options): array
    {
        $exclude = $options['exclude_from_update'] ?? [];
        $excludeList = is_array($exclude) ? $exclude : [];

        return [
            'dry_run' => (bool) ($options['dry_run'] ?? false),
            'force_update' => (bool) ($options['force_update'] ?? false),
            'ignore_unvalidated' => (bool) ($options['ignore_unvalidated'] ?? false),
            'exclude_from_update' => $excludeList,
        ];
    }

    /**
     * Exécute le pipeline pour un seul objet (fetchOne → convert → validate → integrate).
     * Si ni convert ni validate ni integrate : retourne les données brutes uniquement.
     *
     * @param array{
     *   convert?: bool,
     *   lang?: string,
     *   validate?: bool,
     *   integrate?: bool,
     *   dry_run?: bool,
     *   force_update?: bool,
     *   ignore_unvalidated?: bool,
     *   exclude_from_update?: list<string>
     * } $options
     */
    public function runOne(string $source, string $entity, int $id, array $options = []): OrchestratorResult
    {
        try {
            $raw = $this->collectService->fetchOne($source, $entity, $id);
            if ($raw === []) {
                return OrchestratorResult::fail("Aucune donnée collectée pour {$source}/{$entity}/{$id}.");
            }

            $doConvert = !empty($options['convert']) || !empty($options['validate']) || !empty($options['integrate']);
            if (!$doConvert) {
                return OrchestratorResult::ok('OK', $raw, null, null, null, null);
            }

            $context = $this->contextFromOptions($options);
            $context['entityType'] = $entity === 'breed' ? 'class' : $entity;
            $converted = $this->conversionService->convert($source, $entity, $raw, $context);

            $entityConfig = $this->configLoader->loadEntity($source, $entity);
            $entityType = (string) ($entityConfig['target']['krosmozEntity'] ?? $entity);

            if (!empty($options['validate'])) {
                $validationResult = $this->validationService->validate($converted, $entityType);
                if (!$validationResult->isValid()) {
                    return OrchestratorResult::fail(
                        'Validation échouée.',
                        $validationResult->getErrors()
                    );
                }
            }

            $integrationResult = null;
            if (!empty($options['integrate'])) {
                $integrationResult = $this->integrationService->integrate(
                    $entityType,
                    $converted,
                    $this->integrationOptions($options)
                );
                if (!$integrationResult->isSuccess()) {
                    return OrchestratorResult::fail($integrationResult->getMessage());
                }
            }

            return OrchestratorResult::ok(
                'OK',
                $raw,
                $converted,
                $integrationResult,
                null,
                null
            );
        } catch (\Throwable $e) {
            return OrchestratorResult::fail($e->getMessage());
        }
    }

    /**
     * Exécute convert → validate → integrate avec des données brutes déjà collectées (sans collecte).
     * Utilisé quand la collecte est faite côté legacy (ex. monster avec spells/drops).
     *
     * @param array<string, mixed> $raw Données brutes déjà récupérées
     * @param array{
     *   convert?: bool,
     *   lang?: string,
     *   validate?: bool,
     *   integrate?: bool,
     *   dry_run?: bool,
     *   force_update?: bool,
     *   ignore_unvalidated?: bool,
     *   exclude_from_update?: list<string>
     * } $options
     */
    public function runOneWithRaw(string $source, string $entity, array $raw, array $options = []): OrchestratorResult
    {
        try {
            $doConvert = !empty($options['convert']) || !empty($options['validate']) || !empty($options['integrate']);
            if (!$doConvert) {
                return OrchestratorResult::ok('OK', $raw, null, null, null, null);
            }

            $context = $this->contextFromOptions($options);
            $context['entityType'] = $entity === 'breed' ? 'class' : $entity;
            $converted = $this->conversionService->convert($source, $entity, $raw, $context);

            $entityConfig = $this->configLoader->loadEntity($source, $entity);
            $entityType = (string) ($entityConfig['target']['krosmozEntity'] ?? $entity);

            if (!empty($options['validate'])) {
                $validationResult = $this->validationService->validate($converted, $entityType);
                if (!$validationResult->isValid()) {
                    return OrchestratorResult::fail(
                        'Validation échouée.',
                        $validationResult->getErrors()
                    );
                }
            }

            $integrationResult = null;
            if (!empty($options['integrate'])) {
                $integrationResult = $this->integrationService->integrate(
                    $entityType,
                    $converted,
                    $this->integrationOptions($options)
                );
                if (!$integrationResult->isSuccess()) {
                    return OrchestratorResult::fail($integrationResult->getMessage());
                }
            }

            return OrchestratorResult::ok(
                'OK',
                $raw,
                $converted,
                $integrationResult,
                null,
                null
            );
        } catch (\Throwable $e) {
            return OrchestratorResult::fail($e->getMessage());
        }
    }

    /**
     * Exécute le pipeline pour une liste (fetchMany → convert/validate/integrate par item).
     *
     * @param array<string, mixed> $filters
     * @param array{
     *   limit?: int,
     *   offset?: int,
     *   lang?: string,
     *   validate?: bool,
     *   integrate?: bool,
     *   dry_run?: bool,
     *   force_update?: bool,
     *   ignore_unvalidated?: bool,
     *   exclude_from_update?: list<string>
     * } $options
     */
    public function runMany(string $source, string $entity, array $filters = [], array $options = []): OrchestratorResult
    {
        try {
            $collectOptions = [
                'limit' => (int) ($options['limit'] ?? 0),
                'offset' => (int) ($options['offset'] ?? 0),
            ];
            $result = $this->collectService->fetchMany($source, $entity, $filters, $collectOptions);
            $items = $result['items'];
            $meta = $result['meta'];

            $doConvert = !empty($options['convert']) || !empty($options['validate']) || !empty($options['integrate']);
            if (!$doConvert) {
                $totalApi = (int) ($meta['total'] ?? count($items));
                $collected = (int) ($meta['collected'] ?? count($items));
                $msg = $totalApi > 0
                    ? sprintf('%d objet(s) collectés (offset=%d, limit=%s, total API: %d)', $collected, $meta['offset'] ?? 0, $meta['limit'] === 0 ? 'tout' : (string) $meta['limit'], $totalApi)
                    : sprintf('%d objet(s) collectés (offset=%d, limit=%s)', $collected, $meta['offset'] ?? 0, $meta['limit'] === 0 ? 'tout' : (string) $meta['limit']);
                return OrchestratorResult::ok(
                    $msg,
                    null,
                    $items,
                    null,
                    null,
                    $meta
                );
            }

            $context = $this->contextFromOptions($options);
            $context['entityType'] = $entity === 'breed' ? 'class' : $entity;
            $entityConfig = $this->configLoader->loadEntity($source, $entity);
            $entityType = (string) ($entityConfig['target']['krosmozEntity'] ?? $entity);

            $convertedList = [];
            $allValidationErrors = [];
            $integrationResults = [];

            foreach ($items as $i => $raw) {
                if (!is_array($raw)) {
                    continue;
                }
                $converted = $this->conversionService->convert($source, $entity, $raw, $context);
                $convertedList[] = $converted;

                if (!empty($options['validate'])) {
                    $validationResult = $this->validationService->validate($converted, $entityType);
                    if (!$validationResult->isValid()) {
                        foreach ($validationResult->getErrors() as $err) {
                            $allValidationErrors[] = [
                                'path' => "item#{$i}.{$err['path']}",
                                'message' => $err['message'],
                            ];
                        }
                    }
                }

                if (!empty($options['integrate']) && empty($entityConfig['meta']['catalogOnly'] ?? false)) {
                    $integrationResults[] = $this->integrationService->integrate(
                        $entityType,
                        $converted,
                        $this->integrationOptions($options)
                    );
                }
            }

            if ($allValidationErrors !== []) {
                return OrchestratorResult::fail(
                    'Validation échouée sur un ou plusieurs objets.',
                    $allValidationErrors
                );
            }

            $integrationResultsOrNull = $integrationResults !== [] ? $integrationResults : null;

            $totalApi = (int) ($meta['total'] ?? count($convertedList));
            $collected = (int) ($meta['collected'] ?? count($convertedList));
            $msg = $totalApi > 0
                ? sprintf('%d objet(s) traités (offset=%d, limit=%s, total API: %d)', $collected, $meta['offset'] ?? 0, $meta['limit'] === 0 ? 'tout' : (string) $meta['limit'], $totalApi)
                : sprintf('%d objet(s) traités (offset=%d, limit=%s)', $collected, $meta['offset'] ?? 0, $meta['limit'] === 0 ? 'tout' : (string) $meta['limit']);
            return OrchestratorResult::ok(
                $msg,
                null,
                $convertedList,
                null,
                $integrationResultsOrNull,
                $meta
            );
        } catch (\Throwable $e) {
            return OrchestratorResult::fail($e->getMessage());
        }
    }
}
