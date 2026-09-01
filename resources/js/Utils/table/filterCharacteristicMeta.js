/**
 * Métadonnées caractéristique (icône / couleur) pour un filtre de tableau.
 *
 * Les ids `creature_pa` deviennent `pa` ; `resolveDef` lit le store Inertia.
 * Les types d’entité au pluriel (`spells`, `monsters`…) sont normalisés.
 */

import {
    resolveCharacteristicUiColor,
    resolveDef,
} from "@/Composables/entity/useCharacteristicDisplay";
import { normalizeCharacteristicIcon } from "@/Utils/Entity/characteristicTooltipLabels";

/** @type {Record<string, string>} */
const ENTITY_TYPE_TO_KIND = {
    spell: "spell",
    spells: "spell",
    capability: "capability",
    capabilities: "capability",
    monster: "creature",
    monsters: "creature",
    npc: "creature",
    npcs: "creature",
    class: "creature",
    classes: "creature",
    breed: "creature",
    breeds: "creature",
    creature: "creature",
    creatures: "creature",
    item: "item",
    items: "item",
    consumable: "consumable",
    consumables: "consumable",
    resource: "resource",
    resources: "resource",
    panoply: "panoply",
    panoplies: "panoply",
};

/**
 * Clé db_column à chercher à partir de l’id de filtre.
 *
 * @param {unknown} filterId
 * @returns {string}
 *
 * @example
 * characteristicLookupKey('creature_pa') // 'pa'
 * characteristicLookupKey('sight_line') // 'sight_line'
 */
export function characteristicLookupKey(filterId) {
    const id = String(filterId ?? "").trim();
    if (!id) return "";
    return id.replace(/^creature_/, "");
}

/**
 * Groupes Characteristics à interroger selon le type d’entité du tableau.
 *
 * @param {unknown} entityType
 * @returns {string[]}
 *
 * @example
 * sourceGroupsForTableEntityType('spells') // ['spell']
 * sourceGroupsForTableEntityType('monsters') // ['creature']
 */
export function sourceGroupsForTableEntityType(entityType) {
    const t = String(entityType ?? "").trim().toLowerCase();
    const kind = ENTITY_TYPE_TO_KIND[t] ?? t;
    if (kind === "spell") return ["spell"];
    if (kind === "capability") return ["capability"];
    if (kind === "creature") return ["creature"];
    if (kind === "item") return ["item"];
    if (kind === "consumable") return ["item", "consumable"];
    if (kind === "resource") return ["item", "resource"];
    if (kind === "panoply") return ["item", "panoply"];
    return ["creature", "item", "resource", "spell", "capability"];
}

/**
 * Icône et couleur BDD pour un filtre, ou null si ce n’est pas une caractéristique.
 *
 * @param {unknown} filterId
 * @param {{ entityType?: string }} [options]
 * @returns {{ key: string, icon: string|null, color: string|null, cssColor: string, name: string }|null}
 *
 * @example
 * resolveFilterCharacteristicMeta('pa', { entityType: 'spell' })
 */
export function resolveFilterCharacteristicMeta(filterId, options = {}) {
    const key = characteristicLookupKey(filterId);
    if (!key) return null;
    const def = resolveDef(key, undefined, {
        sourceGroups: sourceGroupsForTableEntityType(options.entityType),
    });
    if (!def) return null;
    const color = def._resolvedColor ?? def.color ?? null;
    const iconRaw = def._resolvedIcon ?? def.icon ?? null;
    const icon = normalizeCharacteristicIcon(iconRaw);
    return {
        key,
        icon: icon || null,
        color: typeof color === "string" && color.trim() !== "" ? color : null,
        cssColor: resolveCharacteristicUiColor(color) || "",
        name: String(def.short_name || def.name || "").trim(),
    };
}
