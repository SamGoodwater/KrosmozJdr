<?php

namespace App\Console\Commands;

use App\Services\Scrapping\Catalog\DofusDbItemSuperTypeMappingService;
use App\Services\Scrapping\Catalog\DofusDbItemTypesCatalogService;
use App\Services\Scrapping\Catalog\DofusDbMonsterRacesCatalogService;
use App\Services\Scrapping\V2\Config\CollectAliasResolver;
use App\Services\Scrapping\V2\Config\ConfigLoader;
use App\Services\Scrapping\V2\Orchestrator\Orchestrator;
use Illuminate\Console\Command;

/**
 * Commande V2 refonte scrapping : orchestration Collect → Conversion → Validation → Intégration.
 *
 * Exemple : php artisan scrapping:v2 --collect=monster --id=31
 *           php artisan scrapping:v2 --collect=monster --id=31 --convert --validate --integrate --json
 */
class ScrappingV2Command extends Command
{
    protected $signature = 'scrapping:v2
        {--collect= : Entité à collecter (spell, monster, classe, ressource, item, consumable)}
        {--id= : ID unique pour fetchOne}
        {--name= : Filtre nom (recherche DofusDB)}
        {--type= : Filtre par type ou catégorie (nom, ex: Ressource ou Pierre brute)}
        {--race= : Filtre par race (nom, ex: Bandits d\'Amakna)}
        {--level-min= : Niveau minimum (objets, monstres)}
        {--level-max= : Niveau maximum (objets, monstres)}
        {--limit=0 : Nombre max d\'objets à récupérer (0 = tout)}
        {--offset=0 : Nombre d\'objets à ignorer au début}
        {--convert : Appliquer la conversion après collecte (toujours fait si --validate ou --integrate)}
        {--validate : Valider les données converties (characteristics)}
        {--integrate : Intégrer en base après conversion (écriture ou simulation)}
        {--dry-run : Avec --integrate : simulation, aucune écriture en base}
        {--force-update : Avec --integrate : forcer la mise à jour si l\'entité existe déjà}
        {--replace-existing : Alias de --force-update (remplacer si l\'item existe déjà)}
        {--exclude-from-update= : Avec mise à jour : ne pas écraser ces champs KrosmozJDR (ex: name,image,level)}
        {--ignore-unvalidated : Ignorer les objets dont la race ou le type n\'est pas validé (ex. race absente de la base)}
        {--lang=fr : Langue pour la conversion (pickLang)}
        {--json : Afficher le résultat en JSON}';

    protected $description = 'Refonte scrapping V2 — Collect (spell, monster, classe, ressource, item, consumable) → Conversion → Validation → Intégration. Filtres --type et --race par nom.';

    public function handle(): int
    {
        $collect = $this->option('collect');
        if (!$collect || !is_string($collect)) {
            $aliases = CollectAliasResolver::default()->listAliases();
            $this->error('Option --collect= obligatoire (ex: --collect=monster). Valeurs : ' . implode(', ', $aliases));

            return self::FAILURE;
        }

        $aliasResolver = CollectAliasResolver::default();
        $aliasConfig = $aliasResolver->resolve($collect);

        $source = 'dofusdb';
        $entity = $collect;

        if ($aliasConfig !== null) {
            $source = (string) ($aliasConfig['source'] ?? $source);
            $entity = (string) ($aliasConfig['entity'] ?? $entity);
        }

        try {
            $configLoader = ConfigLoader::default();
            $entities = $configLoader->listEntities($source);
            if (!in_array($entity, $entities, true)) {
                $this->error("Entité '{$entity}' inconnue. Collect : " . implode(', ', $aliasResolver->listAliases()) . ' ; entités config : ' . implode(', ', $entities));

                return self::FAILURE;
            }
        } catch (\Throwable $e) {
            $this->error('Config : ' . $e->getMessage());

            return self::FAILURE;
        }

        $excludeRaw = $this->option('exclude-from-update');
        $excludeFromUpdate = [];
        if (is_string($excludeRaw) && $excludeRaw !== '') {
            $excludeFromUpdate = array_map('trim', array_filter(explode(',', $excludeRaw)));
        }

        $lang = (string) $this->option('lang');
        $options = [
            'convert' => (bool) $this->option('convert'),
            'lang' => $lang,
            'validate' => (bool) $this->option('validate'),
            'integrate' => (bool) $this->option('integrate'),
            'dry_run' => (bool) $this->option('dry-run'),
            'force_update' => (bool) $this->option('force-update') || (bool) $this->option('replace-existing'),
            'exclude_from_update' => $excludeFromUpdate,
            'ignore_unvalidated' => (bool) $this->option('ignore-unvalidated'),
            'limit' => (int) $this->option('limit'),
            'offset' => (int) $this->option('offset'),
        ];

        $orchestrator = Orchestrator::default();

        $idOpt = $this->option('id');
        if ($idOpt !== null && $idOpt !== '') {
            $id = (int) $idOpt;
            $this->info("Run un objet : {$source}/{$entity}/{$id}");
            $result = $orchestrator->runOne($source, $entity, $id, $options);

            return $this->outputResult($result, true, $options);
        }

        $filters = $this->buildFiltersFromOptions($aliasConfig, $lang);
        $this->info('Run liste : ' . $source . '/' . $entity . ' (filtres: ' . json_encode($filters) . ')');
        $result = $orchestrator->runMany($source, $entity, $filters, $options);

        return $this->outputResult($result, false, $options);
    }

    /**
     * Construit le tableau de filtres DofusDB à partir des options de la commande.
     * Correspond aux filtres de base (level, id, race, type) documentés pour l’API DofusDB.
     *
     * @param array{defaultFilter?: array{superTypeGroup: string}}|null $aliasConfig
     * @return array<string, mixed>
     */
    private function buildFiltersFromOptions(?array $aliasConfig, string $lang): array
    {
        $filters = [];

        $name = $this->option('name');
        if ($name !== null && is_string($name) && $name !== '') {
            $filters['name'] = $name;
        }

        $levelMin = $this->option('level-min');
        if ($levelMin !== null && $levelMin !== '' && is_numeric($levelMin)) {
            $filters['levelMin'] = (int) $levelMin;
        }

        $levelMax = $this->option('level-max');
        if ($levelMax !== null && $levelMax !== '' && is_numeric($levelMax)) {
            $filters['levelMax'] = (int) $levelMax;
        }

        $typeOpt = $this->option('type');
        if ($typeOpt !== null && is_string($typeOpt) && $typeOpt !== '') {
            $itemTypesCatalog = app(DofusDbItemTypesCatalogService::class);
            $typeIds = $itemTypesCatalog->resolveTypeIdsByName($typeOpt, $lang);
            if ($typeIds !== []) {
                $superTypeMapping = app(DofusDbItemSuperTypeMappingService::class);
                $excluded = $superTypeMapping->getExcludedTypeIds();
                $filters['typeIds'] = array_values(array_diff($typeIds, $excluded));
            }
        } elseif ($aliasConfig !== null && isset($aliasConfig['defaultFilter']['superTypeGroup'])) {
            $group = (string) $aliasConfig['defaultFilter']['superTypeGroup'];
            $superTypeMapping = app(DofusDbItemSuperTypeMappingService::class);
            $itemTypesCatalog = app(DofusDbItemTypesCatalogService::class);
            $groupConfig = $superTypeMapping->getGroup($group);
            $superTypeIds = $groupConfig['superTypeIds'] ?? [];
            if ($superTypeIds !== []) {
                $typeIds = $itemTypesCatalog->getTypeIdsForSuperTypes($superTypeIds, $lang);
                $excluded = $superTypeMapping->getExcludedTypeIds();
                $filters['typeIds'] = array_values(array_diff($typeIds, $excluded));
            }
        }

        $raceOpt = $this->option('race');
        if ($raceOpt !== null && is_string($raceOpt) && $raceOpt !== '') {
            $monsterRacesCatalog = app(DofusDbMonsterRacesCatalogService::class);
            $raceId = $monsterRacesCatalog->findRaceIdByName($raceOpt, $lang);
            if ($raceId !== null) {
                $filters['raceId'] = $raceId;
            }
        }

        return $filters;
    }

    /**
     * Affiche le résultat de l'orchestrateur et retourne le code de sortie.
     *
     * @param array{lang?: string, validate?: bool, integrate?: bool, dry_run?: bool, force_update?: bool} $options
     */
    private function outputResult(\App\Services\Scrapping\V2\Orchestrator\OrchestratorResult $result, bool $isOne, array $options): int
    {
        if (!$result->isSuccess()) {
            $this->error($result->getMessage());
            foreach ($result->getValidationErrors() as $err) {
                $this->error("  {$err['path']}: {$err['message']}");
            }

            return self::FAILURE;
        }

        if ($this->option('json')) {
            $payload = [
                'success' => true,
                'message' => $result->getMessage(),
                'raw' => $result->getRaw(),
                'converted' => $result->getConverted(),
                'meta' => $result->getMeta(),
            ];
            if ($isOne && $result->getIntegrationResult() !== null) {
                $ir = $result->getIntegrationResult();
                $payload['integration'] = [
                    'creature_id' => $ir->getCreatureId(),
                    'monster_id' => $ir->getMonsterId(),
                    'creature_action' => $ir->getCreatureAction(),
                    'monster_action' => $ir->getMonsterAction(),
                ];
            }
            if (!$isOne && $result->getIntegrationResults() !== null) {
                $payload['integration_results'] = array_map(
                    fn ($ir) => [
                        'creature_id' => $ir->getCreatureId(),
                        'monster_id' => $ir->getMonsterId(),
                        'creature_action' => $ir->getCreatureAction(),
                        'monster_action' => $ir->getMonsterAction(),
                    ],
                    $result->getIntegrationResults()
                );
            }
            $this->line(json_encode($payload, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return self::SUCCESS;
        }

        $this->info($result->getMessage());
        if ($isOne && $result->getConverted() !== null) {
            foreach ($result->getConverted() as $model => $fields) {
                $this->line("  {$model}: " . implode(', ', array_keys($fields)));
            }
        }
        if ($isOne && $result->getIntegrationResult() !== null) {
            $this->info($result->getIntegrationResult()->getMessage());
        }

        return self::SUCCESS;
    }
}
