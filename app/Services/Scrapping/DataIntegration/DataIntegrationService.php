<?php

namespace App\Services\Scrapping\DataIntegration;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Entity\Classe;
use App\Models\Entity\Creature;
use App\Models\Entity\Monster;
use App\Models\Entity\Item;
use App\Models\Entity\Consumable;
use App\Models\Entity\Resource;
use App\Models\Entity\Spell;
use App\Models\Entity\Attribute;
use App\Models\Entity\Capability;

/**
 * Service d'intégration des données dans la structure KrosmozJDR
 * 
 * Gère le mapping de structure entre les données externes et les entités KrosmozJDR,
 * ainsi que la sauvegarde en base de données.
 * 
 * @package App\Services\Scrapping\DataIntegration
 */
class DataIntegrationService
{
    /**
     * Configuration du service
     */
    private array $config;

    /**
     * Constructeur du service d'intégration
     */
    public function __construct()
    {
        $this->config = config('scrapping.data_integration', []);
    }

    /**
     * Intégration d'une classe dans la base KrosmozJDR
     * 
     * @param array $convertedData Données converties de la classe
     * @return array Résultat de l'intégration
     */
    public function integrateClass(array $convertedData): array
    {
        Log::info('Intégration de classe', ['class_name' => $convertedData['name']]);
        
        try {
            DB::beginTransaction();
            
            // Recherche d'une classe existante
            $existingClass = Classe::where('name', $convertedData['name'])->first();
            
            if ($existingClass) {
                // Mise à jour de la classe existante
                $existingClass->update([
                    'description' => $convertedData['description'],
                    'life' => $convertedData['life'],
                    'life_dice' => $convertedData['life_dice'],
                    'specificity' => $convertedData['specificity']
                ]);
                
                $class = $existingClass;
                $action = 'updated';
            } else {
                // Création d'une nouvelle classe
                $class = Classe::create([
                    'name' => $convertedData['name'],
                    'description' => $convertedData['description'],
                    'life' => $convertedData['life'],
                    'life_dice' => $convertedData['life_dice'],
                    'specificity' => $convertedData['specificity']
                ]);
                
                $action = 'created';
            }
            
            DB::commit();
            
            Log::info('Classe intégrée avec succès', [
                'class_id' => $class->id,
                'action' => $action
            ]);
            
            return [
                'id' => $class->id,
                'action' => $action,
                'data' => $class->toArray()
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Erreur lors de l\'intégration de classe', [
                'class_name' => $convertedData['name'],
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Intégration d'un monstre dans la base KrosmozJDR
     * 
     * @param array $convertedData Données converties du monstre
     * @return array Résultat de l'intégration
     */
    public function integrateMonster(array $convertedData): array
    {
        Log::info('Intégration de monstre', ['monster_name' => $convertedData['creatures']['name']]);
        
        try {
            DB::beginTransaction();
            
            $creatureData = $convertedData['creatures'];
            $monsterData = $convertedData['monsters'];
            
            // Recherche d'une créature existante
            $existingCreature = Creature::where('name', $creatureData['name'])->first();
            
            if ($existingCreature) {
                // Mise à jour de la créature existante
                $existingCreature->update([
                    'level' => $creatureData['level'],
                    'life' => $creatureData['life'],
                    'strength' => $creatureData['strength'],
                    'intelligence' => $creatureData['intelligence'],
                    'agility' => $creatureData['agility'],
                    'luck' => $creatureData['luck'],
                    'wisdom' => $creatureData['wisdom'],
                    'chance' => $creatureData['chance']
                ]);
                
                $creature = $existingCreature;
                $creatureAction = 'updated';
            } else {
                // Création d'une nouvelle créature
                $creature = Creature::create([
                    'name' => $creatureData['name'],
                    'level' => $creatureData['level'],
                    'life' => $creatureData['life'],
                    'strength' => $creatureData['strength'],
                    'intelligence' => $creatureData['intelligence'],
                    'agility' => $creatureData['agility'],
                    'luck' => $creatureData['luck'],
                    'wisdom' => $creatureData['wisdom'],
                    'chance' => $creatureData['chance']
                ]);
                
                $creatureAction = 'created';
            }
            
            // Gestion du monstre
            $existingMonster = Monster::where('creature_id', $creature->id)->first();
            
            if ($existingMonster) {
                // Mise à jour du monstre existant
                $existingMonster->update([
                    'size' => $monsterData['size'],
                    'monster_race_id' => $monsterData['monster_race_id']
                ]);
                
                $monster = $existingMonster;
                $monsterAction = 'updated';
            } else {
                // Création d'un nouveau monstre
                $monster = Monster::create([
                    'creature_id' => $creature->id,
                    'size' => $monsterData['size'],
                    'monster_race_id' => $monsterData['monster_race_id']
                ]);
                
                $monsterAction = 'created';
            }
            
            DB::commit();
            
            Log::info('Monstre intégré avec succès', [
                'creature_id' => $creature->id,
                'monster_id' => $monster->id,
                'creature_action' => $creatureAction,
                'monster_action' => $monsterAction
            ]);
            
            return [
                'creature_id' => $creature->id,
                'monster_id' => $monster->id,
                'creature_action' => $creatureAction,
                'monster_action' => $monsterAction,
                'data' => [
                    'creature' => $creature->toArray(),
                    'monster' => $monster->toArray()
                ]
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Erreur lors de l\'intégration de monstre', [
                'monster_name' => $convertedData['creatures']['name'],
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Intégration d'un objet dans la base KrosmozJDR
     * 
     * @param array $convertedData Données converties de l'objet
     * @return array Résultat de l'intégration
     */
    public function integrateItem(array $convertedData): array
    {
        Log::info('Intégration d\'objet', ['item_name' => $convertedData['name']]);
        
        try {
            DB::beginTransaction();
            
            // Détermination du type d'objet et de la table cible
            $targetTable = $this->determineItemTargetTable($convertedData['type'], $convertedData['category']);
            
            $result = $this->integrateItemByType($convertedData, $targetTable);
            
            DB::commit();
            
            Log::info('Objet intégré avec succès', [
                'item_name' => $convertedData['name'],
                'target_table' => $targetTable,
                'result' => $result
            ]);
            
            return $result;
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Erreur lors de l\'intégration d\'objet', [
                'item_name' => $convertedData['name'],
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Intégration d'un sort dans la base KrosmozJDR
     * 
     * @param array $convertedData Données converties du sort
     * @return array Résultat de l'intégration
     */
    public function integrateSpell(array $convertedData): array
    {
        Log::info('Intégration de sort', ['spell_name' => $convertedData['name']]);
        
        try {
            DB::beginTransaction();
            
            // Recherche d'un sort existant
            $existingSpell = Spell::where('name', $convertedData['name'])->first();
            
            if ($existingSpell) {
                // Mise à jour du sort existant
                $existingSpell->update([
                    'description' => $convertedData['description'],
                    'class' => $convertedData['class'],
                    'cost' => $convertedData['cost'],
                    'range' => $convertedData['range'],
                    'area' => $convertedData['area'],
                    'critical_hit' => $convertedData['critical_hit'],
                    'failure' => $convertedData['failure']
                ]);
                
                $spell = $existingSpell;
                $action = 'updated';
            } else {
                // Création d'un nouveau sort
                $spell = Spell::create([
                    'name' => $convertedData['name'],
                    'description' => $convertedData['description'],
                    'class' => $convertedData['class'],
                    'cost' => $convertedData['cost'],
                    'range' => $convertedData['range'],
                    'area' => $convertedData['area'],
                    'critical_hit' => $convertedData['critical_hit'],
                    'failure' => $convertedData['failure']
                ]);
                
                $action = 'created';
            }
            
            // Gestion des niveaux si présents
            if (isset($convertedData['levels']) && is_array($convertedData['levels'])) {
                $this->integrateSpellLevels($spell, $convertedData['levels']);
            }
            
            DB::commit();
            
            Log::info('Sort intégré avec succès', [
                'spell_id' => $spell->id,
                'action' => $action
            ]);
            
            return [
                'id' => $spell->id,
                'action' => $action,
                'data' => $spell->toArray()
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Erreur lors de l\'intégration de sort', [
                'spell_name' => $convertedData['name'],
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Détermine la table cible pour un objet selon son type et sa catégorie
     * 
     * @param string $type Type de l'objet
     * @param string $category Catégorie de l'objet
     * @return string Nom de la table cible
     */
    private function determineItemTargetTable(string $type, string $category): string
    {
        $mapping = $this->config['items_type_mapping'] ?? [];
        
        foreach ($mapping as $itemType => $config) {
            if ($type === $itemType || $category === $itemType) {
                return $config['target_table'];
            }
        }
        
        // Par défaut, utiliser la table items
        return 'items';
    }

    /**
     * Intègre un objet selon son type dans la table appropriée
     * 
     * @param array $convertedData Données converties de l'objet
     * @param string $targetTable Table cible
     * @return array Résultat de l'intégration
     */
    private function integrateItemByType(array $convertedData, string $targetTable): array
    {
        $itemData = [
            'name' => $convertedData['name'],
            'level' => $convertedData['level'],
            'description' => $convertedData['description'],
            'type' => $convertedData['type'],
            'category' => $convertedData['category'],
            'rarity' => $convertedData['rarity'],
            'price' => $convertedData['price']
        ];
        
        switch ($targetTable) {
            case 'consumables':
                return $this->integrateConsumable($itemData);
                
            case 'resources':
                return $this->integrateResource($itemData);
                
            case 'items':
            default:
                return $this->integrateGenericItem($itemData);
        }
    }

    /**
     * Intègre un consommable
     * 
     * @param array $itemData Données de l'objet
     * @return array Résultat de l'intégration
     */
    private function integrateConsumable(array $itemData): array
    {
        $existingConsumable = Consumable::where('name', $itemData['name'])->first();
        
        if ($existingConsumable) {
            $existingConsumable->update($itemData);
            $consumable = $existingConsumable;
            $action = 'updated';
        } else {
            $consumable = Consumable::create($itemData);
            $action = 'created';
        }
        
        return [
            'id' => $consumable->id,
            'action' => $action,
            'table' => 'consumables',
            'data' => $consumable->toArray()
        ];
    }

    /**
     * Intègre une ressource
     * 
     * @param array $itemData Données de l'objet
     * @return array Résultat de l'intégration
     */
    private function integrateResource(array $itemData): array
    {
        $existingResource = Resource::where('name', $itemData['name'])->first();
        
        if ($existingResource) {
            $existingResource->update($itemData);
            $resource = $existingResource;
            $action = 'updated';
        } else {
            $resource = Resource::create($itemData);
            $action = 'created';
        }
        
        return [
            'id' => $resource->id,
            'action' => $action,
            'table' => 'resources',
            'data' => $resource->toArray()
        ];
    }

    /**
     * Intègre un objet générique
     * 
     * @param array $itemData Données de l'objet
     * @return array Résultat de l'intégration
     */
    private function integrateGenericItem(array $itemData): array
    {
        $existingItem = Item::where('name', $itemData['name'])->first();
        
        if ($existingItem) {
            $existingItem->update($itemData);
            $item = $existingItem;
            $action = 'updated';
        } else {
            $item = Item::create($itemData);
            $action = 'created';
        }
        
        return [
            'id' => $item->id,
            'action' => $action,
            'table' => 'items',
            'data' => $item->toArray()
        ];
    }

    /**
     * Intègre les niveaux d'un sort
     * 
     * @param Spell $spell Sort parent
     * @param array $levels Niveaux à intégrer
     */
    private function integrateSpellLevels(Spell $spell, array $levels): void
    {
        // Suppression des anciens niveaux
        $spell->spellTypes()->detach();
        
        // Intégration des nouveaux niveaux
        foreach ($levels as $levelData) {
            // Ici, vous devriez créer ou mettre à jour les types de sort
            // selon votre logique métier
            Log::info('Niveau de sort intégré', [
                'spell_id' => $spell->id,
                'level' => $levelData['level']
            ]);
        }
    }

    /**
     * Vérifie la cohérence des données avant intégration
     * 
     * @param array $data Données à vérifier
     * @param string $entityType Type d'entité
     * @return bool True si les données sont cohérentes
     * @throws \Exception En cas d'incohérence
     */
    public function validateDataConsistency(array $data, string $entityType): bool
    {
        $requiredFields = $this->config['required_fields'][$entityType] ?? [];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new \Exception("Champ requis manquant : {$field} pour l'entité {$entityType}");
            }
        }
        
        return true;
    }

    /**
     * Nettoie les données temporaires après intégration
     * 
     * @param string $entityType Type d'entité
     * @return int Nombre d'éléments nettoyés
     */
    public function cleanupTemporaryData(string $entityType): int
    {
        // Logique de nettoyage des données temporaires
        // selon le type d'entité
        Log::info('Nettoyage des données temporaires', ['entity_type' => $entityType]);
        
        return 0; // À implémenter selon vos besoins
    }
}
