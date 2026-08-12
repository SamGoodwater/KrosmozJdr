<?php

namespace App\Services;

use App\Models\Entity\Breed;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

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
     * @param  Model  $entity  L'entité à convertir en PDF
     * @param  string  $entityType  Le type d'entité (item, spell, monster, etc.)
     * @param  array  $options  Options supplémentaires pour la génération
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
     * @param  Collection|array  $entities  Les entités à convertir en PDF
     * @param  string  $entityType  Le type d'entité
     * @param  array  $options  Options supplémentaires pour la génération
     * @return \Barryvdh\DomPDF\PDF
     */
    public static function generateForEntities($entities, string $entityType, array $options = [])
    {
        if (! ($entities instanceof Collection)) {
            $entities = collect($entities);
        }

        $template = self::getTemplatePath($entityType, true);
        $data = $entities->map(fn ($entity) => self::prepareEntityData($entity, $entityType));

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
     * @param  string  $entityType  Le type d'entité
     * @param  bool  $multiple  Si true, utilise le template pour plusieurs entités
     * @return string Le chemin du template
     */
    protected static function getTemplatePath(string $entityType, bool $multiple = false): string
    {
        $suffix = $multiple ? '-multiple' : '';
        $template = "pdf.entities.{$entityType}{$suffix}";

        // Vérifier si le template existe, sinon utiliser le template générique
        if (! view()->exists($template)) {
            $template = $multiple ? 'pdf.entities.generic-multiple' : 'pdf.entities.generic';
        }

        return $template;
    }

    /**
     * Prépare les données d'une entité pour l'affichage dans le PDF
     *
     * @param  Model  $entity  L'entité
     * @param  string  $entityType  Le type d'entité
     * @return array Les données préparées
     */
    protected static function prepareEntityData(Model $entity, string $entityType): array
    {
        // Charger les relations courantes
        $entity->loadMissing(self::getRelationsForType($entityType));

        if ($entityType === 'breed' && $entity instanceof Breed) {
            $entity->load([
                'spells' => fn ($q) => $q->orderBy('breed_spell.character_level')
                    ->orderBy('breed_spell.slot_index')
                    ->orderBy('breed_spell.choice_order')
                    ->orderBy('spells.name'),
            ]);
        }

        $data = [
            'id' => $entity->id,
            'name' => $entity->name ?? $entity->title ?? 'Sans nom',
            'description' => $entity->description ?? null,
            'created_at' => $entity->created_at?->format('d/m/Y H:i'),
            'created_by' => self::resolveCreatedByLabel($entity, $entityType),
        ];

        // Monstre / PNJ : le nom jouable est sur la créature liée.
        if (in_array($entityType, ['monster', 'npc'], true) && $entity->relationLoaded('creature') && $entity->creature) {
            $data['name'] = $entity->creature->name ?: $data['name'];
            $data['description'] = $entity->creature->description ?? $data['description'];
        }

        // Ajouter les données spécifiques selon le type
        $data = array_merge($data, self::getSpecificData($entity, $entityType));

        return $data;
    }

    /**
     * Retourne les relations à charger selon le type d'entité
     *
     * @param  string  $entityType  Le type d'entité
     * @return array Les relations à charger
     */
    protected static function getRelationsForType(string $entityType): array
    {
        return match ($entityType) {
            'item' => ['itemType', 'createdBy', 'resources', 'panoplies'],
            'spell' => ['spellType', 'createdBy', 'breeds'],
            'monster' => ['monsterRace', 'creature.createdBy', 'creature'],
            'npc' => ['creature.createdBy', 'creature', 'breed', 'specialization'],
            'breed' => ['createdBy', 'npcs', 'spells'],
            'panoply' => ['createdBy', 'items'],
            'campaign' => ['createdBy', 'users', 'scenarios'],
            'scenario' => ['createdBy', 'campaign'],
            'creature' => ['createdBy', 'npc', 'monster'],
            'resource' => ['createdBy', 'resourceType', 'consumables'],
            'consumable' => ['createdBy', 'consumableType', 'resources'],
            'condition' => ['createdBy', 'creatures'],
            'capability' => ['createdBy', 'specializations', 'creatures'],
            'specialization' => ['createdBy', 'capabilities', 'npcs'],
            'shop' => ['createdBy', 'npc', 'items', 'consumables', 'resources'],
            default => ['createdBy'],
        };
    }

    /**
     * Libellé auteur : relation directe ou via créature (monstre / PNJ).
     */
    protected static function resolveCreatedByLabel(Model $entity, string $entityType): string
    {
        if (in_array($entityType, ['monster', 'npc'], true)) {
            return $entity->creature?->createdBy?->name
                ?? $entity->creature?->createdBy?->email
                ?? 'Système';
        }

        return $entity->createdBy?->name ?? $entity->createdBy?->email ?? 'Système';
    }

    /**
     * Retourne les données spécifiques selon le type d'entité
     *
     * @param  Model  $entity  L'entité
     * @param  string  $entityType  Le type d'entité
     * @return array Les données spécifiques
     */
    protected static function getSpecificData(Model $entity, string $entityType): array
    {
        return match ($entityType) {
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
                'level' => $entity->creature?->level ?? null,
                'life' => $entity->creature?->life ?? null,
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
                'life_dice' => $entity->life_dice,
                'dofusdb_id' => $entity->dofusdb_id,
                'evolution' => self::breedEvolutionForPdf($entity->evolution ?? null),
                'sorts_par_emplacements' => $entity instanceof Breed
                    ? self::breedSpellSlotsSummaryForPdf($entity)
                    : null,
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
            'condition' => [
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
     * HTML « évolution » : null si aucun texte visible (équivalent contenu vide côté fiche).
     */
    protected static function breedEvolutionForPdf(?string $html): ?string
    {
        if ($html === null || $html === '') {
            return null;
        }
        $text = trim(html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $text = preg_replace('/\s+| +/u', ' ', $text) ?? '';
        $text = trim(str_replace("\xc2\xa0", ' ', $text));

        return $text === '' ? null : $html;
    }

    /**
     * Liste des sorts par emplacement pour le PDF (texte multiligne).
     */
    protected static function breedSpellSlotsSummaryForPdf(Breed $breed): ?string
    {
        $slots = $breed->getSpellSlotsGrouped();
        if ($slots === []) {
            return null;
        }
        $lines = [];
        foreach ($slots as $slot) {
            $names = $slot['spells']->pluck('name')->filter()->implode(' · ');
            $lines[] = 'Niv. '.$slot['character_level'].' — Empl. '.$slot['slot_index'].' : '.$names;
        }

        return implode("\n", $lines);
    }

    /**
     * Formate la rareté en texte lisible
     *
     * @param  int|null  $rarity  La valeur de rareté
     * @return string Le texte formaté
     */
    protected static function formatRarity(?int $rarity): ?string
    {
        if ($rarity === null) {
            return null;
        }

        return match ($rarity) {
            0 => 'Commun',
            1 => 'Peu commun',
            2 => 'Rare',
            3 => 'Épique',
            4 => 'Légendaire',
            default => "Rareté {$rarity}",
        };
    }
}
