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
        Log::info('Conversion de classe', ['class_name' => $rawData['name'] ?? 'Unknown']);
        
        $converted = [
            'name' => $rawData['name'] ?? '',
            'description' => $rawData['description'] ?? '',
            'life' => $this->convertLife($rawData['life'] ?? 0, 'class'),
            'life_dice' => $rawData['life_dice'] ?? '',
            'specificity' => $rawData['specificity'] ?? ''
        ];
        
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
        Log::info('Conversion de monstre', ['monster_name' => $rawData['name'] ?? 'Unknown']);
        
        $converted = [
            'creatures' => [
                'name' => $rawData['name'] ?? '',
                'level' => $this->convertLevel($rawData['level'] ?? 1, 'monster'),
                'life' => $this->convertLife($rawData['life'] ?? 0, 'monster'),
                'strength' => $this->convertAttribute($rawData['strength'] ?? 0, 'strength', 'monster'),
                'intelligence' => $this->convertAttribute($rawData['intelligence'] ?? 0, 'intelligence', 'monster'),
                'agility' => $this->convertAttribute($rawData['agility'] ?? 0, 'agility', 'monster'),
                'luck' => $this->convertAttribute($rawData['luck'] ?? 0, 'luck', 'monster'),
                'wisdom' => $this->convertAttribute($rawData['wisdom'] ?? 0, 'wisdom', 'monster'),
                'chance' => $this->convertAttribute($rawData['chance'] ?? 0, 'chance', 'monster')
            ],
            'monsters' => [
                'size' => $this->convertSize($rawData['size'] ?? 'medium'),
                'monster_race_id' => $rawData['monster_race_id'] ?? null
            ]
        ];
        
        // Validation des valeurs converties
        $this->validateConvertedValues($converted['creatures'], 'monster');
        
        Log::info('Monstre converti avec succès', ['monster_name' => $converted['creatures']['name']]);
        
        return $converted;
    }

    /**
     * Conversion d'un objet selon les caractéristiques KrosmozJDR
     * 
     * @param array $rawData Données brutes de l'objet
     * @return array Données converties
     */
    public function convertItem(array $rawData): array
    {
        Log::info('Conversion d\'objet', ['item_name' => $rawData['name'] ?? 'Unknown']);
        
        $converted = [
            'name' => $rawData['name'] ?? '',
            'level' => $this->convertLevel($rawData['level'] ?? 1, 'item'),
            'description' => $rawData['description'] ?? '',
            'type' => $rawData['type'] ?? 'equipment',
            'category' => $rawData['category'] ?? 'equipment',
            'rarity' => $this->convertRarity($rawData['rarity'] ?? 'common'),
            'price' => $this->convertPrice($rawData['price'] ?? 0)
        ];
        
        // Validation des valeurs converties
        $this->validateConvertedValues($converted, 'item');
        
        Log::info('Objet converti avec succès', ['item_name' => $converted['name']]);
        
        return $converted;
    }

    /**
     * Conversion d'un sort selon les caractéristiques KrosmozJDR
     * 
     * @param array $rawData Données brutes du sort
     * @return array Données converties
     */
    public function convertSpell(array $rawData): array
    {
        Log::info('Conversion de sort', ['spell_name' => $rawData['name'] ?? 'Unknown']);
        
        $converted = [
            'name' => $rawData['name'] ?? '',
            'description' => $rawData['description'] ?? '',
            'class' => $rawData['class'] ?? '',
            'cost' => $this->convertCost($rawData['cost'] ?? 0),
            'range' => $this->convertRange($rawData['range'] ?? 1),
            'area' => $this->convertArea($rawData['area'] ?? 1),
            'critical_hit' => $this->convertCriticalHit($rawData['critical_hit'] ?? 0),
            'failure' => $this->convertFailure($rawData['failure'] ?? 0)
        ];
        
        // Gestion des niveaux si présents
        if (isset($rawData['levels']) && is_array($rawData['levels'])) {
            $converted['levels'] = $this->convertSpellLevels($rawData['levels']);
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
                'area' => $this->convertArea($level['area'] ?? 1)
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
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new \Exception("Champ requis manquant : {$field} pour l'entité {$entityType}");
            }
        }
        
        Log::info("Validation des valeurs converties réussie", ['entity_type' => $entityType]);
    }
}
