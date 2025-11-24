<?php

namespace App\Console\Commands;

use App\Services\Scrapping\DataCollect\DataCollectService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Commande de test pour le service DataCollect
 * 
 * Permet de tester la collecte de données depuis l'API DofusDB
 * via l'interface CLI.
 */
class TestDataCollectCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'scrapping:test-datacollect 
                            {--entity= : Type d\'entité à tester (class, monster, item, spell, effect)}
                            {--id= : ID spécifique de l\'entité à tester}
                            {--type= : Type d\'objet pour les items (1-205)}
                            {--limit=5 : Nombre d\'objets à collecter}
                            {--clear-cache : Nettoyer le cache avant les tests}
                            {--detailed : Affichage détaillé des données collectées}';

    /**
     * The console command description.
     */
    protected $description = 'Teste le service DataCollect pour la collecte de données depuis DofusDB';

    /**
     * Execute the console command.
     */
    public function handle(DataCollectService $dataCollectService): int
    {
        $this->info('🧪 Démarrage des tests du service DataCollect...');
        $this->newLine();

        try {
            // Test de la disponibilité de l'API
            $this->testApiAvailability($dataCollectService);

            // Nettoyage du cache si demandé
            if ($this->option('clear-cache')) {
                $this->clearCache($dataCollectService);
            }

            // Tests spécifiques selon les options
            $entity = $this->option('entity');
            $id = $this->option('id');
            $type = $this->option('type');

            if ($entity && $id) {
                $this->testSpecificEntity($dataCollectService, $entity, (int) $id);
            } elseif ($entity) {
                $this->testEntityType($dataCollectService, $entity, $type);
            } else {
                $this->runAllTests($dataCollectService);
            }

            $this->info('✅ Tous les tests sont terminés avec succès !');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Erreur lors des tests : ' . $e->getMessage());
            Log::error('Erreur dans TestDataCollectCommand', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }

    /**
     * Test de la disponibilité de l'API DofusDB
     */
    private function testApiAvailability(DataCollectService $dataCollectService): void
    {
        $this->info('🔍 Test de la disponibilité de l\'API DofusDB...');
        
        try {
            $isAvailable = $dataCollectService->isDofusDbAvailable();
            
            if ($isAvailable) {
                $this->info('✅ API DofusDB disponible');
            } else {
                $this->warn('⚠️  API DofusDB non disponible');
            }
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors du test de disponibilité : ' . $e->getMessage());
        }
        
        $this->newLine();
    }

    /**
     * Nettoyage du cache
     */
    private function clearCache(DataCollectService $dataCollectService): void
    {
        $this->info('🧹 Nettoyage du cache...');
        
        try {
            $clearedCount = $dataCollectService->clearCache();
            $this->info("✅ Cache nettoyé : {$clearedCount} entrées supprimées");
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors du nettoyage du cache : ' . $e->getMessage());
        }
        
        $this->newLine();
    }

    /**
     * Test d'une entité spécifique
     */
    private function testSpecificEntity(DataCollectService $dataCollectService, string $entity, int $id): void
    {
        $this->info("🔍 Test de collecte de l'entité {$entity} avec l'ID {$id}...");
        
        try {
            switch ($entity) {
                case 'class':
                    $data = $dataCollectService->collectClass($id);
                    $this->displayClassData($data);
                    break;
                    
                case 'monster':
                    $data = $dataCollectService->collectMonster($id);
                    $this->displayMonsterData($data);
                    break;
                    
                case 'item':
                    $data = $dataCollectService->collectItem($id);
                    $this->displayItemData($data);
                    break;
                    
                case 'spell':
                    $data = $dataCollectService->collectSpell($id);
                    $this->displaySpellData($data);
                    break;
                    
                case 'effect':
                    $data = $dataCollectService->collectEffect($id);
                    $this->displayEffectData($data);
                    break;
                    
                default:
                    $this->error("❌ Type d'entité non reconnu : {$entity}");
                    return;
            }
            
            $this->info("✅ Entité {$entity} ID {$id} collectée avec succès");
            
        } catch (\Exception $e) {
            $this->error("❌ Erreur lors de la collecte de l'entité {$entity} ID {$id} : " . $e->getMessage());
        }
        
        $this->newLine();
    }

    /**
     * Test d'un type d'entité
     */
    private function testEntityType(DataCollectService $dataCollectService, string $entity, ?string $type): void
    {
        $this->info("🔍 Test de collecte du type d'entité {$entity}...");
        
        try {
            switch ($entity) {
                case 'class':
                    $this->testClassCollection($dataCollectService);
                    break;
                    
                case 'monster':
                    $this->testMonsterCollection($dataCollectService);
                    break;
                    
                case 'item':
                    $this->testItemCollection($dataCollectService, $type);
                    break;
                    
                case 'spell':
                    $this->testSpellCollection($dataCollectService);
                    break;
                    
                case 'effect':
                    $this->testEffectCollection($dataCollectService);
                    break;
                    
                default:
                    $this->error("❌ Type d'entité non reconnu : {$entity}");
                    return;
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Erreur lors du test du type d'entité {$entity} : " . $e->getMessage());
        }
        
        $this->newLine();
    }

    /**
     * Exécution de tous les tests
     */
    private function runAllTests(DataCollectService $dataCollectService): void
    {
        $this->info('🔍 Exécution de tous les tests...');
        
        // Test des classes
        $this->testClassCollection($dataCollectService);
        
        // Test des monstres
        $this->testMonsterCollection($dataCollectService);
        
        // Test des objets
        $this->testItemCollection($dataCollectService);
        
        // Test des sorts
        $this->testSpellCollection($dataCollectService);
        
        // Test des effets
        $this->testEffectCollection($dataCollectService);
    }

    /**
     * Test de collecte des classes
     */
    private function testClassCollection(DataCollectService $dataCollectService): void
    {
        $this->info('🎭 Test de collecte des classes...');
        
        try {
            // Test avec la classe ID 1 (Féca)
            $data = $dataCollectService->collectClass(1);
            $this->displayClassData($data);
            $this->info('✅ Test de collecte des classes réussi');
            
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors du test des classes : ' . $e->getMessage());
        }
    }

    /**
     * Test de collecte des monstres
     */
    private function testMonsterCollection(DataCollectService $dataCollectService): void
    {
        $this->info('🐉 Test de collecte des monstres...');
        
        try {
            // Test avec le monstre ID 31
            $data = $dataCollectService->collectMonster(31);
            $this->displayMonsterData($data);
            $this->info('✅ Test de collecte des monstres réussi');
            
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors du test des monstres : ' . $e->getMessage());
        }
    }

    /**
     * Test de collecte des objets
     */
    private function testItemCollection(DataCollectService $dataCollectService, ?string $type): void
    {
        $this->info('📦 Test de collecte des objets...');
        
        try {
            if ($type) {
                // Test avec un type spécifique
                $data = $dataCollectService->collectItem((int) $type);
                $this->displayItemData($data);
            } else {
                // Test avec une ressource (type 15)
                $data = $dataCollectService->collectItem(15);
                $this->displayItemData($data);
            }
            
            $this->info('✅ Test de collecte des objets réussi');
            
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors du test des objets : ' . $e->getMessage());
        }
    }

    /**
     * Test de collecte des sorts
     */
    private function testSpellCollection(DataCollectService $dataCollectService): void
    {
        $this->info('🔮 Test de collecte des sorts...');
        
        try {
            // Test avec un sort ID 24510
            $data = $dataCollectService->collectSpell(24510);
            $this->displaySpellData($data);
            $this->info('✅ Test de collecte des sorts réussi');
            
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors du test des sorts : ' . $e->getMessage());
        }
    }

    /**
     * Test de collecte des effets
     */
    private function testEffectCollection(DataCollectService $dataCollectService): void
    {
        $this->info('⚡ Test de collecte des effets...');
        
        try {
            // Test avec un effet ID 2
            $data = $dataCollectService->collectEffect(2);
            $this->displayEffectData($data);
            $this->info('✅ Test de collecte des effets réussi');
            
        } catch (\Exception $e) {
            $this->error('❌ Erreur lors du test des effets : ' . $e->getMessage());
        }
    }

    /**
     * Affichage des données de classe
     */
    private function displayClassData(array $data): void
    {
        if ($this->option('detailed')) {
            $this->line('📊 Données de classe collectées :');
            $this->line(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            $this->line("📊 Classe ID: {$data['id']}");
            if (isset($data['description']['fr'])) {
                $this->line("📝 Description: " . substr($data['description']['fr'], 0, 100) . '...');
            }
        }
    }

    /**
     * Affichage des données de monstre
     */
    private function displayMonsterData(array $data): void
    {
        if ($this->option('detailed')) {
            $this->line('📊 Données de monstre collectées :');
            $this->line(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            $this->line("📊 Monstre ID: {$data['id']}");
            if (isset($data['name']['fr'])) {
                $this->line("📝 Nom: {$data['name']['fr']}");
            }
            $this->line("📊 Niveau: {$data['level']}");
        }
    }

    /**
     * Affichage des données d'objet
     */
    private function displayItemData(array $data): void
    {
        if ($this->option('detailed')) {
            $this->line('📊 Données d\'objet collectées :');
            $this->line(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            $this->line("📊 Objet ID: {$data['id']}");
            if (isset($data['name']['fr'])) {
                $this->line("📝 Nom: {$data['name']['fr']}");
            }
            $this->line("📊 Type: {$data['typeId']}");
            $this->line("📊 Niveau: {$data['level']}");
        }
    }

    /**
     * Affichage des données de sort
     */
    private function displaySpellData(array $data): void
    {
        if ($this->option('detailed')) {
            $this->line('📊 Données de sort collectées :');
            $this->line(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            $this->line("📊 Sort ID: {$data['id']}");
            if (isset($data['name']['fr'])) {
                $this->line("📝 Nom: {$data['name']['fr']}");
            }
            $this->line("📊 Niveaux: " . count($data['spellLevels']));
        }
    }

    /**
     * Affichage des données d'effet
     */
    private function displayEffectData(array $data): void
    {
        if ($this->option('detailed')) {
            $this->line('📊 Données d\'effet collectées :');
            $this->line(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            $this->line("📊 Effet ID: {$data['id']}");
            if (isset($data['description']['fr'])) {
                $this->line("📝 Description: " . substr($data['description']['fr'], 0, 100) . '...');
            }
        }
    }
}
