<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\DofusdbEffectMapping;
use App\Services\Characteristic\Getter\CharacteristicGetterService;
use App\Services\Scrapping\Core\Conversion\SpellEffects\DofusdbEffectMappingService;
use App\Services\Scrapping\Http\DofusDbClient;
use Illuminate\Console\Command;

/**
 * Backfill characteristic_key pour les mappings d'effets DofusDB en source "characteristic".
 *
 * Stratégie:
 * - cible les lignes dofusdb_effect_mappings avec characteristic_source=characteristic et characteristic_key vide,
 * - lit characteristic depuis GET /effects/{id},
 * - résout characteristic_key via la BDD des caractéristiques (groupe spell),
 * - fallback sur la config JSON dofusdb_characteristic_to_krosmoz_spell.json,
 * - met à jour la BDD (ou affiche uniquement avec --dry-run).
 */
final class ScrappingEffectsBackfillCharacteristicsCommand extends Command
{
    protected $signature = 'scrapping:effects:backfill-characteristics
                            {--ids= : Liste d\'effectId séparés par des virgules (optionnel)}
                            {--dry-run : N\'écrit pas en base, affiche seulement les corrections proposées}
                            {--lang=fr : Langue API DofusDB}
                            {--skip-cache : Ignore le cache HTTP DofusDB}';

    protected $description = 'Backfill characteristic_key des mappings d\'effets (source characteristic) pour fiabiliser les conversions';
    protected $aliases = ['dofusdb:backfill-effect-characteristics'];

    public function handle(
        DofusDbClient $client,
        CharacteristicGetterService $characteristicGetter,
        DofusdbEffectMappingService $mappingService
    ): int {
        $dryRun = (bool) $this->option('dry-run');
        $lang = (string) $this->option('lang');
        $skipCache = (bool) $this->option('skip-cache');
        $effectIds = $this->parseIdsOption((string) ($this->option('ids') ?? ''));

        $query = DofusdbEffectMapping::query()
            ->where('characteristic_source', DofusdbEffectMapping::SOURCE_CHARACTERISTIC)
            ->where(function ($q): void {
                $q->whereNull('characteristic_key')->orWhere('characteristic_key', '');
            });

        if ($effectIds !== []) {
            $query->whereIn('dofusdb_effect_id', $effectIds);
        }

        /** @var \Illuminate\Support\Collection<int, DofusdbEffectMapping> $rows */
        $rows = $query->orderBy('dofusdb_effect_id')->get();

        if ($rows->isEmpty()) {
            $this->info('Aucune ligne à corriger (characteristic_source=characteristic avec characteristic_key vide).');
            return self::SUCCESS;
        }

        $spellMapFromDb = $characteristicGetter->getDofusdbToCharacteristicKeyMap('spell');
        $spellMapFromConfig = $this->loadSpellCharacteristicMapFromConfig();

        $updated = 0;
        $proposed = 0;
        $unresolved = 0;
        $errors = 0;

        $this->line('Lignes à traiter: ' . $rows->count());

        foreach ($rows as $row) {
            $effectId = (int) $row->dofusdb_effect_id;

            try {
                $effectData = $client->getJson(
                    "https://api.dofusdb.fr/effects/{$effectId}?lang={$lang}",
                    ['skip_cache' => $skipCache]
                );
            } catch (\Throwable $e) {
                $errors++;
                $this->warn("Effect {$effectId}: erreur API ({$e->getMessage()})");
                continue;
            }

            $dofusdbCharacteristicId = isset($effectData['characteristic']) && is_numeric($effectData['characteristic'])
                ? (int) $effectData['characteristic']
                : null;

            if ($dofusdbCharacteristicId === null || $dofusdbCharacteristicId <= 0) {
                $unresolved++;
                $this->warn("Effect {$effectId}: characteristic introuvable dans la réponse API.");
                continue;
            }

            $resolvedKey = $spellMapFromDb[$dofusdbCharacteristicId]
                ?? $spellMapFromConfig[$dofusdbCharacteristicId]
                ?? null;

            if (!is_string($resolvedKey) || $resolvedKey === '') {
                $unresolved++;
                $this->warn("Effect {$effectId}: aucun mapping characteristic_key pour characteristic={$dofusdbCharacteristicId}.");
                continue;
            }

            if ($dryRun) {
                $proposed++;
                $this->line("DRY-RUN effect {$effectId}: characteristic {$dofusdbCharacteristicId} -> {$resolvedKey}");
                continue;
            }

            $row->characteristic_key = $resolvedKey;
            $row->save();
            $updated++;
            $this->info("UPDATED effect {$effectId}: characteristic {$dofusdbCharacteristicId} -> {$resolvedKey}");
        }

        if (!$dryRun && $updated > 0) {
            $mappingService->clearCache();
        }

        $this->newLine();
        $this->table(
            ['Scannés', 'Mis à jour', 'Propositions', 'Non résolus', 'Erreurs API'],
            [[(string) $rows->count(), (string) $updated, (string) $proposed, (string) $unresolved, (string) $errors]]
        );

        return self::SUCCESS;
    }

    /**
     * @return list<int>
     */
    private function parseIdsOption(string $raw): array
    {
        if ($raw === '') {
            return [];
        }

        $ids = [];
        foreach (explode(',', $raw) as $chunk) {
            $trimmed = trim($chunk);
            if ($trimmed === '' || !is_numeric($trimmed)) {
                continue;
            }
            $id = (int) $trimmed;
            if ($id > 0) {
                $ids[] = $id;
            }
        }

        return array_values(array_unique($ids));
    }

    /**
     * @return array<int, string>
     */
    private function loadSpellCharacteristicMapFromConfig(): array
    {
        $path = resource_path('scrapping/config/sources/dofusdb/dofusdb_characteristic_to_krosmoz_spell.json');
        if (!is_file($path)) {
            return [];
        }

        $content = @file_get_contents($path);
        if ($content === false) {
            return [];
        }

        $decoded = json_decode($content, true);
        $mapping = is_array($decoded['mapping'] ?? null) ? $decoded['mapping'] : [];
        $out = [];
        foreach ($mapping as $id => $key) {
            if (is_numeric($id) && is_string($key) && $key !== '') {
                $out[(int) $id] = $key;
            }
        }

        return $out;
    }
}

