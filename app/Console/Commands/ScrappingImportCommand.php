<?php

namespace App\Console\Commands;

use App\Services\Scrapping\Orchestrator\ScrappingOrchestrator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Commande de production pour importer des données via l'orchestrateur
 * 
 * Permet d'importer des entités depuis DofusDB vers KrosmozJDR
 * en utilisant le workflow complet (collecte → conversion → intégration).
 * 
 * @package App\Console\Commands
 */
class ScrappingImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'scrapping:import 
                            {entity? : Type d\'entité à importer (class, monster, item, spell)}
                            {id? : ID de l\'entité dans DofusDB}
                            {--batch= : Fichier JSON contenant une liste d\'entités à importer}
                            {--skip-cache : Ignorer le cache lors de la collecte}
                            {--force-update : Forcer la mise à jour même si l\'entité existe déjà}
                            {--dry-run : Simuler l\'import sans sauvegarder en base}
                            {--validate-only : Valider uniquement sans sauvegarder}
                            {--detailed : Affichage détaillé des résultats}';

    /**
     * The console command description.
     */
    protected $description = 'Importe des entités depuis DofusDB vers KrosmozJDR via l\'orchestrateur complet';

    /**
     * Execute the console command.
     */
    public function handle(ScrappingOrchestrator $orchestrator): int
    {
        $this->info('🚀 Démarrage de l\'import via l\'orchestrateur...');
        $this->newLine();

        try {
            // Import en lot si un fichier est fourni
            if ($this->option('batch')) {
                return $this->handleBatchImport($orchestrator);
            }

            // Vérifier que les arguments sont fournis pour l'import unique
            if (!$this->argument('entity') || !$this->argument('id')) {
                $this->error('❌ Veuillez spécifier une entité et un ID, ou utiliser --batch pour un import en lot.');
                return Command::FAILURE;
            }

            // Import d'une entité unique
            return $this->handleSingleImport($orchestrator);

        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de l\'import : ' . $e->getMessage());
            Log::error('Erreur dans ScrappingImportCommand', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }

    /**
     * Gère l'import d'une entité unique
     */
    private function handleSingleImport(ScrappingOrchestrator $orchestrator): int
    {
        $entity = $this->argument('entity');
        $id = (int) $this->argument('id');
        $options = $this->extractOptions();

        $this->info("📥 Import de l'entité {$entity} (ID: {$id})...");
        $this->newLine();

        try {
            $method = 'import' . ucfirst($entity);
            
            if (!method_exists($orchestrator, $method)) {
                $this->error("❌ Type d'entité invalide : {$entity}");
                $this->info('Types valides : class, monster, item, spell');
                return Command::FAILURE;
            }

            $result = $orchestrator->$method($id, $options);

            if ($result['success']) {
                $this->info('✅ Import réussi !');
                $this->newLine();
                
                if ($this->option('detailed')) {
                    $this->displayDetailedResult($result);
                } else {
                    $this->displaySummaryResult($result);
                }
                
                return Command::SUCCESS;
            }

            $this->error('❌ Import échoué : ' . ($result['error'] ?? 'Erreur inconnue'));
            return Command::FAILURE;

        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de l\'import : ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Gère l'import en lot depuis un fichier JSON
     */
    private function handleBatchImport(ScrappingOrchestrator $orchestrator): int
    {
        $batchFile = $this->option('batch');
        
        if (!file_exists($batchFile)) {
            $this->error("❌ Fichier introuvable : {$batchFile}");
            return Command::FAILURE;
        }

        $content = file_get_contents($batchFile);
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('❌ Erreur de parsing JSON : ' . json_last_error_msg());
            return Command::FAILURE;
        }

        // Support des deux formats : tableau direct ou objet avec clé "entities"
        $entities = isset($data['entities']) && is_array($data['entities']) ? $data['entities'] : $data;

        if (empty($entities) || !is_array($entities)) {
            $this->error('❌ Le fichier doit contenir un tableau d\'entités ou un objet avec une clé "entities"');
            return Command::FAILURE;
        }

        $this->info("📦 Import en lot de " . count($entities) . " entité(s)...");
        $this->newLine();

        $options = $this->extractOptions();
        $result = $orchestrator->importBatch($entities, $options);

        $this->displayBatchResult($result);

        return $result['success'] ? Command::SUCCESS : Command::FAILURE;
    }

    /**
     * Extrait les options depuis les arguments de la commande
     */
    private function extractOptions(): array
    {
        $options = [];

        if ($this->option('skip-cache')) {
            $options['skip_cache'] = true;
        }

        if ($this->option('force-update')) {
            $options['force_update'] = true;
        }

        if ($this->option('dry-run')) {
            $options['dry_run'] = true;
        }

        if ($this->option('validate-only')) {
            $options['validate_only'] = true;
        }

        return $options;
    }

    /**
     * Affiche un résumé du résultat
     */
    private function displaySummaryResult(array $result): void
    {
        $this->line('📊 Résumé :');
        $this->line('  Message : ' . $result['message']);
        
        if (isset($result['data'])) {
            $data = $result['data'];
            if (isset($data['id'])) {
                $this->line('  ID KrosmozJDR : ' . $data['id']);
            }
            if (isset($data['name'])) {
                $this->line('  Nom : ' . $data['name']);
            }
        }
    }

    /**
     * Affiche le résultat détaillé
     */
    private function displayDetailedResult(array $result): void
    {
        $this->line('📊 Résultat détaillé :');
        $this->line('  Message : ' . $result['message']);
        $this->newLine();

        if (isset($result['data'])) {
            $this->line('📦 Données importées :');
            $this->line(json_encode($result['data'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * Affiche le résultat d'un import en lot
     */
    private function displayBatchResult(array $result): void
    {
        $summary = $result['summary'];
        
        $this->info('📊 Résumé de l\'import en lot :');
        $this->line("  Total : {$summary['total']}");
        $this->line("  ✅ Succès : {$summary['success']}");
        $this->line("  ❌ Erreurs : {$summary['errors']}");
        $this->newLine();

        if ($this->option('detailed') && !empty($result['results'])) {
            $this->line('📋 Détails par entité :');
            $this->newLine();
            
            foreach ($result['results'] as $index => $itemResult) {
                $status = $itemResult['success'] ? '✅' : '❌';
                $entity = $result['results'][$index]['entity'] ?? ['type' => 'unknown', 'id' => 'unknown'];
                $this->line("  {$status} {$entity['type']} #{$entity['id']} : " . ($itemResult['message'] ?? ''));
                
                if (!$itemResult['success'] && isset($itemResult['error'])) {
                    $this->line("     Erreur : {$itemResult['error']}");
                }
            }
        }
    }
}

