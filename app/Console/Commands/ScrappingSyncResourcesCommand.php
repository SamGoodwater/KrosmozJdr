<?php

namespace App\Console\Commands;

use App\Models\Type\ResourceType;
use App\Services\Scrapping\DataCollect\DataCollectService;
use App\Services\Scrapping\Orchestrator\ScrappingOrchestrator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Commande d'import "catalogue" des ressources depuis DofusDB.
 *
 * @description
 * Parcourt l'endpoint /items par pagination, filtre sur les typeId "allowed"
 * (table resource_types) et importe les items correspondants dans la table `resources`.
 *
 * Objectif: alimenter la base automatiquement sans devoir importer manuellement
 * des monstres/items.
 *
 * @example
 * php artisan scrapping:sync-resources --limit=100 --max-pages=5
 */
class ScrappingSyncResourcesCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'scrapping:sync-resources
                            {--limit=100 : Taille de page DofusDB (max conseillé: 100)}
                            {--start-skip=0 : Skip initial (pour reprendre une pagination)}
                            {--max-pages=0 : Nombre max de pages (0 = illimité)}
                            {--use-server-filter=1 : Tente de filtrer côté DofusDB (typeId[$in])}
                            {--dry-run : N\'écrit rien en base, affiche uniquement le résumé}
                            {--force-update : Passe l\'option force_update aux imports}
                            {--verbose-ids : Affiche les IDs importés/ignorés}';

    /**
     * The console command description.
     */
    protected $description = 'Synchronise le catalogue des ressources depuis DofusDB (via resource_types.allowed).';

    public function handle(
        DataCollectService $collector,
        ScrappingOrchestrator $orchestrator
    ): int {
        $limit = max(1, min(200, (int) $this->option('limit')));
        $skip = max(0, (int) $this->option('start-skip'));
        $maxPages = max(0, (int) $this->option('max-pages'));
        $useServerFilter = (bool) ((int) $this->option('use-server-filter'));
        $dryRun = (bool) $this->option('dry-run');
        $forceUpdate = (bool) $this->option('force-update');
        $verboseIds = (bool) $this->option('verbose-ids');

        $typeIds = ResourceType::query()
            ->allowed()
            ->whereNotNull('dofusdb_type_id')
            ->pluck('dofusdb_type_id')
            ->map(fn ($v) => (int) $v)
            ->values()
            ->all();

        if (empty($typeIds)) {
            $this->warn("Aucun typeId autorisé trouvé dans resource_types (decision=allowed).");
            $this->line("➡️  Va dans l'UX de scrapping et passe des types en 'allowed', puis relance la commande.");
            return Command::SUCCESS;
        }

        $this->info('🚀 Sync ressources depuis DofusDB');
        $this->line('Types autorisés: ' . implode(', ', $typeIds));
        $this->line("Params: limit={$limit}, start-skip={$skip}, max-pages=" . ($maxPages ?: '∞') . ', dry-run=' . ($dryRun ? 'yes' : 'no'));
        $this->newLine();

        $options = [
            'include_relations' => false,
            'force_update' => $forceUpdate,
            'dry_run' => $dryRun,
        ];

        $page = 0;
        $stats = [
            'seen' => 0,
            'candidates' => 0,
            'imported' => 0,
            'skipped' => 0,
            'errors' => 0,
        ];

        while (true) {
            $page++;
            if ($maxPages > 0 && $page > $maxPages) {
                break;
            }

            $extraQuery = [];
            if ($useServerFilter) {
                // Feathers/Mongo style: typeId[$in][]=15&typeId[$in][]=35
                $extraQuery['typeId[$in][]'] = $typeIds;
            }

            try {
                $response = $collector->collectItemsPage($skip, $limit, $extraQuery);
            } catch (\Throwable $e) {
                // Fallback: si le filtre côté serveur n'est pas supporté, on retombe en mode "scan" local.
                if ($useServerFilter) {
                    $this->warn("Filtre serveur non supporté (ou erreur réseau). Fallback en scan local sans filtre.");
                    Log::warning('scrapping:sync-resources server-filter failed, fallback to local scan', [
                        'error' => $e->getMessage(),
                    ]);
                    $useServerFilter = false;
                    continue;
                }
                $this->error('Erreur DofusDB: ' . $e->getMessage());
                return Command::FAILURE;
            }

            $items = $response['data'] ?? null;
            if (!is_array($items)) {
                $this->warn('Réponse inattendue de DofusDB (clé data manquante).');
                return Command::FAILURE;
            }

            if (count($items) === 0) {
                break;
            }

            foreach ($items as $item) {
                $stats['seen']++;
                $id = isset($item['id']) ? (int) $item['id'] : null;
                $typeId = isset($item['typeId']) ? (int) $item['typeId'] : null;
                if (!$id || !$typeId) {
                    $stats['skipped']++;
                    continue;
                }

                // En fallback (scan local), on filtre côté app
                if (!$useServerFilter && !in_array($typeId, $typeIds, true)) {
                    $stats['skipped']++;
                    continue;
                }

                $stats['candidates']++;

                $result = $orchestrator->importResource($id, $options);
                if (($result['success'] ?? false) === true) {
                    $stats['imported']++;
                    if ($verboseIds) {
                        $this->line("✅ Imported resource dofusdb#{$id}");
                    }
                } else {
                    $stats['errors']++;
                    if ($verboseIds) {
                        $this->line("❌ Error dofusdb#{$id}: " . ($result['error'] ?? 'unknown'));
                    }
                }
            }

            $this->line("Page {$page} OK (skip={$skip}) | candidates={$stats['candidates']} imported={$stats['imported']} errors={$stats['errors']}");

            // Pagination
            $skip += $limit;

            // Si l'API renvoie moins que la limite, fin
            if (count($items) < $limit) {
                break;
            }
        }

        $this->newLine();
        $this->info('📊 Résumé sync ressources');
        $this->line('Seen: ' . $stats['seen']);
        $this->line('Candidates: ' . $stats['candidates']);
        $this->line('Imported: ' . $stats['imported']);
        $this->line('Skipped: ' . $stats['skipped']);
        $this->line('Errors: ' . $stats['errors']);

        return Command::SUCCESS;
    }
}


