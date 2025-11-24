<?php

namespace App\Services\Scrapping\DataIntegration;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;
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
        // Charger la configuration depuis le fichier de config du service
        $this->config = require __DIR__ . '/config.php';
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
            
            // Mapping des noms de colonnes vers la structure de la base
            $creatureAttributes = [
                'name' => $creatureData['name'],
                'level' => (string) $creatureData['level'],
                'life' => (string) $creatureData['life'],
                'strong' => (string) $creatureData['strength'],
                'intel' => (string) $creatureData['intelligence'],
                'agi' => (string) $creatureData['agility'],
                'sagesse' => (string) $creatureData['wisdom'],
                'chance' => (string) $creatureData['chance'],
                'created_by' => $this->getSystemUserId() // Utilisateur système pour imports automatiques
            ];
            
            if ($existingCreature) {
                // Mise à jour de la créature existante
                $existingCreature->update($creatureAttributes);
                
                $creature = $existingCreature;
                $creatureAction = 'updated';
            } else {
                // Création d'une nouvelle créature
                $creature = Creature::create($creatureAttributes);
                
                $creatureAction = 'created';
            }
            
            // Gestion du monstre
            $existingMonster = Monster::where('creature_id', $creature->id)->first();
            
            // Conversion de la taille string en integer
            $sizeInt = $this->convertSizeToInt($monsterData['size'] ?? 'medium');
            
            // Vérifier que la race de monstre existe, sinon mettre à null
            $monsterRaceId = $monsterData['monster_race_id'];
            if ($monsterRaceId !== null) {
                $raceExists = DB::table('monster_races')->where('id', $monsterRaceId)->exists();
                if (!$raceExists) {
                    Log::warning('Race de monstre inexistante, utilisation de null', ['race_id' => $monsterRaceId]);
                    $monsterRaceId = null;
                }
            }
            
            if ($existingMonster) {
                // Mise à jour du monstre existant
                $existingMonster->update([
                    'size' => $sizeInt,
                    'monster_race_id' => $monsterRaceId
                ]);
                
                $monster = $existingMonster;
                $monsterAction = 'updated';
            } else {
                // Création d'un nouveau monstre
                $monster = Monster::create([
                    'creature_id' => $creature->id,
                    'size' => $sizeInt,
                    'monster_race_id' => $monsterRaceId
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
            
            // Nettoyage des doublons dans les autres tables avant intégration
            $this->cleanupDuplicateItems($convertedData['name'], $targetTable);
            
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
     * Nettoie les doublons d'un objet dans les tables autres que la table cible
     * 
     * @param string $itemName Nom de l'objet à nettoyer
     * @param string $targetTable Table cible (où l'objet doit être)
     */
    private function cleanupDuplicateItems(string $itemName, string $targetTable): void
    {
        $tablesToCheck = ['items', 'consumables', 'resources'];
        
        foreach ($tablesToCheck as $table) {
            // Ne pas vérifier la table cible
            if ($table === $targetTable) {
                continue;
            }
            
            $duplicate = null;
            
            switch ($table) {
                case 'items':
                    $duplicate = Item::where('name', $itemName)->first();
                    break;
                case 'consumables':
                    $duplicate = Consumable::where('name', $itemName)->first();
                    break;
                case 'resources':
                    $duplicate = Resource::where('name', $itemName)->first();
                    break;
            }
            
            if ($duplicate) {
                Log::warning('Doublon détecté et supprimé', [
                    'item_name' => $itemName,
                    'duplicate_table' => $table,
                    'duplicate_id' => $duplicate->id,
                    'target_table' => $targetTable
                ]);
                
                $duplicate->delete();
            }
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
            
            // Mapping des données converties vers les colonnes de la table spells
            // cost -> pa (points d'action)
            // range -> po (portée)
            // area -> area (zone)
            $spellData = [
                'name' => $convertedData['name'],
                'description' => $convertedData['description'],
                'pa' => (string) ($convertedData['cost'] ?? '3'), // Points d'action
                'po' => (string) ($convertedData['range'] ?? '1'), // Portée
                'area' => (int) ($convertedData['area'] ?? 0), // Zone
                'created_by' => $this->getSystemUserId(),
            ];
            
            // Recherche d'un sort existant
            $existingSpell = Spell::where('name', $convertedData['name'])->first();
            
            if ($existingSpell) {
                // Mise à jour du sort existant
                $existingSpell->update($spellData);
                $spell = $existingSpell;
                $action = 'updated';
            } else {
                // Création d'un nouveau sort
                $spell = Spell::create($spellData);
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
        $mapping = $this->config['dofusdb_mapping']['items_type_mapping'] ?? [];
        
        // Chercher d'abord par type, puis par catégorie
        foreach ($mapping as $itemType => $config) {
            if ($type === $itemType) {
                return $config['target_table'];
            }
        }
        
        // Si pas trouvé par type, chercher par catégorie
        foreach ($mapping as $itemType => $config) {
            if ($category === $itemType || (isset($config['category']) && $category === $config['category'])) {
                return $config['target_table'];
            }
        }
        
        // Par défaut, utiliser la table items
        Log::warning('Type d\'objet non mappé, utilisation de la table items par défaut', [
            'type' => $type,
            'category' => $category
        ]);
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
        switch ($targetTable) {
            case 'consumables':
                return $this->integrateConsumable($convertedData);
                
            case 'resources':
                return $this->integrateResource($convertedData);
                
            case 'items':
            default:
                return $this->integrateGenericItem($convertedData);
        }
    }

    /**
     * Intègre un consommable
     * 
     * @param array $convertedData Données converties de l'objet
     * @return array Résultat de l'intégration
     */
    private function integrateConsumable(array $convertedData): array
    {
        // Mapping des données vers les colonnes de la table consumables
        $consumableData = [
            'name' => $convertedData['name'],
            'description' => $convertedData['description'],
            'level' => (string) $convertedData['level'],
            'price' => (string) $convertedData['price'],
            'rarity' => $this->convertRarityToInt($convertedData['rarity']),
            'created_by' => $this->getSystemUserId(),
        ];
        
        $existingConsumable = Consumable::where('name', $consumableData['name'])->first();
        
        if ($existingConsumable) {
            $existingConsumable->update($consumableData);
            $consumable = $existingConsumable;
            $action = 'updated';
        } else {
            $consumable = Consumable::create($consumableData);
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
     * @param array $convertedData Données converties de l'objet
     * @return array Résultat de l'intégration
     */
    private function integrateResource(array $convertedData): array
    {
        // Mapping des données vers les colonnes de la table resources
        $resourceData = [
            'name' => $convertedData['name'],
            'description' => $convertedData['description'],
            'level' => (string) $convertedData['level'],
            'price' => (string) $convertedData['price'],
            'rarity' => $this->convertRarityToInt($convertedData['rarity']),
            'created_by' => $this->getSystemUserId(),
        ];
        
        $existingResource = Resource::where('name', $resourceData['name'])->first();
        
        if ($existingResource) {
            $existingResource->update($resourceData);
            $resource = $existingResource;
            $action = 'updated';
        } else {
            $resource = Resource::create($resourceData);
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
     * @param array $convertedData Données converties de l'objet
     * @return array Résultat de l'intégration
     */
    private function integrateGenericItem(array $convertedData): array
    {
        // Mapping des données vers les colonnes de la table items
        $itemData = [
            'name' => $convertedData['name'],
            'description' => $convertedData['description'],
            'level' => (string) $convertedData['level'],
            'price' => (string) $convertedData['price'],
            'rarity' => $this->convertRarityToInt($convertedData['rarity']),
            'created_by' => $this->getSystemUserId(),
        ];
        
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

    /**
     * Convertit une taille string en integer pour la base de données
     * 
     * @param string $sizeString Taille en string (tiny, small, medium, large, huge)
     * @return int Taille en integer (0-4)
     */
    private function convertSizeToInt(string $sizeString): int
    {
        $sizeMap = [
            'tiny' => 0,
            'small' => 1,
            'medium' => 2,
            'large' => 3,
            'huge' => 4,
        ];
        
        return $sizeMap[$sizeString] ?? 2; // Default to medium (2)
    }

    /**
     * Convertit une rareté string en integer pour la base de données
     * 
     * @param string $rarityString Rareté en string (common, uncommon, rare, epic, legendary)
     * @return int Rareté en integer (0-4)
     */
    private function convertRarityToInt(string $rarityString): int
    {
        $rarityMap = [
            'common' => 0,
            'uncommon' => 1,
            'rare' => 2,
            'epic' => 3,
            'legendary' => 4,
        ];
        
        return $rarityMap[$rarityString] ?? 0; // Default to common (0)
    }

    /**
     * Récupère l'ID d'un utilisateur système pour les imports automatiques
     * 
     * @return int ID de l'utilisateur système
     */
    private function getSystemUserId(): int
    {
        // Utiliser l'utilisateur connecté si disponible
        if (auth()->check()) {
            return auth()->id();
        }
        
        // Sinon, utiliser le premier utilisateur admin disponible
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        if ($admin) {
            return $admin->id;
        }
        
        // En dernier recours, utiliser le premier utilisateur disponible
        $user = User::first();
        if ($user) {
            return $user->id;
        }
        
        // Si aucun utilisateur n'existe, on lance une exception
        throw new \Exception('Aucun utilisateur disponible pour les imports automatiques');
    }
}
