<?php

namespace App\Services\Scrapping\DataConversion;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

/**
 * Service de conversion des données selon les caractéristiques KrosmozJDR
 * 
 * Convertit les valeurs issues de sites externes selon les règles,
 * formules et limites définies dans la configuration des caractéristiques.
 * 
 * @package App\Services\Scrapping\DataConversion
 */
class DataConversionService
{
    /**
     * Configuration du service
     */
    private array $config;

    /**
     * Configuration des caractéristiques KrosmozJDR
     */
    private array $characteristics;

    /**
     * Constructeur du service de conversion
     */
    public function __construct()
    {
        $this->config = config('scrapping.data_conversion', []);
        $this->characteristics = config('characteristics', []);
    }

    /**
     * Conversion d'une classe selon les caractéristiques KrosmozJDR
     * 
     * @param array $rawData Données brutes de la classe
     * @return array Données converties
     */
    public function convertClass(array $rawData): array
    {
        // Les classes DofusDB n'ont pas de champ 'name' direct
        // On extrait le nom depuis la description multilingue ou on utilise l'ID
        $name = $this->extractMultilingualValue($rawData['name'] ?? null, 'Classe ' . ($rawData['id'] ?? 'Unknown'));
        $description = $this->extractMultilingualValue($rawData['description'] ?? null, '');
        
        // Tronquer la description à 255 caractères (limite de la colonne VARCHAR)
        $description = mb_substr($description, 0, 255);
        
        Log::info('Conversion de classe', ['class_id' => $rawData['id'] ?? 'Unknown', 'class_name' => $name]);
        
        $converted = [
            'dofusdb_id' => (string) ($rawData['id'] ?? ''),
            'name' => $name,
            'description' => $description,
            'life' => $this->convertLife($rawData['life'] ?? 0, 'class'),
            'life_dice' => $rawData['life_dice'] ?? '',
            'specificity' => $rawData['specificity'] ?? ''
        ];
        
        // Préserver les sorts associés (si présents dans rawData)
        if (isset($rawData['spells']) && is_array($rawData['spells'])) {
            $converted['spells'] = $rawData['spells'];
        }
        
        // Validation des valeurs converties
        $this->validateConvertedValues($converted, 'class');
        
        Log::info('Classe convertie avec succès', ['class_name' => $converted['name']]);
        
        return $converted;
    }

    /**
     * Conversion d'un monstre selon les caractéristiques KrosmozJDR
     * 
     * @param array $rawData Données brutes du monstre
     * @return array Données converties
     */
    public function convertMonster(array $rawData): array
    {
        // Extraction du nom multilingue
        $name = $this->extractMultilingualValue($rawData['name'] ?? null, 'Monstre ' . ($rawData['id'] ?? 'Unknown'));
        
        // Extraction des caractéristiques depuis les grades ou les champs directs
        $grade = $this->extractMonsterGrade($rawData);
        
        Log::info('Conversion de monstre', ['monster_id' => $rawData['id'] ?? 'Unknown', 'monster_name' => $name]);
        
        $converted = [
            'creatures' => [
                'name' => $name,
                'level' => $this->convertLevel($grade['level'] ?? $rawData['level'] ?? 1, 'monster'),
                'life' => $this->convertLife($grade['lifePoints'] ?? $rawData['lifePoints'] ?? 0, 'monster'),
                'strength' => $this->convertAttribute($grade['strength'] ?? 0, 'strength', 'monster'),
                'intelligence' => $this->convertAttribute($grade['intelligence'] ?? 0, 'intelligence', 'monster'),
                'agility' => $this->convertAttribute($grade['agility'] ?? 0, 'agility', 'monster'),
                'luck' => $this->convertAttribute($grade['luck'] ?? 0, 'luck', 'monster'),
                'wisdom' => $this->convertAttribute($grade['wisdom'] ?? 0, 'wisdom', 'monster'),
                'chance' => $this->convertAttribute($grade['chance'] ?? 0, 'chance', 'monster'),
                'image' => $rawData['img'] ?? null,
            ],
            'monsters' => [
                'dofusdb_id' => (string) ($rawData['id'] ?? ''),
                'size' => $this->convertSize($rawData['size'] ?? 'medium'),
                'monster_race_id' => $rawData['race'] ?? $rawData['monster_race_id'] ?? null
            ]
        ];
        
        // Préserver les sorts associés (si présents dans rawData)
        if (isset($rawData['spells']) && is_array($rawData['spells'])) {
            $converted['spells'] = $rawData['spells'];
        }
        
        // Préserver les ressources (drops) associées (si présents dans rawData)
        if (isset($rawData['drops']) && is_array($rawData['drops'])) {
            $converted['drops'] = $rawData['drops'];
        }
        
        // Validation des valeurs converties
        $this->validateConvertedValues($converted['creatures'], 'monster');
        
        Log::info('Monstre converti avec succès', ['monster_name' => $converted['creatures']['name']]);
        
        return $converted;
    }
    
    /**
     * Extrait les caractéristiques depuis les grades d'un monstre
     * 
     * @param array $rawData Données brutes du monstre
     * @return array Caractéristiques extraites
     */
    private function extractMonsterGrade(array $rawData): array
    {
        // Si des grades sont présents, on prend le premier (grade 1)
        if (isset($rawData['grades']) && is_array($rawData['grades']) && !empty($rawData['grades'])) {
            $grade = $rawData['grades'][0];
            return [
                'level' => $grade['level'] ?? null,
                'lifePoints' => $grade['lifePoints'] ?? null,
                'strength' => $grade['strength'] ?? null,
                'intelligence' => $grade['intelligence'] ?? null,
                'agility' => $grade['agility'] ?? null,
                'wisdom' => $grade['wisdom'] ?? null,
                'chance' => $grade['chance'] ?? null,
                'luck' => null, // Pas de luck dans les grades
            ];
        }
        
        // Sinon, on retourne un tableau vide pour utiliser les valeurs par défaut
        return [];
    }

    /**
     * Conversion d'un objet selon les caractéristiques KrosmozJDR
     * 
     * @param array $rawData Données brutes de l'objet
     * @return array Données converties
     */
    public function convertItem(array $rawData): array
    {
        // Extraction du nom et de la description depuis les objets multilingues
        $name = $this->extractMultilingualValue($rawData['name'] ?? null, 'Objet ' . ($rawData['id'] ?? 'Unknown'));
        $description = $this->extractMultilingualValue($rawData['description'] ?? null, '');
        
        // Tronquer la description à 255 caractères si nécessaire
        $description = mb_substr($description, 0, 255);
        
        // Extraction et mapping du type depuis typeId
        $typeId = $rawData['typeId'] ?? null;
        $typeMapping = $this->mapItemTypeId($typeId);
        
        Log::info('Conversion d\'objet', [
            'item_id' => $rawData['id'] ?? 'Unknown',
            'item_name' => $name,
            'type_id' => $typeId,
            'mapped_type' => $typeMapping['type'],
            'mapped_category' => $typeMapping['category']
        ]);
        
        $converted = [
            'dofusdb_id' => (string) ($rawData['id'] ?? ''),
            'name' => $name,
            'level' => $this->convertLevel($rawData['level'] ?? 1, 'item'),
            'description' => $description,
            'type' => $typeMapping['type'],
            'category' => $typeMapping['category'],
            'type_id' => $typeId, // Conserver le typeId pour référence
            'rarity' => $this->convertRarity($rawData['rarity'] ?? 'common'),
            'price' => $this->convertPrice($rawData['price'] ?? 0),
            'image' => $rawData['img'] ?? null,
            'effect' => $this->convertEffects($rawData['effects'] ?? []),
            'bonus' => $this->convertBonus($rawData['effects'] ?? []),
        ];
        
        // Préserver la recette (ressources) si présente dans rawData
        if (isset($rawData['recipe']) && is_array($rawData['recipe'])) {
            $converted['recipe'] = $rawData['recipe'];
        }
        
        // Validation des valeurs converties
        $this->validateConvertedValues($converted, 'item');
        
        Log::info('Objet converti avec succès', ['item_name' => $converted['name']]);
        
        return $converted;
    }

    /**
     * Conversion d'une panoplie selon les caractéristiques KrosmozJDR
     * 
     * @param array $rawData Données brutes de la panoplie
     * @return array Données converties
     */
    public function convertPanoply(array $rawData): array
    {
        $name = $this->extractMultilingualValue($rawData['name'] ?? null, 'Panoplie ' . ($rawData['id'] ?? 'Unknown'));
        $description = $this->extractMultilingualValue($rawData['description'] ?? null, '');
        
        // Tronquer la description à 255 caractères si nécessaire
        $description = mb_substr($description, 0, 255);
        
        // Convertir les effets en bonus (format texte)
        $bonus = $this->convertPanoplyEffects($rawData['effects'] ?? []);
        
        // Tronquer le bonus à 255 caractères (limite VARCHAR)
        $bonus = mb_substr($bonus, 0, 255);
        
        Log::info('Conversion de panoplie', [
            'panoply_id' => $rawData['id'] ?? 'Unknown',
            'panoply_name' => $name
        ]);
        
        $converted = [
            'dofusdb_id' => (string) ($rawData['id'] ?? ''),
            'name' => $name,
            'description' => $description,
            'bonus' => $bonus,
            'usable' => $rawData['usable'] ?? 0,
            'is_visible' => $rawData['is_visible'] ?? 'guest',
        ];
        
        // Préserver les items associés (si présents dans rawData)
        if (isset($rawData['items']) && is_array($rawData['items'])) {
            $converted['items'] = $rawData['items'];
        }
        if (isset($rawData['item_ids']) && is_array($rawData['item_ids'])) {
            $converted['item_ids'] = $rawData['item_ids'];
        }
        
        // Validation des valeurs converties
        $this->validateConvertedValues($converted, 'panoply');
        
        Log::info('Panoplie convertie avec succès', ['panoply_name' => $converted['name']]);
        
        return $converted;
    }

    /**
     * Convertit les effets de panoplie en texte de bonus
     * 
     * @param array $effects Tableau de tableaux d'effets (par nombre d'items)
     * @return string Description textuelle des bonus
     */
    private function convertPanoplyEffects(array $effects): string
    {
        if (empty($effects)) {
            return '';
        }
        
        $bonusDescriptions = [];
        foreach ($effects as $index => $effectGroup) {
            if (empty($effectGroup) || !is_array($effectGroup)) {
                continue;
            }
            
            $itemCount = $index + 1; // Le premier groupe (index 0) = 1 item, etc.
            $effectTexts = [];
            
            foreach ($effectGroup as $effect) {
                if (!is_array($effect)) {
                    continue;
                }
                
                // Extraire les informations de l'effet
                $effectId = $effect['effectId'] ?? null;
                $from = $effect['from'] ?? 0;
                $to = $effect['to'] ?? 0;
                $characteristic = $effect['characteristic'] ?? null;
                
                // Construire une description basique
                if ($from > 0 || $to > 0) {
                    $range = $from === $to ? (string)$from : "{$from}-{$to}";
                    $effectTexts[] = "+{$range}";
                }
            }
            
            if (!empty($effectTexts)) {
                $bonusDescriptions[] = "{$itemCount} pièce(s): " . implode(', ', $effectTexts);
            }
        }
        
        return implode(' | ', $bonusDescriptions);
    }
    
    /**
     * Mappe un typeId DofusDB vers un type et catégorie KrosmozJDR
     * 
     * @param int|null $typeId Type ID depuis DofusDB
     * @return array ['type' => string, 'category' => string]
     */
    private function mapItemTypeId(?int $typeId): array
    {
        // Source of truth DB: si l'utilisateur a autorisé ce typeId comme "ressource",
        // on le force en "resource" pour garantir l'intégration dans la table `resources`
        // sans modification de code lors de nouveaux typeId.
        if ($typeId !== null && \App\Models\Type\ResourceType::isDofusdbTypeAllowed($typeId)) {
            return ['type' => 'resource', 'category' => 'resource'];
        }

        // Mapping basé sur la configuration de DataIntegration
        $typeMapping = [
            1 => ['type' => 'weapon', 'category' => 'weapon'],
            2 => ['type' => 'weapon', 'category' => 'weapon'], // Arc
            3 => ['type' => 'weapon', 'category' => 'weapon'], // Bouclier
            4 => ['type' => 'weapon', 'category' => 'weapon'], // Bâton
            5 => ['type' => 'weapon', 'category' => 'weapon'], // Dague
            6 => ['type' => 'weapon', 'category' => 'weapon'], // Épée
            7 => ['type' => 'weapon', 'category' => 'weapon'], // Marteau
            8 => ['type' => 'weapon', 'category' => 'weapon'], // Pelle
            9 => ['type' => 'ring', 'category' => 'accessory'],
            10 => ['type' => 'amulet', 'category' => 'accessory'],
            11 => ['type' => 'belt', 'category' => 'accessory'],
            12 => ['type' => 'potion', 'category' => 'potion'],
            13 => ['type' => 'boots', 'category' => 'accessory'],
            14 => ['type' => 'hat', 'category' => 'accessory'],
            15 => ['type' => 'resource', 'category' => 'resource'],
            16 => ['type' => 'equipment', 'category' => 'equipment'],
            17 => ['type' => 'equipment', 'category' => 'equipment'], // Cape
            18 => ['type' => 'equipment', 'category' => 'equipment'], // Familier
            19 => ['type' => 'weapon', 'category' => 'weapon'], // Hache
            20 => ['type' => 'weapon', 'category' => 'weapon'], // Outil
            35 => ['type' => 'flower', 'category' => 'flower'],
            203 => ['type' => 'cosmetic', 'category' => 'cosmetic'],
            205 => ['type' => 'mount', 'category' => 'mount'],
        ];
        
        if ($typeId !== null && isset($typeMapping[$typeId])) {
            return $typeMapping[$typeId];
        }
        
        // Par défaut, utiliser equipment
        Log::warning('TypeId non mappé, utilisation de la valeur par défaut', ['type_id' => $typeId]);
        return ['type' => 'equipment', 'category' => 'equipment'];
    }

    /**
     * Conversion d'un sort selon les caractéristiques KrosmozJDR
     * 
     * @param array $rawData Données brutes du sort
     * @return array Données converties
     */
    public function convertSpell(array $rawData): array
    {
        // Extraction du nom et de la description depuis les objets multilingues
        $name = $this->extractMultilingualValue($rawData['name'] ?? null, 'Sort ' . ($rawData['id'] ?? 'Unknown'));
        $description = $this->extractMultilingualValue($rawData['description'] ?? null, '');
        
        // Tronquer la description à 255 caractères si nécessaire
        $description = mb_substr($description, 0, 255);
        
        Log::info('Conversion de sort', ['spell_id' => $rawData['id'] ?? 'Unknown', 'spell_name' => $name]);
        
        $converted = [
            'dofusdb_id' => (string) ($rawData['id'] ?? ''),
            'name' => $name,
            'description' => $description,
            'class' => $rawData['class'] ?? '',
            'cost' => $this->convertCost($rawData['cost'] ?? 0),
            'range' => $this->convertRange($rawData['range'] ?? 1),
            'area' => $this->convertArea($rawData['area'] ?? 1),
            'critical_hit' => $this->convertCriticalHit($rawData['critical_hit'] ?? 0),
            'failure' => $this->convertFailure($rawData['failure'] ?? 0),
            'image' => $rawData['img'] ?? null,
            'effect' => $this->convertEffects($rawData['effects'] ?? []),
            'level' => $rawData['level'] ?? null,
        ];
        
        // Gestion des niveaux si présents
        if (isset($rawData['levels']) && is_array($rawData['levels'])) {
            $converted['levels'] = $this->convertSpellLevels($rawData['levels']);
        }
        
        // Préserver le monstre invoqué (si présent dans rawData)
        if (isset($rawData['summon']) && is_array($rawData['summon'])) {
            $converted['summon'] = $rawData['summon'];
        }
        
        // Validation des valeurs converties
        $this->validateConvertedValues($converted, 'spell');
        
        Log::info('Sort converti avec succès', ['spell_name' => $converted['name']]);
        
        return $converted;
    }

    /**
     * Conversion d'un effet selon les caractéristiques KrosmozJDR
     * 
     * @param array $rawData Données brutes de l'effet
     * @return array Données converties
     */
    public function convertEffect(array $rawData): array
    {
        Log::info('Conversion d\'effet', ['effect_description' => $rawData['description'] ?? 'Unknown']);
        
        $converted = [
            'description' => $rawData['description'] ?? '',
            'type' => $this->convertEffectType($rawData['type'] ?? 'buff'),
            'value' => $this->convertEffectValue($rawData['value'] ?? 0),
            'condition' => $rawData['condition'] ?? ''
        ];
        
        // Validation des valeurs converties
        $this->validateConvertedValues($converted, 'effect');
        
        Log::info('Effet converti avec succès');
        
        return $converted;
    }

    /**
     * Conversion de la vie selon les caractéristiques KrosmozJDR
     * 
     * @param int $value Valeur brute
     * @param string $entityType Type d'entité
     * @return int Valeur convertie
     */
    private function convertLife(int $value, string $entityType): int
    {
        $limits = $this->characteristics['life']['limits'][$entityType] ?? [1, 1000];
        $min = $limits[0] ?? 1;
        $max = $limits[1] ?? 1000;
        
        if ($value < $min) {
            Log::warning("Vie trop faible, utilisation de la valeur minimale", [
                'value' => $value,
                'min' => $min,
                'entity_type' => $entityType
            ]);
            return $min;
        }
        
        if ($value > $max) {
            Log::warning("Vie trop élevée, utilisation de la valeur maximale", [
                'value' => $value,
                'max' => $max,
                'entity_type' => $entityType
            ]);
            return $max;
        }
        
        return $value;
    }

    /**
     * Conversion du niveau selon les caractéristiques KrosmozJDR
     * 
     * @param int $value Valeur brute
     * @param string $entityType Type d'entité
     * @return int Valeur convertie
     */
    private function convertLevel(int $value, string $entityType): int
    {
        $limits = $this->characteristics['level']['limits'][$entityType] ?? [1, 200];
        $min = $limits[0] ?? 1;
        $max = $limits[1] ?? 200;
        
        return max($min, min($max, $value));
    }

    /**
     * Conversion d'un attribut selon les caractéristiques KrosmozJDR
     * 
     * @param int $value Valeur brute
     * @param string $attributeType Type d'attribut
     * @param string $entityType Type d'entité
     * @return int Valeur convertie
     */
    private function convertAttribute(int $value, string $attributeType, string $entityType): int
    {
        $limits = $this->characteristics['attributes'][$attributeType]['limits'][$entityType] ?? [0, 100];
        $min = $limits[0] ?? 0;
        $max = $limits[1] ?? 100;
        
        return max($min, min($max, $value));
    }

    /**
     * Conversion de la taille selon les caractéristiques KrosmozJDR
     * 
     * @param string $value Valeur brute
     * @return string Valeur convertie
     */
    private function convertSize(string $value): string
    {
        $validSizes = $this->characteristics['size']['valid_values'] ?? ['tiny', 'small', 'medium', 'large', 'huge'];
        
        if (!in_array($value, $validSizes)) {
            Log::warning("Taille invalide, utilisation de la taille par défaut", ['value' => $value]);
            return $this->characteristics['size']['default'] ?? 'medium';
        }
        
        return $value;
    }

    /**
     * Conversion de la rareté selon les caractéristiques KrosmozJDR
     * 
     * @param string $value Valeur brute
     * @return string Valeur convertie
     */
    private function convertRarity(string $value): string
    {
        $validRarities = $this->characteristics['rarity']['valid_values'] ?? ['common', 'uncommon', 'rare', 'epic', 'legendary'];
        
        if (!in_array($value, $validRarities)) {
            Log::warning("Rareté invalide, utilisation de la rareté par défaut", ['value' => $value]);
            return $this->characteristics['rarity']['default'] ?? 'common';
        }
        
        return $value;
    }

    /**
     * Conversion du prix selon les caractéristiques KrosmozJDR
     * 
     * @param int $value Valeur brute
     * @return int Valeur convertie
     */
    private function convertPrice(int $value): int
    {
        $limits = $this->characteristics['price']['limits'] ?? [0, 1000000];
        $min = $limits[0] ?? 0;
        $max = $limits[1] ?? 1000000;
        
        return max($min, min($max, $value));
    }

    /**
     * Conversion du coût selon les caractéristiques KrosmozJDR
     * 
     * @param int $value Valeur brute
     * @return int Valeur convertie
     */
    private function convertCost(int $value): int
    {
        $limits = $this->characteristics['cost']['limits'] ?? [0, 100];
        $min = $limits[0] ?? 0;
        $max = $limits[1] ?? 100;
        
        return max($min, min($max, $value));
    }

    /**
     * Conversion de la portée selon les caractéristiques KrosmozJDR
     * 
     * @param int $value Valeur brute
     * @return int Valeur convertie
     */
    private function convertRange(int $value): int
    {
        $limits = $this->characteristics['range']['limits'] ?? [1, 20];
        $min = $limits[0] ?? 1;
        $max = $limits[1] ?? 20;
        
        return max($min, min($max, $value));
    }

    /**
     * Conversion de la zone selon les caractéristiques KrosmozJDR
     * 
     * @param int $value Valeur brute
     * @return int Valeur convertie
     */
    private function convertArea(int $value): int
    {
        $limits = $this->characteristics['area']['limits'] ?? [1, 10];
        $min = $limits[0] ?? 1;
        $max = $limits[1] ?? 10;
        
        return max($min, min($max, $value));
    }

    /**
     * Conversion du coup critique selon les caractéristiques KrosmozJDR
     * 
     * @param int $value Valeur brute
     * @return int Valeur convertie
     */
    private function convertCriticalHit(int $value): int
    {
        $limits = $this->characteristics['critical_hit']['limits'] ?? [0, 100];
        $min = $limits[0] ?? 0;
        $max = $limits[1] ?? 100;
        
        return max($min, min($max, $value));
    }

    /**
     * Conversion de l'échec selon les caractéristiques KrosmozJDR
     * 
     * @param int $value Valeur brute
     * @return int Valeur convertie
     */
    private function convertFailure(int $value): int
    {
        $limits = $this->characteristics['failure']['limits'] ?? [0, 100];
        $min = $limits[0] ?? 0;
        $max = $limits[1] ?? 100;
        
        return max($min, min($max, $value));
    }

    /**
     * Conversion des niveaux de sort selon les caractéristiques KrosmozJDR
     * 
     * @param array $levels Niveaux bruts
     * @return array Niveaux convertis
     */
    private function convertSpellLevels(array $levels): array
    {
        $convertedLevels = [];
        
        foreach ($levels as $level) {
            $convertedLevels[] = [
                'level' => $this->convertLevel($level['level'] ?? 1, 'spell'),
                'cost' => $this->convertCost($level['cost'] ?? 0),
                'range' => $this->convertRange($level['range'] ?? 1),
                'area' => $this->convertArea($level['area'] ?? 1),
                'effects' => $level['effects'] ?? [], // Préserver les effets pour l'analyse des types
            ];
        }
        
        return $convertedLevels;
    }

    /**
     * Conversion du type d'effet selon les caractéristiques KrosmozJDR
     * 
     * @param string $value Valeur brute
     * @return string Valeur convertie
     */
    private function convertEffectType(string $value): string
    {
        $validTypes = $this->characteristics['effect_types']['valid_values'] ?? ['buff', 'debuff', 'neutral'];
        
        if (!in_array($value, $validTypes)) {
            Log::warning("Type d'effet invalide, utilisation du type par défaut", ['value' => $value]);
            return $this->characteristics['effect_types']['default'] ?? 'neutral';
        }
        
        return $value;
    }

    /**
     * Conversion de la valeur d'effet selon les caractéristiques KrosmozJDR
     * 
     * @param int $value Valeur brute
     * @return int Valeur convertie
     */
    private function convertEffectValue(int $value): int
    {
        $limits = $this->characteristics['effect_value']['limits'] ?? [-100, 100];
        $min = $limits[0] ?? -100;
        $max = $limits[1] ?? 100;
        
        return max($min, min($max, $value));
    }

    /**
     * Extraction d'une valeur multilingue depuis les données DofusDB
     * 
     * @param mixed $multilingualData Données multilingues (peut être string, array ou null)
     * @param string $defaultValue Valeur par défaut si non trouvée
     * @return string Valeur extraite selon la langue configurée
     */
    private function extractMultilingualValue($multilingualData, string $defaultValue = ''): string
    {
        // Si c'est déjà une chaîne, on la retourne directement
        if (is_string($multilingualData)) {
            return $multilingualData;
        }
        
        // Si c'est null ou vide, on retourne la valeur par défaut
        if (empty($multilingualData) || !is_array($multilingualData)) {
            return $defaultValue;
        }
        
        // Récupération de la langue configurée
        $language = config('scrapping.data_collect.dofusdb.default_language', 'fr');
        
        // Extraction de la valeur selon la langue
        if (isset($multilingualData[$language])) {
            return (string) $multilingualData[$language];
        }
        
        // Fallback sur 'fr' si la langue configurée n'existe pas
        if (isset($multilingualData['fr'])) {
            return (string) $multilingualData['fr'];
        }
        
        // Fallback sur la première valeur disponible
        if (!empty($multilingualData)) {
            return (string) reset($multilingualData);
        }
        
        return $defaultValue;
    }

    /**
     * Validation des valeurs converties
     * 
     * @param array $data Données à valider
     * @param string $entityType Type d'entité
     * @throws \Exception En cas de validation échouée
     */
    private function validateConvertedValues(array $data, string $entityType): void
    {
        $requiredFields = $this->characteristics['required_fields'][$entityType] ?? [];
        
        foreach ($requiredFields as $field) {
            // Utiliser array_key_exists au lieu de isset pour détecter les valeurs null explicites
            // Ne pas utiliser empty() car cela rejette les valeurs 0 légitimes
            if (!array_key_exists($field, $data)) {
                throw new \Exception("Champ requis manquant : {$field} pour l'entité {$entityType}");
            }
            
            // Vérifier que la valeur n'est pas null (mais 0 est valide)
            if ($data[$field] === null) {
                throw new \Exception("Champ requis null : {$field} pour l'entité {$entityType}");
            }
        }
        
        Log::info("Validation des valeurs converties réussie", ['entity_type' => $entityType]);
    }

    /**
     * Convertit les effets en texte lisible
     * 
     * @param array $effects Tableau d'effets
     * @return string|null Description textuelle des effets
     */
    private function convertEffects(array $effects): ?string
    {
        if (empty($effects)) {
            return null;
        }

        $effectDescriptions = [];
        foreach ($effects as $effect) {
            if (!is_array($effect)) {
                continue;
            }

            $characteristic = $effect['characteristic'] ?? null;
            $from = $effect['from'] ?? 0;
            $to = $effect['to'] ?? 0;
            $description = $effect['description'] ?? null;

            if ($description) {
                $effectDescriptions[] = $description;
            } elseif ($characteristic && ($from > 0 || $to > 0)) {
                $range = $from === $to ? (string)$from : "{$from}-{$to}";
                $effectDescriptions[] = "{$characteristic}: +{$range}";
            }
        }

        return !empty($effectDescriptions) ? implode(', ', $effectDescriptions) : null;
    }

    /**
     * Convertit les effets en bonus textuel
     * 
     * @param array $effects Tableau d'effets
     * @return string|null Description textuelle du bonus
     */
    private function convertBonus(array $effects): ?string
    {
        if (empty($effects)) {
            return null;
        }

        $bonusDescriptions = [];
        foreach ($effects as $effect) {
            if (!is_array($effect)) {
                continue;
            }

            $from = $effect['from'] ?? 0;
            $to = $effect['to'] ?? 0;

            if ($from > 0 || $to > 0) {
                $range = $from === $to ? (string)$from : "{$from}-{$to}";
                $bonusDescriptions[] = "+{$range}";
            }
        }

        return !empty($bonusDescriptions) ? implode(', ', $bonusDescriptions) : null;
    }
}
