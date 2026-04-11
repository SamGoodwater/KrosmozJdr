<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Correspondance entre les identifiants d’élément DofusDB (effets / définitions) et le masque Krosmoz.
 *
 * DofusDB (effets / définitions : effectElement, etc.) :
 * 0 neutre, 1 feu, 2 eau, 3 terre, 4 air.
 *
 * Attention : sur la racine du sort Dofus, spell_global.elementId sert au jet d’attaque (stat),
 * pas à l’élément des dégâts — ne pas l’utiliser pour le masque élémentaire du sort Krosmoz.
 *
 * Krosmoz : bits 0–4 = Neutre, Terre, Feu, Air, Eau (voir {@see ElementBitmask::PRIMARY_LABELS}).
 *
 * @see docs/50-Fonctionnalités/Scrapping/DOFUSDB_EFFECTS_CONVERSION.md
 */
final class DofusDbElementId
{
    public const NEUTRAL = 0;

    public const FIRE = 1;

    public const WATER = 2;

    public const EARTH = 3;

    public const AIR = 4;

    /**
     * Dofus elementId → indice primaire élémentaire Krosmoz (0–4), ou null si inconnu.
     */
    public static function toKrosmozElementPrimaryIndex(int $dofusElementId): ?int
    {
        return match ($dofusElementId) {
            self::NEUTRAL => 0,
            self::FIRE => 2,
            self::WATER => 4,
            self::EARTH => 1,
            self::AIR => 3,
            default => null,
        };
    }

    /**
     * Masque 7 bits avec un seul primaire élémentaire (0–127).
     */
    public static function toSingleElementMask(int $dofusElementId): int
    {
        $p = self::toKrosmozElementPrimaryIndex($dofusElementId);

        return $p === null ? 0 : ElementBitmask::fromPrimaries([$p]);
    }

    /**
     * Conversion effectElement Dofus (0–4) → masque ; -1 ou inconnu → null.
     * Nom historique « spellGlobal » : ne pas confondre avec spell_global.elementId racine (stat d’attaque).
     */
    public static function spellGlobalElementIdToMask(?int $dofusSpellElementId): ?int
    {
        if ($dofusSpellElementId === null || $dofusSpellElementId < 0) {
            return null;
        }
        $p = self::toKrosmozElementPrimaryIndex($dofusSpellElementId);
        if ($p === null) {
            return null;
        }

        return ElementBitmask::fromPrimaries([$p]);
    }
}
