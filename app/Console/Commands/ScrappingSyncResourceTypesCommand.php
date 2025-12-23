<?php

namespace App\Console\Commands;

use App\Models\Type\ResourceType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Synchronise les types DofusDB "Ressource" (ItemType) dans la table `resource_types`.
 *
 * @description
 * DofusDB expose les types d'items via /item-types. Pour les ressources, on filtre sur `superTypeId=9`
 * (super-type "Ressource"). Cette commande permet de :
 * - créer/mettre à jour les entrées `resource_types.dofusdb_type_id`,
 * - renseigner un nom lisible (fr),
 * - et définir une décision par défaut (pending/allowed).
 *
 * @example
 * php artisan scrapping:sync-resource-types --decision=pending
 */
class ScrappingSyncResourceTypesCommand extends Command
{
    protected $signature = 'scrapping:sync-resource-types
                            {--decision=pending : Décision par défaut pour les nouveaux types (pending|allowed)}
                            {--limit=100 : Taille de page DofusDB}
                            {--max-pages=0 : Nombre max de pages (0 = illimité)}
                            {--dry-run : N\'écrit rien en base}';

    protected $description = 'Synchronise les types DofusDB "Ressource" (superTypeId=9) dans resource_types.';

    public function handle(): int
    {
        $decision = (string) $this->option('decision');
        if (!in_array($decision, [ResourceType::DECISION_PENDING, ResourceType::DECISION_ALLOWED], true)) {
            $this->error("Option --decision invalide: {$decision}. Valeurs acceptées: pending|allowed");
            return Command::FAILURE;
        }

        $limit = max(1, min(200, (int) $this->option('limit')));
        $maxPages = max(0, (int) $this->option('max-pages'));
        $dryRun = (bool) $this->option('dry-run');

        $baseUrl = (string) config('scrapping.data_collect.dofusdb_base_url', 'https://api.dofusdb.fr');
        $lang = (string) config('scrapping.data_collect.default_language', 'fr');
        $timeout = (int) config('scrapping.data_collect.timeout', 30);
        $ua = (string) config('scrapping.data_collect.user_agent', 'KrosmozJDR-Scrapping/1.0');

        $this->info('🚀 Sync DofusDB item-types (Ressource)');
        $this->line("BaseUrl={$baseUrl} lang={$lang} limit={$limit} max-pages=" . ($maxPages ?: '∞') . ' decision=' . $decision . ' dry-run=' . ($dryRun ? 'yes' : 'no'));
        $this->newLine();

        $skip = 0;
        $page = 0;
        $stats = [
            'seen' => 0,
            'resource_types' => 0,
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => 0,
        ];

        while (true) {
            $page++;
            if ($maxPages > 0 && $page > $maxPages) {
                break;
            }

            $url = rtrim($baseUrl, '/') . '/item-types';
            $query = [
                'lang' => $lang,
                '$limit' => $limit,
                '$skip' => $skip,
            ];

            try {
                $resp = Http::timeout($timeout)
                    ->withHeaders([
                        'User-Agent' => $ua,
                        'Accept' => 'application/json',
                    ])
                    ->get($url, $query);

                if (!$resp->successful()) {
                    $stats['errors']++;
                    $this->error("Erreur DofusDB ({$resp->status()}) sur /item-types");
                    Log::warning('scrapping:sync-resource-types dofusdb failed', [
                        'status' => $resp->status(),
                        'body' => $resp->body(),
                    ]);
                    return Command::FAILURE;
                }

                $payload = $resp->json();
                $data = is_array($payload) ? ($payload['data'] ?? null) : null;
                if (!is_array($data)) {
                    $stats['errors']++;
                    $this->error('Réponse inattendue de DofusDB (clé data manquante).');
                    return Command::FAILURE;
                }

                if (count($data) === 0) {
                    break;
                }

                $received = count($data);
                $total = is_array($payload) && isset($payload['total']) ? (int) $payload['total'] : 0;
                $apiLimit = is_array($payload) && isset($payload['limit']) ? (int) $payload['limit'] : 0;
                $effectiveStep = $apiLimit > 0 ? $apiLimit : $received;

                foreach ($data as $row) {
                    $stats['seen']++;

                    $typeId = isset($row['id']) ? (int) $row['id'] : 0;
                    $superTypeId = isset($row['superTypeId']) ? (int) $row['superTypeId'] : 0;

                    if ($typeId <= 0) {
                        $stats['skipped']++;
                        continue;
                    }

                    // Ressources uniquement: superTypeId=9
                    if ($superTypeId !== 9) {
                        continue;
                    }

                    $stats['resource_types']++;

                    $name = null;
                    if (isset($row['name']) && is_array($row['name'])) {
                        $name = $row['name']['fr'] ?? $row['name'][$lang] ?? null;
                        if (!is_string($name)) {
                            $name = null;
                        }
                    }
                    $label = $name ? "{$name} (DofusDB)" : "DofusDB type #{$typeId}";

                    if ($dryRun) {
                        continue;
                    }

                    /** @var ResourceType $model */
                    $model = ResourceType::firstOrNew(['dofusdb_type_id' => $typeId]);

                    $wasExisting = $model->exists;
                    $oldName = $model->name;
                    $oldDecision = $model->decision;

                    // Toujours améliorer le nom si c'est un placeholder
                    $model->name = $oldName && !str_starts_with($oldName, 'DofusDB type #') ? $oldName : $label;

                    // Ne pas écraser une décision explicitement choisie par l'utilisateur
                    if (!$wasExisting) {
                        $model->decision = $decision;
                    } elseif (!in_array($oldDecision, [ResourceType::DECISION_ALLOWED, ResourceType::DECISION_BLOCKED, ResourceType::DECISION_PENDING], true)) {
                        $model->decision = $decision;
                    }

                    // Valeurs métiers par défaut
                    if (!$wasExisting) {
                        $model->usable = 1;
                        $model->is_visible = 'guest';
                    }

                    $model->save();

                    if ($wasExisting) {
                        $stats['updated']++;
                    } else {
                        $stats['created']++;
                    }
                }

                $this->line("Page {$page} OK (skip={$skip}) | seen={$stats['seen']} resource_types={$stats['resource_types']} created={$stats['created']} updated={$stats['updated']}");

                $skip += $effectiveStep;

                // Arrêt si on a atteint le total (quand fourni par l'API)
                if ($total > 0 && $skip >= $total) {
                    break;
                }

                // Fallback: si l'API ne fournit pas total, on s'arrête quand on reçoit moins que demandé.
                if ($total <= 0 && $received < $limit) {
                    break;
                }
            } catch (\Throwable $e) {
                $stats['errors']++;
                $this->error('Erreur réseau/HTTP: ' . $e->getMessage());
                return Command::FAILURE;
            }
        }

        $this->newLine();
        $this->info('📊 Résumé sync resource-types');
        $this->line('Seen rows: ' . $stats['seen']);
        $this->line('Resource types found: ' . $stats['resource_types']);
        $this->line('Created: ' . $stats['created']);
        $this->line('Updated: ' . $stats['updated']);
        $this->line('Errors: ' . $stats['errors']);

        $this->newLine();
        $this->line('➡️  Étape suivante: valide/autorise les types via l’UX Scrapping, puis relance `scrapping:sync-resources`.');

        return Command::SUCCESS;
    }
}


