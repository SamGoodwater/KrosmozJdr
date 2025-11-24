<?php

namespace App\Services\Scrapping\Orchestrator;

use App\Services\Scrapping\DataCollect\DataCollectService;
use App\Services\Scrapping\DataConversion\DataConversionService;
use App\Services\Scrapping\DataIntegration\DataIntegrationService;
use Illuminate\Support\Facades\Log;

/**
 * Service d'orchestration du processus de scrapping
 * 
 * Coordonne l'ensemble du processus de récupération, conversion et intégration
 * des données depuis des sites externes vers KrosmozJDR.
 * 
 * @package App\Services\Scrapping\Orchestrator
 */
class ScrappingOrchestrator
{
    /**
     * Constructeur du service d'orchestration
     */
    public function __construct(
        private DataCollectService $dataCollectService,
        private DataConversionService $dataConversionService,
        private DataIntegrationService $dataIntegrationService
    ) {}

    /**
     * Import d'une classe depuis DofusDB
     * 
     * @param int $dofusdbId ID de la classe dans DofusDB
     * @param array $options Options d'import
     * @return array Résultat de l'import
     */
    public function importClass(int $dofusdbId, array $options = []): array
    {
        Log::info('Début import classe', ['dofusdb_id' => $dofusdbId, 'options' => $options]);
        
        try {
            // 1. Collecte des données depuis DofusDB
            $rawData = $this->dataCollectService->collectClass($dofusdbId);
            
            // 2. Conversion des valeurs selon les caractéristiques KrosmozJDR
            $convertedData = $this->dataConversionService->convertClass($rawData);
            
            // 3. Intégration dans la base KrosmozJDR
            $result = $this->dataIntegrationService->integrateClass($convertedData);
            
            Log::info('Import classe terminé avec succès', ['dofusdb_id' => $dofusdbId]);
            
            return [
                'success' => true,
                'data' => $result,
                'message' => 'Classe importée avec succès'
            ];
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'import de classe', [
                'dofusdb_id' => $dofusdbId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Erreur lors de l\'import de la classe'
            ];
        }
    }

    /**
     * Import d'un monstre depuis DofusDB
     * 
     * @param int $dofusdbId ID du monstre dans DofusDB
     * @param array $options Options d'import
     * @return array Résultat de l'import
     */
    public function importMonster(int $dofusdbId, array $options = []): array
    {
        Log::info('Début import monstre', ['dofusdb_id' => $dofusdbId, 'options' => $options]);
        
        try {
            // 1. Collecte des données depuis DofusDB
            $rawData = $this->dataCollectService->collectMonster($dofusdbId);
            
            // 2. Conversion des valeurs selon les caractéristiques KrosmozJDR
            $convertedData = $this->dataConversionService->convertMonster($rawData);
            
            // 3. Intégration dans la base KrosmozJDR
            $result = $this->dataIntegrationService->integrateMonster($convertedData);
            
            Log::info('Import monstre terminé avec succès', ['dofusdb_id' => $dofusdbId]);
            
            return [
                'success' => true,
                'data' => $result,
                'message' => 'Monstre importé avec succès'
            ];
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'import de monstre', [
                'dofusdb_id' => $dofusdbId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Erreur lors de l\'import du monstre'
            ];
        }
    }

    /**
     * Import d'un objet depuis DofusDB
     * 
     * @param int $dofusdbId ID de l'objet dans DofusDB
     * @param array $options Options d'import
     * @return array Résultat de l'import
     */
    public function importItem(int $dofusdbId, array $options = []): array
    {
        Log::info('Début import objet', ['dofusdb_id' => $dofusdbId, 'options' => $options]);
        
        try {
            // 1. Collecte des données depuis DofusDB
            $rawData = $this->dataCollectService->collectItem($dofusdbId);
            
            // 2. Conversion des valeurs selon les caractéristiques KrosmozJDR
            $convertedData = $this->dataConversionService->convertItem($rawData);
            
            // 3. Intégration dans la base KrosmozJDR
            $result = $this->dataIntegrationService->integrateItem($convertedData);
            
            Log::info('Import objet terminé avec succès', ['dofusdb_id' => $dofusdbId]);
            
            return [
                'success' => true,
                'data' => $result,
                'message' => 'Objet importé avec succès'
            ];
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'import d\'objet', [
                'dofusdb_id' => $dofusdbId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Erreur lors de l\'import de l\'objet'
            ];
        }
    }

    /**
     * Import d'un sort depuis DofusDB
     * 
     * @param int $dofusdbId ID du sort dans DofusDB
     * @param array $options Options d'import
     * @return array Résultat de l'import
     */
    public function importSpell(int $dofusdbId, array $options = []): array
    {
        Log::info('Début import sort', ['dofusdb_id' => $dofusdbId, 'options' => $options]);
        
        try {
            // 1. Collecte des données depuis DofusDB
            $rawData = $this->dataCollectService->collectSpell($dofusdbId);
            
            // 2. Conversion des valeurs selon les caractéristiques KrosmozJDR
            $convertedData = $this->dataConversionService->convertSpell($rawData);
            
            // 3. Intégration dans la base KrosmozJDR
            $result = $this->dataIntegrationService->integrateSpell($convertedData);
            
            Log::info('Import sort terminé avec succès', ['dofusdb_id' => $dofusdbId]);
            
            return [
                'success' => true,
                'data' => $result,
                'message' => 'Sort importé avec succès'
            ];
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'import de sort', [
                'dofusdb_id' => $dofusdbId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Erreur lors de l\'import du sort'
            ];
        }
    }

    /**
     * Import en lot de plusieurs entités
     * 
     * @param array $entities Liste des entités à importer
     * @param array $options Options d'import
     * @return array Résultat de l'import en lot
     */
    public function importBatch(array $entities, array $options = []): array
    {
        Log::info('Début import en lot', ['entities_count' => count($entities), 'options' => $options]);
        
        $results = [];
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($entities as $entity) {
            try {
                $method = 'import' . ucfirst($entity['type']);
                $result = $this->$method($entity['id'], $options);
                
                // Ajouter l'entité au résultat pour l'affichage
                $result['entity'] = $entity;
                
                if ($result['success']) {
                    $successCount++;
                } else {
                    $errorCount++;
                }
                
                $results[] = $result;
                
            } catch (\Exception $e) {
                $errorCount++;
                $results[] = [
                    'success' => false,
                    'error' => $e->getMessage(),
                    'entity' => $entity
                ];
            }
        }
        
        Log::info('Import en lot terminé', [
            'total' => count($entities),
            'success' => $successCount,
            'errors' => $errorCount
        ]);
        
        return [
            'success' => $errorCount === 0,
            'results' => $results,
            'summary' => [
                'total' => count($entities),
                'success' => $successCount,
                'errors' => $errorCount
            ]
        ];
    }
}
