/**
 * Caractéristique unique « Actions » (groupe spell).
 * Chaque action est une surcharge : valeur = slug, icône, couleur, sous-texte = libellé du badge.
 *
 * @see database/seeders/data/characteristic-definitions/spell/actions-spell-definition.json
 */

import { TAILWIND_CHARACTERISTIC_PALETTES } from "@/Constants/tailwindCharacteristicPalettes";

/** Clé de la caractéristique Actions. */
export const SPELL_ACTIONS_CHARACTERISTIC_KEY = "actions_spell";

/** Nuance de palette assez foncée pour un texte blanc (contraste du badge plein). */
const ACTION_BADGE_SHADE = "800";

/**
 * Fond du badge d’action : nuance 800 de la palette, texte blanc.
 * `purple` n’a que la nuance 500 dans le thème : on l’assombrit vers le noir.
 *
 * @param {string|null|undefined} color
 * @returns {string}
 *
 * @example
 * actionBadgeBackgroundToken("pink") // "var(--color-pink-800)"
 */
export function actionBadgeBackgroundToken(color) {
    if (!color || typeof color !== "string") {
        return "";
    }
    let raw = color.trim().toLowerCase();
    if (raw === "") {
        return "";
    }
    if (raw.startsWith("#")) {
        return `color-mix(in srgb, ${raw} 38%, black)`;
    }
    if (raw === "brown" || raw.startsWith("brown-")) {
        raw = raw === "brown" ? "amber" : `amber-${raw.slice("brown-".length)}`;
    }
    if (raw === "purple" || raw.startsWith("purple-")) {
        return "color-mix(in srgb, var(--color-purple-500) 38%, black)";
    }
    const name = raw.includes("-") ? raw.slice(0, raw.lastIndexOf("-")) : raw;
    if (!TAILWIND_CHARACTERISTIC_PALETTES.includes(name)) {
        return "";
    }
    return `var(--color-${name}-${ACTION_BADGE_SHADE})`;
}

/**
 * Badge d’action plein : fond foncé de la couleur, texte blanc.
 *
 * @param {string|null|undefined} color
 * @returns {Record<string, string>|undefined}
 *
 * @example
 * spellActionBadgeStyle("pink") // { backgroundColor, color }
 */
export function spellActionBadgeStyle(color) {
    const backgroundColor = actionBadgeBackgroundToken(color);
    if (backgroundColor === "") {
        return undefined;
    }
    return {
        backgroundColor,
        color: "#fff",
    };
}
