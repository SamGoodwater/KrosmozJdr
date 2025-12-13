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
use App\Models\Entity\Panoply;
use App\Models\Type\ItemType;
use App\Models\Type\ConsumableType;
use App\Models\Type\ResourceType;
use App\Models\Type\SpellType;

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
                    'dofusdb_id' => $convertedData['dofusdb_id'] ?? $existingClass->dofusdb_id,
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
                    'dofusdb_id' => $convertedData['dofusdb_id'] ?? null,
                    'name' => $convertedData['name'],
                    'description' => $convertedData['description'],
                    'life' => $convertedData['life'],
                    'life_dice' => $convertedData['life_dice'],
                    'specificity' => $convertedData['specificity'],
                    'created_by' => $this->getSystemUserId(),
                ]);
                
                $action = 'created';
            }
            
            // Intégration des relations : sorts de la classe via class_spell
            if (isset($convertedData['spells']) && is_array($convertedData['spells'])) {
                $spellIds = [];
                foreach ($convertedData['spells'] as $spellData) {
                    $spellId = is_array($spellData) ? ($spellData['id'] ?? null) : $spellData;
                    if ($spellId) {
                        // Chercher le sort dans la base par son dofusdb_id ou son nom
                        $spell = Spell::where('dofusdb_id', $spellId)
                            ->orWhere(function($query) use ($spellData) {
                                if (is_array($spellData) && isset($spellData['name'])) {
                                    $query->where('name', $spellData['name']);
                                }
                            })
                            ->first();
                        
                        if ($spell) {
                            $spellIds[] = $spell->id;
                        }
                    }
                }
                
                // Synchroniser les sorts de la classe
                if (!empty($spellIds)) {
                    $class->spells()->sync($spellIds);
                    Log::info('Sorts associés à la classe', [
                        'class_id' => $class->id,
                        'spell_count' => count($spellIds)
                    ]);
                }
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
            
            // Intégration des relations : sorts et ressources du monstre
            // Sorts via creature_spell
            if (isset($convertedData['spells']) && is_array($convertedData['spells'])) {
                $spellIds = [];
                foreach ($convertedData['spells'] as $spellData) {
                    $spellId = is_array($spellData) ? ($spellData['id'] ?? null) : $spellData;
                    if ($spellId) {
                        $spell = Spell::where('dofusdb_id', $spellId)
                            ->orWhere(function($query) use ($spellData) {
                                if (is_array($spellData) && isset($spellData['name'])) {
                                    $query->where('name', $spellData['name']);
                                }
                            })
                            ->first();
                        
                        if ($spell) {
                            $spellIds[] = $spell->id;
                        }
                    }
                }
                
                // Synchroniser les sorts de la créature
                if (!empty($spellIds)) {
                    $creature->spells()->sync($spellIds);
                    Log::info('Sorts associés au monstre', [
                        'creature_id' => $creature->id,
                        'spell_count' => count($spellIds)
                    ]);
                }
            }
            
            // Ressources (drops) via creature_resource
            if (isset($convertedData['drops']) && is_array($convertedData['drops'])) {
                $resourceData = [];
                foreach ($convertedData['drops'] as $resourceItem) {
                    $resourceId = is_array($resourceItem) ? ($resourceItem['id'] ?? null) : $resourceItem;
                    $quantity = is_array($resourceItem) ? ($resourceItem['quantity'] ?? 1) : 1;
                    
                    if ($resourceId) {
                        $resource = Resource::where('dofusdb_id', $resourceId)
                            ->orWhere(function($query) use ($resourceItem) {
                                if (is_array($resourceItem) && isset($resourceItem['name'])) {
                                    $query->where('name', $resourceItem['name']);
                                }
                            })
                            ->first();
                        
                        if ($resource) {
                            $resourceData[$resource->id] = ['quantity' => (string) $quantity];
                        }
                    }
                }
                
                // Synchroniser les ressources de la créature
                if (!empty($resourceData)) {
                    $creature->resources()->sync($resourceData);
                    Log::info('Ressources associées au monstre', [
                        'creature_id' => $creature->id,
                        'resource_count' => count($resourceData)
                    ]);
                }
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
            
            // Intégration des relations : recette (ressources) via item_resource
            // Note: La recette s'applique uniquement aux items (pas aux consumables ni resources)
            if ($targetTable === 'items' && isset($convertedData['recipe']) && is_array($convertedData['recipe'])) {
                $item = Item::find($result['id']);
                
                if ($item) {
                    $resourceData = [];
                    foreach ($convertedData['recipe'] as $recipeItem) {
                        $resourceItem = $recipeItem['resource'] ?? $recipeItem;
                        $resourceId = is_array($resourceItem) ? ($resourceItem['id'] ?? null) : $resourceItem;
                        $quantity = is_array($recipeItem) ? ($recipeItem['quantity'] ?? 1) : 1;
                        
                        if ($resourceId) {
                            $resource = Resource::where('dofusdb_id', $resourceId)
                                ->orWhere(function($query) use ($resourceItem) {
                                    if (is_array($resourceItem) && isset($resourceItem['name'])) {
                                        $query->where('name', $resourceItem['name']);
                                    }
                                })
                                ->first();
                            
                            if ($resource) {
                                $resourceData[$resource->id] = ['quantity' => (string) $quantity];
                            }
                        }
                    }
                    
                    // Synchroniser les ressources de la recette
                    if (!empty($resourceData)) {
                        $item->resources()->sync($resourceData);
                        Log::info('Recette associée à l\'objet', [
                            'item_id' => $item->id,
                            'resource_count' => count($resourceData)
                        ]);
                    }
                }
            }
            
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
                'dofusdb_id' => $convertedData['dofusdb_id'] ?? null,
                'name' => $convertedData['name'],
                'description' => $convertedData['description'],
                'pa' => (string) ($convertedData['cost'] ?? '3'), // Points d'action
                'po' => (string) ($convertedData['range'] ?? '1'), // Portée
                'area' => (int) ($convertedData['area'] ?? 0), // Zone
                'image' => $convertedData['image'] ?? null,
                'effect' => $convertedData['effect'] ?? null,
                'level' => $convertedData['level'] ?? 1, // Niveau par défaut si non fourni
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
            
            // Intégration des relations : monstre invoqué via spell_invocation
            if (isset($convertedData['summon']) && is_array($convertedData['summon'])) {
                $summonData = $convertedData['summon'];
                $summonId = $summonData['id'] ?? null;
                $summonName = is_array($summonData) && isset($summonData['name']) 
                    ? (is_array($summonData['name']) ? ($summonData['name']['fr'] ?? $summonData['name']) : $summonData['name'])
                    : null;
                
                if ($summonId || $summonName) {
                    // Chercher le monstre par son dofusdb_id ou son nom
                    $monster = null;
                    
                    if ($summonId) {
                        $monster = Monster::where('dofusdb_id', $summonId)->first();
                    }
                    
                    if (!$monster && $summonName) {
                        // Chercher par le nom de la créature associée
                        $creature = Creature::where('name', $summonName)->first();
                        if ($creature) {
                            $monster = Monster::where('creature_id', $creature->id)->first();
                        }
                    }
                    
                    if ($monster) {
                        // Synchroniser le monstre invoqué
                        $spell->monsters()->sync([$monster->id]);
                        Log::info('Monstre invoqué associé au sort', [
                            'spell_id' => $spell->id,
                            'monster_id' => $monster->id
                        ]);
                    } else {
                        Log::warning('Monstre invoqué non trouvé pour le sort', [
                            'spell_id' => $spell->id,
                            'summon_dofusdb_id' => $summonId,
                            'summon_name' => $summonName
                        ]);
                    }
                }
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
     * Intégration d'une panoplie dans la base KrosmozJDR
     * 
     * @param array $convertedData Données converties de la panoplie
     * @return array Résultat de l'intégration
     */
    public function integratePanoply(array $convertedData): array
    {
        Log::info('Intégration de panoplie', ['panoply_name' => $convertedData['name']]);
        
        try {
            DB::beginTransaction();
            
            // Recherche d'une panoplie existante par dofusdb_id ou name
            $existingPanoply = null;
            if (!empty($convertedData['dofusdb_id'])) {
                $existingPanoply = Panoply::where('dofusdb_id', $convertedData['dofusdb_id'])->first();
            }
            if (!$existingPanoply) {
                $existingPanoply = Panoply::where('name', $convertedData['name'])->first();
            }
            
            if ($existingPanoply) {
                // Mise à jour de la panoplie existante
                $existingPanoply->update([
                    'dofusdb_id' => $convertedData['dofusdb_id'] ?? $existingPanoply->dofusdb_id,
                    'description' => $convertedData['description'],
                    'bonus' => $convertedData['bonus'],
                    'usable' => $convertedData['usable'] ?? 0,
                    'is_visible' => $convertedData['is_visible'] ?? 'guest',
                ]);
                $panoply = $existingPanoply;
                $action = 'updated';
            } else {
                // Création d'une nouvelle panoplie
                $panoply = Panoply::create([
                    'dofusdb_id' => $convertedData['dofusdb_id'] ?? null,
                    'name' => $convertedData['name'],
                    'description' => $convertedData['description'],
                    'bonus' => $convertedData['bonus'],
                    'usable' => $convertedData['usable'] ?? 0,
                    'is_visible' => $convertedData['is_visible'] ?? 'guest',
                    'created_by' => $this->getSystemUserId(),
                ]);
                $action = 'created';
            }
            
            DB::commit();
            
            Log::info('Panoplie intégrée avec succès', [
                'panoply_id' => $panoply->id,
                'action' => $action
            ]);
            
            return [
                'id' => $panoply->id,
                'action' => $action,
                'data' => [
                    'panoply' => $panoply->toArray()
                ]
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de l\'intégration de panoplie', [
                'panoply_name' => $convertedData['name'] ?? 'Unknown',
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
     * Recherche l'entité existante correspondante avant import
     *
     * @param string $type Type d'entité (class, monster, item, spell)
     * @param array $convertedData Données converties
     * @return array|null
     */
    public function findExistingEntity(string $type, array $convertedData): ?array
    {
        return match ($type) {
            'class' => $this->wrapExistingRecord('classes', Classe::where('name', $convertedData['name'] ?? null)->first()),
            'monster' => $this->findExistingMonster($convertedData),
            'item' => $this->findExistingItem($convertedData),
            'spell' => $this->wrapExistingRecord('spells', Spell::where('name', $convertedData['name'] ?? null)->first()),
            'panoply' => $this->wrapExistingRecord('panoplies', Panoply::where('name', $convertedData['name'] ?? null)->orWhere('dofusdb_id', $convertedData['dofusdb_id'] ?? null)->first()),
            default => null,
        };
    }

    /**
     * Encapsule un enregistrement existant sous un format standard
     */
    private function wrapExistingRecord(string $table, $model): ?array
    {
        if (!$model) {
            return null;
        }

        return [
            'table' => $table,
            'record' => $model->toArray(),
        ];
    }

    /**
     * Recherche d'un monstre/creature existant
     */
    private function findExistingMonster(array $convertedData): ?array
    {
        $creatureName = $convertedData['creatures']['name'] ?? null;

        if (!$creatureName) {
            return null;
        }

        $creature = Creature::where('name', $creatureName)->first();

        if (!$creature) {
            return null;
        }

        $monster = Monster::where('creature_id', $creature->id)->first();

        return [
            'table' => 'creatures',
            'record' => [
                'creature' => $creature->toArray(),
                'monster' => $monster?->toArray(),
            ],
        ];
    }

    /**
     * Recherche d'un objet/consommable/ressource existant
     */
    private function findExistingItem(array $convertedData): ?array
    {
        if (!isset($convertedData['name'], $convertedData['type'], $convertedData['category'])) {
            return null;
        }

        $targetTable = $this->determineItemTargetTable($convertedData['type'], $convertedData['category']);

        $model = match ($targetTable) {
            'items' => Item::where('name', $convertedData['name'])->first(),
            'consumables' => Consumable::where('name', $convertedData['name'])->first(),
            'resources' => Resource::where('name', $convertedData['name'])->first(),
            default => null,
        };

        if (!$model) {
            return null;
        }

        return [
            'table' => $targetTable,
            'record' => $model->toArray(),
        ];
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
            'dofusdb_id' => $convertedData['dofusdb_id'] ?? null,
            'name' => $convertedData['name'],
            'description' => $convertedData['description'],
            'level' => (string) $convertedData['level'],
            'price' => (string) $convertedData['price'],
            'rarity' => $this->convertRarityToInt($convertedData['rarity']),
            'image' => $convertedData['image'] ?? null,
            'effect' => $convertedData['effect'] ?? null,
            'created_by' => $this->getSystemUserId(),
        ];
        
        // Assigner le type de consommable si disponible
        if (isset($convertedData['type_id'])) {
            $consumableType = $this->getConsumableTypeFromDofusdbTypeId($convertedData['type_id']);
            if ($consumableType) {
                $consumableData['consumable_type_id'] = $consumableType->id;
            }
        }
        
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
            'dofusdb_id' => $convertedData['dofusdb_id'] ?? null,
            'name' => $convertedData['name'],
            'description' => $convertedData['description'],
            'level' => (string) $convertedData['level'],
            'price' => (string) $convertedData['price'],
            'rarity' => $this->convertRarityToInt($convertedData['rarity']),
            'image' => $convertedData['image'] ?? null,
            'weight' => $convertedData['weight'] ?? null,
            'created_by' => $this->getSystemUserId(),
        ];
        
        // Assigner le type de ressource si disponible
        if (isset($convertedData['type_id'])) {
            $resourceType = $this->getResourceTypeFromDofusdbTypeId($convertedData['type_id']);
            if ($resourceType) {
                $resourceData['resource_type_id'] = $resourceType->id;
            }
        }
        
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
            'dofusdb_id' => $convertedData['dofusdb_id'] ?? null,
            'name' => $convertedData['name'],
            'description' => $convertedData['description'],
            'level' => (string) $convertedData['level'],
            'price' => (string) $convertedData['price'],
            'rarity' => $this->convertRarityToInt($convertedData['rarity']),
            'image' => $convertedData['image'] ?? null,
            'effect' => $convertedData['effect'] ?? null,
            'bonus' => $convertedData['bonus'] ?? null,
            'created_by' => $this->getSystemUserId(),
        ];
        
        // Assigner le type d'item si disponible
        if (isset($convertedData['type_id'])) {
            $itemType = $this->getItemTypeFromDofusdbTypeId($convertedData['type_id']);
            if ($itemType) {
                $itemData['item_type_id'] = $itemType->id;
            }
        }
        
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
     * Intègre les niveaux d'un sort et assigne les types de sort
     * 
     * @param Spell $spell Sort parent
     * @param array $levels Niveaux à intégrer
     */
    private function integrateSpellLevels(Spell $spell, array $levels): void
    {
        // Détacher les anciens types de sort
        $spell->spellTypes()->detach();
        
        // Analyser les niveaux pour déterminer les types de sort
        $spellTypeIds = $this->determineSpellTypes($spell, $levels);
        
        // Assigner les types de sort
        if (!empty($spellTypeIds)) {
            $spell->spellTypes()->sync($spellTypeIds);
            Log::info('Types de sort assignés', [
                'spell_id' => $spell->id,
                'spell_type_ids' => $spellTypeIds
            ]);
        }
        
        // Log des niveaux intégrés
        foreach ($levels as $levelData) {
            Log::info('Niveau de sort intégré', [
                'spell_id' => $spell->id,
                'level' => $levelData['level'] ?? 'unknown'
            ]);
        }
    }
    
    /**
     * Détermine les types de sort basés sur les effets et caractéristiques
     * 
     * @param Spell $spell Sort à analyser
     * @param array $levels Niveaux du sort
     * @return array IDs des types de sort
     */
    private function determineSpellTypes(Spell $spell, array $levels): array
    {
        $spellTypeIds = [];
        
        // Si le sort a un monstre invoqué, c'est un sort d'invocation
        if ($spell->monsters()->exists()) {
            $invocationType = SpellType::where('name', 'Invocation')->first();
            if ($invocationType) {
                $spellTypeIds[] = $invocationType->id;
            }
        }
        
        // Analyser les effets des niveaux pour déterminer le type
        // Note: Cette logique est basique et peut être améliorée
        $hasHealing = false;
        $hasDamage = false;
        $hasBuff = false;
        $hasDebuff = false;
        
        foreach ($levels as $level) {
            $effects = $level['effects'] ?? [];
            if (is_array($effects)) {
                foreach ($effects as $effect) {
                    $characteristic = $effect['characteristic'] ?? null;
                    
                    // Détection basique des types d'effets
                    if (in_array($characteristic, ['HP', 'Heal'])) {
                        $hasHealing = true;
                    }
                    if (in_array($characteristic, ['Damage', 'CriticalHit'])) {
                        $hasDamage = true;
                    }
                    if (in_array($characteristic, ['Strength', 'Intelligence', 'Agility', 'Wisdom', 'Chance'])) {
                        $hasBuff = true;
                    }
                    if (in_array($characteristic, ['Weakness', 'Reduction'])) {
                        $hasDebuff = true;
                    }
                }
            }
        }
        
        // Assigner les types selon les effets détectés
        if ($hasHealing) {
            $healingType = SpellType::where('name', 'Soin')->first();
            if ($healingType) {
                $spellTypeIds[] = $healingType->id;
            }
        }
        
        if ($hasDamage) {
            $offensiveType = SpellType::where('name', 'Offensif')->first();
            if ($offensiveType) {
                $spellTypeIds[] = $offensiveType->id;
            }
        }
        
        if ($hasBuff) {
            $buffType = SpellType::where('name', 'Buff')->first();
            if ($buffType) {
                $spellTypeIds[] = $buffType->id;
            }
        }
        
        if ($hasDebuff) {
            $debuffType = SpellType::where('name', 'Debuff')->first();
            if ($debuffType) {
                $spellTypeIds[] = $debuffType->id;
            }
        }
        
        // Si aucun type n'a été détecté, assigner "Défensif" par défaut
        if (empty($spellTypeIds)) {
            $defensiveType = SpellType::where('name', 'Défensif')->first();
            if ($defensiveType) {
                $spellTypeIds[] = $defensiveType->id;
            }
        }
        
        return array_unique($spellTypeIds);
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
        
        // Utiliser l'utilisateur système (créé par le seeder)
        $systemUser = User::getSystemUser();
        if ($systemUser) {
            return $systemUser->id;
        }
        
        // Si l'utilisateur système n'existe pas, utiliser le premier admin disponible
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
        throw new \Exception('Aucun utilisateur disponible pour les imports automatiques. Veuillez exécuter le seeder pour créer l\'utilisateur système.');
    }

    /**
     * Récupère un ItemType depuis un typeId DofusDB
     * 
     * @param int|null $typeId Type ID depuis DofusDB
     * @return ItemType|null
     */
    private function getItemTypeFromDofusdbTypeId(?int $typeId): ?ItemType
    {
        if ($typeId === null) {
            return null;
        }

        // Mapping typeId DofusDB -> ItemType name
        $typeMapping = [
            1 => 'Arc',
            2 => 'Bouclier',
            3 => 'Bouclier', // Bouclier aussi
            4 => 'Bâton',
            5 => 'Dague',
            6 => 'Épée',
            7 => 'Marteau',
            8 => 'Pelle',
            9 => 'Anneau',
            10 => 'Amulette',
            11 => 'Ceinture',
            13 => 'Bottes',
            14 => 'Chapeau',
            16 => 'Cape',
            17 => 'Cape', // Cape aussi
            18 => 'Familier',
            19 => 'Hache',
            20 => 'Outil',
        ];

        $typeName = $typeMapping[$typeId] ?? null;
        if ($typeName === null) {
            Log::warning('TypeId DofusDB non mappé pour ItemType', ['type_id' => $typeId]);
            return null;
        }

        return ItemType::where('name', $typeName)->first();
    }

    /**
     * Récupère un ConsumableType depuis un typeId DofusDB
     * 
     * @param int|null $typeId Type ID depuis DofusDB
     * @return ConsumableType|null
     */
    private function getConsumableTypeFromDofusdbTypeId(?int $typeId): ?ConsumableType
    {
        if ($typeId === null) {
            return null;
        }

        // Mapping typeId DofusDB -> ConsumableType name
        $typeMapping = [
            12 => 'Potion', // Potions
            13 => 'Parchemin d\'expérience', // Parchemins d'expérience
            14 => 'Objet de dons', // Objets de dons
        ];

        $typeName = $typeMapping[$typeId] ?? null;
        if ($typeName === null) {
            Log::warning('TypeId DofusDB non mappé pour ConsumableType', ['type_id' => $typeId]);
            return null;
        }

        return ConsumableType::where('name', $typeName)->first();
    }

    /**
     * Récupère un ResourceType depuis un typeId DofusDB
     * 
     * @param int|null $typeId Type ID depuis DofusDB
     * @return ResourceType|null
     */
    private function getResourceTypeFromDofusdbTypeId(?int $typeId): ?ResourceType
    {
        if ($typeId === null) {
            return null;
        }

        // Source of truth DB: resource_types.dofusdb_type_id + decision
        // On enregistre/actualise le type détecté pour la revue UX.
        $resourceType = ResourceType::touchDofusdbType($typeId);

        // Si pas autorisé, on ne force pas l'assignation (mais la ressource peut quand même exister
        // si elle a été importée via un autre chemin). On laisse null pour éviter d'associer à un type bloqué.
        if ($resourceType->decision !== 'allowed') {
            return null;
        }

        return $resourceType;
    }
}
