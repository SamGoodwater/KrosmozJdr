<?php

namespace App\Console\Commands;

use App\Models\Entity\Consumable;
use App\Models\Entity\Item;
use App\Models\Entity\Monster;
use App\Models\Entity\Resource;
use App\Models\Entity\Spell;
use App\Services\Scrapping\Media\ScrappingImageStorageService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Backfill des images en local pour les entités déjà importées.
 *
 * @description
 * Télécharge les images depuis DofusDB et les stocke dans:
 *   storage/app/public/scrapping/images/{entity}/{bucket}/{dofusdb_id}.{ext}
 * puis met à jour le champ `image` avec l'URL publique (/storage/...).
 *
 * @example
 * php artisan scrapping:backfill-images resource --limit=200
 * php artisan scrapping:backfill-images --limit=100 --dry-run
 */
class ScrappingBackfillImagesCommand extends Command
{
    protected $signature = 'scrapping:backfill-images
                            {entity? : resource|item|consumable|spell|monster (vide = tous)}
                            {--limit=0 : Nombre max d\'entités à traiter (0 = illimité)}
                            {--chunk=200 : Taille de chunk pour la pagination}
                            {--force : Re-télécharge même si l\'image locale existe déjà}
                            {--dry-run : N\'écrit rien, n\'effectue pas de téléchargement (prévisualisation)}
                            {--delay-ms=0 : Pause entre téléchargements (ms)}';

    protected $description = 'Télécharge et stocke localement les images des entités existantes (backfill).';

    public function handle(ScrappingImageStorageService $imageStore): int
    {
        $entity = $this->argument('entity') ? (string) $this->argument('entity') : null;
        $entities = $entity ? [$entity] : ['resource', 'item', 'consumable', 'spell', 'monster'];

        $limit = max(0, (int) $this->option('limit'));
        $chunk = max(10, (int) $this->option('chunk'));
        $force = (bool) $this->option('force');
        $dryRun = (bool) $this->option('dry-run');
        $delayMs = max(0, (int) $this->option('delay-ms'));

        $baseUrl = rtrim((string) config('scrapping.data_collect.dofusdb_base_url', 'https://api.dofusdb.fr'), '/');

        $cfg = config('scrapping.images', []);
        if (!(bool) ($cfg['enabled'] ?? false)) {
            $this->warn('SCRAPPING_IMAGES_ENABLED=false → backfill annulé.');
            return Command::SUCCESS;
        }

        if ($force && !$dryRun) {
            // Permet au service de stockage d'écraser une image déjà existante.
            config(['scrapping.images.force_update' => true]);
        }

        $this->info('🖼️  Backfill images (scrapping)');
        $this->line('Entities: ' . implode(', ', $entities));
        $this->line('Base URL: ' . $baseUrl);
        $this->line('Mode: ' . ($dryRun ? 'dry-run' : 'write'));
        $this->newLine();

        $stats = [
            'scanned' => 0,
            'candidates' => 0,
            'downloaded' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => 0,
        ];

        $globalProcessed = 0;

        foreach ($entities as $type) {
            $type = strtolower(trim($type));
            if (!in_array($type, ['resource', 'item', 'consumable', 'spell', 'monster'], true)) {
                $this->error("Type invalide: {$type}");
                return Command::FAILURE;
            }

            $this->info("➡️  {$type}");

            $processOne = function (?string $dofusdbId, ?string $currentImage, string $folder, callable $save) use (
                $baseUrl, $imageStore, $force, $dryRun, $delayMs, &$stats, &$globalProcessed, $limit
            ) {
                $stats['scanned']++;
                $globalProcessed++;

                if ($limit > 0 && $globalProcessed > $limit) {
                    return false; // stop
                }

                if (!$dofusdbId) {
                    $stats['skipped']++;
                    return true;
                }

                $isLocal = $currentImage && str_contains($currentImage, '/storage/scrapping/images/');
                if ($isLocal && !$force) {
                    $stats['skipped']++;
                    return true;
                }

                $remoteUrl = null;
                if ($currentImage && Str::startsWith($currentImage, ['http://', 'https://'])) {
                    $remoteUrl = $currentImage;
                }

                if (!$remoteUrl) {
                    $remoteUrl = $this->guessDofusdbImageUrl($baseUrl, $folder, $dofusdbId);
                }

                if (!$remoteUrl) {
                    $stats['skipped']++;
                    return true;
                }

                $stats['candidates']++;

                if ($dryRun) {
                    $this->line("  - {$folder}#{$dofusdbId} → {$remoteUrl}");
                    return true;
                }

                $storedUrl = $imageStore->storeFromUrl($remoteUrl, $folder, $dofusdbId);
                if (!$storedUrl) {
                    $stats['errors']++;
                    return true;
                }

                $stats['downloaded']++;

                if (!$currentImage || $currentImage !== $storedUrl || $force) {
                    $save($storedUrl);
                    $stats['updated']++;
                }

                if ($delayMs > 0) {
                    usleep($delayMs * 1000);
                }

                return true;
            };

            if ($type === 'resource') {
                Resource::query()
                    ->whereNotNull('dofusdb_id')
                    ->orderBy('id')
                    ->chunkById($chunk, function ($rows) use ($processOne) {
                        foreach ($rows as $r) {
                            $ok = $processOne((string) $r->dofusdb_id, $r->image, 'resources', function ($url) use ($r) {
                                $r->image = $url;
                                $r->save();
                            });
                            if ($ok === false) return false;
                        }
                        return true;
                    });
            }

            if ($type === 'item') {
                Item::query()
                    ->whereNotNull('dofusdb_id')
                    ->orderBy('id')
                    ->chunkById($chunk, function ($rows) use ($processOne) {
                        foreach ($rows as $r) {
                            $ok = $processOne((string) $r->dofusdb_id, $r->image, 'items', function ($url) use ($r) {
                                $r->image = $url;
                                $r->save();
                            });
                            if ($ok === false) return false;
                        }
                        return true;
                    });
            }

            if ($type === 'consumable') {
                Consumable::query()
                    ->whereNotNull('dofusdb_id')
                    ->orderBy('id')
                    ->chunkById($chunk, function ($rows) use ($processOne) {
                        foreach ($rows as $r) {
                            $ok = $processOne((string) $r->dofusdb_id, $r->image, 'consumables', function ($url) use ($r) {
                                $r->image = $url;
                                $r->save();
                            });
                            if ($ok === false) return false;
                        }
                        return true;
                    });
            }

            if ($type === 'spell') {
                Spell::query()
                    ->whereNotNull('dofusdb_id')
                    ->orderBy('id')
                    ->chunkById($chunk, function ($rows) use ($processOne) {
                        foreach ($rows as $r) {
                            $ok = $processOne((string) $r->dofusdb_id, $r->image, 'spells', function ($url) use ($r) {
                                $r->image = $url;
                                $r->save();
                            });
                            if ($ok === false) return false;
                        }
                        return true;
                    });
            }

            if ($type === 'monster') {
                Monster::query()
                    ->whereNotNull('dofusdb_id')
                    ->with('creature:id,image')
                    ->orderBy('id')
                    ->chunkById($chunk, function ($rows) use ($processOne) {
                        foreach ($rows as $m) {
                            $creature = $m->creature;
                            if (!$creature) {
                                continue;
                            }
                            $ok = $processOne((string) $m->dofusdb_id, $creature->image, 'monsters', function ($url) use ($creature) {
                                $creature->image = $url;
                                $creature->save();
                            });
                            if ($ok === false) return false;
                        }
                        return true;
                    });
            }
        }

        $this->newLine();
        $this->info('📊 Résumé');
        foreach ($stats as $k => $v) {
            $this->line(str_pad($k, 12) . ': ' . $v);
        }

        return Command::SUCCESS;
    }

    /**
     * Déduit l'URL d'image DofusDB selon l'entité.
     */
    private function guessDofusdbImageUrl(string $baseUrl, string $folder, string $dofusdbId): ?string
    {
        $id = rawurlencode($dofusdbId);

        return match ($folder) {
            // DofusDB expose items/consumables/resources via /img/items/{id}.png
            'items', 'resources', 'consumables' => "{$baseUrl}/img/items/{$id}.png",
            // Sorts: /img/spells/sort_{id}.png
            'spells' => "{$baseUrl}/img/spells/sort_{$id}.png",
            // Monstres: /img/monsters/{id}.png
            'monsters' => "{$baseUrl}/img/monsters/{$id}.png",
            default => null,
        };
    }
}


