/**
 * Affichage compact capacité (vues Minimal / Line) — portée, incantation, durées.
 *
 * @description
 * Réutilise {@link readSpellField} (mêmes champs camel/snake que le modèle sort/capacité).
 * Icônes / couleurs via {@link resolveSpellUsageCharacteristicVisual}.
 */

import { readSpellField } from "@/Utils/Entity/spellMinimalUsageDisplay";
import { resolveSpellUsageCharacteristicVisual } from "@/Utils/Entity/spellUsageCharacteristicVisual";

export { readSpellField as readCapabilityField };

/**
 * Durée d’effet et délai avant réutilisation (texte libre + métadonnées BDD).
 *
 * @param {object|null|undefined} entity
 * @returns {{
 *   duration: { show: boolean, text: string, visual: object, tooltip: string },
 *   reuseDelay: { show: boolean, text: string, visual: object, tooltip: string },
 * }}
 */
export function buildCapabilityDurationReusePresentation(entity) {
    const durRaw = readSpellField(entity, "duration", "duration");
    const reuseRaw = readSpellField(entity, "timeBeforeUseAgain", "time_before_use_again");

    const durStr =
        durRaw != null && String(durRaw).trim() !== "" && String(durRaw).trim().toLowerCase() !== "false"
            ? String(durRaw).trim()
            : "";
    const reuseStr =
        reuseRaw != null && String(reuseRaw).trim() !== "" && String(reuseRaw).trim().toLowerCase() !== "false"
            ? String(reuseRaw).trim()
            : "";

    const durVis = resolveSpellUsageCharacteristicVisual("duration");
    const reuseVis = resolveSpellUsageCharacteristicVisual("time_before_use_again");

    return {
        duration: {
            show: durStr.length > 0,
            text: durStr,
            visual: durVis,
            tooltip: [
                durVis.characteristicName || "Durée",
                durVis.characteristicHelper || "",
                durStr ? `Valeur : ${durStr}` : "",
            ]
                .filter(Boolean)
                .join("\n"),
        },
        reuseDelay: {
            show: reuseStr.length > 0,
            text: reuseStr,
            visual: reuseVis,
            tooltip: [
                reuseVis.characteristicName || "Délai avant réutilisation",
                reuseVis.characteristicHelper || "",
                reuseStr ? `Valeur : ${reuseStr}` : "",
            ]
                .filter(Boolean)
                .join("\n"),
        },
    };
}
