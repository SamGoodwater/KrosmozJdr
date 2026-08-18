<?php

declare(strict_types=1);

namespace App\Services\Effect;

use App\Models\Entity\Spell;
use App\Models\Type\SpellType;

/**
 * Aperçu d’un sort lié (monstre / créature) : méta + chips d’effets pour `SpellViewMinimal`.
 *
 * @description
 * Le payload tableau monstres n’embarque pas l’arbre `effects`. Les chips
 * `effect_usages_chips` suffisent à la vue minimale (sans second fetch).
 */
final class SpellNestedPreviewSerializer
{
    public function __construct(
        private readonly SpellEffectUsagesDataService $spellEffectUsagesDataService,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function serialize(Spell $spell): array
    {
        $usages = $this->spellEffectUsagesDataService->build($spell);

        return [
            'id' => $spell->id,
            'name' => $spell->name,
            'description' => $spell->description,
            'level' => $spell->level,
            'pa' => $spell->pa,
            'po_min' => $spell->po_min,
            'po_max' => $spell->po_max,
            'image' => $spell->image,
            'category' => $spell->category,
            'element' => $spell->element,
            'is_magic' => $spell->is_magic,
            'sight_line' => $spell->sight_line,
            'po_editable' => $spell->po_editable,
            'effect' => $spell->effect ?? null,
            'effect_usages_summary' => $usages['summary'],
            'effect_usages_chips' => $usages['chips'],
            'resolution_mode' => (string) ($spell->resolution_mode ?? 'attack_roll'),
            'attack_characteristic_key' => $spell->attack_characteristic_key,
            'save_characteristic_key' => $spell->save_characteristic_key,
            'save_dc_formula' => $spell->save_dc_formula,
            'save_success_note' => $spell->save_success_note,
            'auto_success_if_willing_target' => (bool) ($spell->auto_success_if_willing_target ?? false),
            'spellTypes' => $spell->relationLoaded('spellTypes')
                ? $spell->spellTypes->map(fn (SpellType $t) => [
                    'id' => $t->id,
                    'name' => $t->name,
                    'color' => $t->color ?? null,
                    'icon' => $t->icon ?? null,
                ])->values()->all()
                : [],
        ];
    }

    /**
     * Ajoute résumé / chips sur l’instance pour un dump Eloquent (Inertia).
     */
    public function decorate(Spell $spell): void
    {
        $usages = $this->spellEffectUsagesDataService->build($spell);
        $spell->setAttribute('effect_usages_summary', $usages['summary']);
        $spell->setAttribute('effect_usages_chips', $usages['chips']);
    }
}
