<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Service de génération PDF
 * 
 * @description
 * Service pour générer des PDFs pour les entités du système.
 * Supporte la génération pour une entité unique ou plusieurs entités.
 * 
 * @example
 * // Générer un PDF pour un item
 * $pdf = PdfService::generateForEntity($item, 'item');
 * return $pdf->download('item-' . $item->id . '.pdf');
 * 
 * // Générer un PDF pour plusieurs items
 * $pdf = PdfService::generateForEntities($items, 'item');
 * return $pdf->download('items.pdf');
 */
class PdfService
{
    /**
     * Génère un PDF pour une entité unique
     * 
     * @param Model $entity L'entité à convertir en PDF
     * @param string $entityType Le type d'entité (item, spell, monster, etc.)
     * @param array $options Options supplémentaires pour la génération
     * @return \Barryvdh\DomPDF\PDF
     */
    public static function generateForEntity(Model $entity, string $entityType, array $options = [])
    {
        $template = self::getTemplatePath($entityType);
        $data = self::prepareEntityData($entity, $entityType);
        
        $defaultOptions = [
            'paper' => 'a4',
            'orientation' => 'portrait',
        ];
        
        $options = array_merge($defaultOptions, $options);
        
        return Pdf::loadView($template, [
            'entity' => $data,
            'entityType' => $entityType,
            'isMultiple' => false,
        ])
        ->setPaper($options['paper'], $options['orientation']);
    }

    /**
     * Génère un PDF pour plusieurs entités
     * 
     * @param Collection|array $entities Les entités à convertir en PDF
     * @param string $entityType Le type d'entité
     * @param array $options Options supplémentaires pour la génération
     * @return \Barryvdh\DomPDF\PDF
     */
    public static function generateForEntities($entities, string $entityType, array $options = [])
    {
        if (!($entities instanceof Collection)) {
            $entities = collect($entities);
        }
        
        $template = self::getTemplatePath($entityType, true);
        $data = $entities->map(fn($entity) => self::prepareEntityData($entity, $entityType));
        
        $defaultOptions = [
            'paper' => 'a4',
            'orientation' => 'portrait',
        ];
        
        $options = array_merge($defaultOptions, $options);
        
        return Pdf::loadView($template, [
            'entities' => $data,
            'entityType' => $entityType,
            'isMultiple' => true,
        ])
        ->setPaper($options['paper'], $options['orientation']);
    }

    /**
     * Retourne le chemin du template Blade selon le type d'entité
     * 
     * @param string $entityType Le type d'entité
     * @param bool $multiple Si true, utilise le template pour plusieurs entités
     * @return string Le chemin du template
     */
    protected static function getTemplatePath(string $entityType, bool $multiple = false): string
    {
        $suffix = $multiple ? '-multiple' : '';
        $template = "pdf.entities.{$entityType}{$suffix}";
        
        // Vérifier si le template existe, sinon utiliser le template générique
        if (!view()->exists($template)) {
            $template = $multiple ? 'pdf.entities.generic-multiple' : 'pdf.entities.generic';
        }
        
        return $template;
    }

    /**
     * Prépare les données d'une entité pour l'affichage dans le PDF
     * 
     * @param Model $entity L'entité
     * @param string $entityType Le type d'entité
     * @return array Les données préparées
     */
    protected static function prepareEntityData(Model $entity, string $entityType): array
    {
        // Charger les relations courantes
        $entity->loadMissing(self::getRelationsForType($entityType));
        
        $data = [
            'id' => $entity->id,
            'name' => $entity->name ?? $entity->title ?? 'Sans nom',
            'description' => $entity->description ?? null,
            'created_at' => $entity->created_at?->format('d/m/Y H:i'),
            'created_by' => $entity->createdBy?->name ?? 'Système',
        ];
        
        // Ajouter les données spécifiques selon le type
        $data = array_merge($data, self::getSpecificData($entity, $entityType));
        
        return $data;
    }

    /**
     * Retourne les relations à charger selon le type d'entité
     * 
     * @param string $entityType Le type d'entité
     * @return array Les relations à charger
     */
    protected static function getRelationsForType(string $entityType): array
    {
        return match($entityType) {
            'item' => ['itemType', 'createdBy', 'resources', 'panoplies'],
            'spell' => ['spellType', 'createdBy', 'breeds'],
            'monster' => ['monsterRace', 'createdBy', 'creature'],
            'npc' => ['creature', 'breed', 'specialization', 'createdBy'],
            'breed' => ['createdBy', 'npcs', 'spells'],
            'panoply' => ['createdBy', 'items'],
            'campaign' => ['createdBy', 'users', 'scenarios'],
            'scenario' => ['createdBy', 'campaign'],
            'creature' => ['createdBy', 'npc', 'monster'],
            'resource' => ['createdBy', 'resourceType', 'consumables'],
            'consumable' => ['createdBy', 'consumableType', 'resources'],
            'attribute' => ['createdBy', 'creatures'],
            'capability' => ['createdBy', 'specializations', 'creatures'],
            'specialization' => ['createdBy', 'capabilities', 'npcs'],
            'shop' => ['createdBy', 'npc', 'items', 'consumables', 'resources'],
            default => ['createdBy'],
        };
    }

    /**
     * Retourne les données spécifiques selon le type d'entité
     * 
     * @param Model $entity L'entité
     * @param string $entityType Le type d'entité
     * @return array Les données spécifiques
     */
    protected static function getSpecificData(Model $entity, string $entityType): array
    {
        return match($entityType) {
            'item' => [
                'level' => $entity->level,
                'rarity' => self::formatRarity($entity->rarity ?? null),
                'item_type' => $entity->itemType?->name ?? null,
                'effect' => $entity->effect,
                'bonus' => $entity->bonus,
                'price' => $entity->price,
                'dofusdb_id' => $entity->dofusdb_id,
            ],
            'spell' => [
                'level' => $entity->level,
                'ap_cost' => $entity->ap_cost,
                'range' => $entity->range,
                'spell_type' => $entity->spellType?->name ?? null,
                'dofusdb_id' => $entity->dofusdb_id,
            ],
            'monster' => [
                'level' => $entity->level,
                'life' => $entity->life,
                'size' => $entity->size,
                'is_boss' => $entity->is_boss ?? false,
                'monster_race' => $entity->monsterRace?->name ?? null,
                'dofusdb_id' => $entity->dofusdb_id,
            ],
            'npc' => [
                'breed' => $entity->breed?->name ?? null,
                'specialization' => $entity->specialization?->name ?? null,
                'creature' => $entity->creature?->name ?? null,
            ],
            'breed' => [
                'life' => $entity->life,
                'life_dice' => $entity->life_dice,
                'dofusdb_id' => $entity->dofusdb_id,
            ],
            'panoply' => [
                'state' => $entity->state ?? null,
                'read_level' => $entity->read_level ?? null,
                'write_level' => $entity->write_level ?? null,
                'bonus' => $entity->bonus,
                'dofusdb_id' => $entity->dofusdb_id,
            ],
            'campaign' => [
                'description' => $entity->description,
                'start_date' => $entity->start_date?->format('d/m/Y'),
                'end_date' => $entity->end_date?->format('d/m/Y'),
            ],
            'scenario' => [
                'description' => $entity->description,
                'campaign' => $entity->campaign?->name ?? null,
            ],
            'resource' => [
                'level' => $entity->level,
                'resource_type' => $entity->resourceType?->name ?? null,
                'dofusdb_id' => $entity->dofusdb_id,
            ],
            'consumable' => [
                'level' => $entity->level,
                'consumable_type' => $entity->consumableType?->name ?? null,
                'dofusdb_id' => $entity->dofusdb_id,
            ],
            'attribute' => [
                'state' => $entity->state ?? null,
                'read_level' => $entity->read_level ?? null,
                'write_level' => $entity->write_level ?? null,
            ],
            'capability' => [
                'description' => $entity->description,
            ],
            'specialization' => [
                'description' => $entity->description,
            ],
            'shop' => [
                'description' => $entity->description,
                'npc' => $entity->npc?->creature?->name ?? null,
            ],
            default => [],
        };
    }

    /**
     * Formate la rareté en texte lisible
     * 
     * @param int|null $rarity La valeur de rareté
     * @return string Le texte formaté
     */
    protected static function formatRarity(?int $rarity): ?string
    {
        if ($rarity === null) {
            return null;
        }
        
        return match($rarity) {
            0 => 'Commun',
            1 => 'Peu commun',
            2 => 'Rare',
            3 => 'Épique',
            4 => 'Légendaire',
            default => "Rareté {$rarity}",
        };
    }
}

