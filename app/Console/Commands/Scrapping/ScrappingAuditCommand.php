<?php

declare(strict_types=1);

namespace App\Console\Commands\Scrapping;

use App\Console\ArtisanExitCode;
use App\Models\DofusdbEffectMapping;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Services\Scrapping\Core\Config\ConfigLoader;
use App\Services\Scrapping\Core\Config\ScrappingMappingValidator;
use Illuminate\Console\Command;

/**
 * Audite le socle de conversion sans collecter ni écrire de données.
 *
 * @example php artisan scrapping:audit --json --fail-on-review
 */
final class ScrappingAuditCommand extends Command
{
    protected $signature = 'scrapping:audit
                            {--source=dofusdb : Source à auditer}
                            {--entity= : Limiter à une entité}
                            {--fail-on-review : Échouer si une formule paramétrable manque}
                            {--json : Produire un rapport JSON}';

    protected $description = 'Audite les mappings et formules du pipeline de scrapping sans écriture';

    public function handle(
        ConfigLoader $loader,
        ScrappingMappingValidator $validator,
        CharacteristicGetterService $characteristics,
    ): int {
        $source = (string) $this->option('source');
        $requestedEntity = trim((string) $this->option('entity'));
        $entities = $requestedEntity !== '' ? [$requestedEntity] : $loader->listEntities($source);

        $entityReports = [];
        $structuralErrors = 0;
        $manualReviews = 0;

        foreach ($entities as $entity) {
            try {
                $config = $loader->loadEntity($source, $entity);
                $mapping = $config['mapping'] ?? [];
                $catalogOnly = (bool) (($config['meta']['catalogOnly'] ?? false) === true);
                // Catalogue-only (ex. monster-race) : pas d'intégration, mapping cible optionnel.
                $errors = $catalogOnly ? [] : $validator->validate($entity, $mapping);
                $reviews = $catalogOnly ? [] : $this->missingFormulaReviews($entity, $mapping, $characteristics);
            } catch (\Throwable $exception) {
                $errors = [['path' => 'config', 'message' => $exception->getMessage()]];
                $reviews = [];
            }

            $structuralErrors += count($errors);
            $manualReviews += count($reviews);
            $entityReports[] = [
                'entity' => $entity,
                'mapping_count' => is_array($mapping ?? null) ? count($mapping) : 0,
                'errors' => $errors,
                'manual_reviews' => $reviews,
            ];
        }

        $missingEffectCharacteristics = 0;
        if ($requestedEntity === '' || $requestedEntity === 'spell') {
            $missingEffectCharacteristics = DofusdbEffectMapping::query()
                ->where('characteristic_source', DofusdbEffectMapping::SOURCE_CHARACTERISTIC)
                ->where(fn ($query) => $query->whereNull('characteristic_key')->orWhere('characteristic_key', ''))
                ->count();
        }
        $manualReviews += $missingEffectCharacteristics;

        $report = [
            'ok' => $structuralErrors === 0 && (! $this->option('fail-on-review') || $manualReviews === 0),
            'source' => $source,
            'summary' => [
                'entities' => count($entityReports),
                'structural_errors' => $structuralErrors,
                'manual_reviews' => $manualReviews,
                'effect_mappings_missing_characteristic' => $missingEffectCharacteristics,
            ],
            'entities' => $entityReports,
        ];

        if ($this->option('json')) {
            $this->line((string) json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            $this->info('Audit du scrapping');
            $this->table(
                ['Entité', 'Mappings', 'Erreurs', 'Revues manuelles'],
                array_map(static fn (array $row): array => [
                    $row['entity'],
                    (string) $row['mapping_count'],
                    (string) count($row['errors']),
                    (string) count($row['manual_reviews']),
                ], $entityReports)
            );
            $this->line("Mappings d'effets sans caractéristique : {$missingEffectCharacteristics}");
        }

        return $report['ok'] ? ArtisanExitCode::SUCCESS : ArtisanExitCode::FAILURE;
    }

    /**
     * @return list<array{path:string,message:string}>
     */
    private function missingFormulaReviews(
        string $entity,
        mixed $mapping,
        CharacteristicGetterService $characteristics
    ): array {
        if (! is_array($mapping)) {
            return [];
        }

        $entityType = $entity === 'breed' ? 'class' : $entity;
        $reviews = [];
        foreach (array_values($mapping) as $index => $rule) {
            if (! is_array($rule)) {
                continue;
            }
            $usesParametricConversion = false;
            $formatters = $rule['formatters'] ?? [];
            if (is_array($formatters)) {
                foreach ($formatters as $formatter) {
                    if (is_array($formatter) && ($formatter['name'] ?? null) === 'convertCharacteristic') {
                        $usesParametricConversion = true;
                        break;
                    }
                }
            }
            if (! $usesParametricConversion) {
                continue;
            }
            $key = $rule['characteristic_key'] ?? null;
            if (! is_string($key) || $key === '' || $characteristics->getConversionFormula($key, $entityType) !== null) {
                continue;
            }
            $reviews[] = [
                'path' => "mapping.{$index}.characteristic_key",
                'message' => "Aucune conversion_formula pour {$key}; la valeur brute sera conservée.",
            ];
        }

        return $reviews;
    }
}
