/**
 * Registre partagé des types d'entités utilisés par les références riches (kref).
 *
 * Source de vérité frontend pour:
 * - recherche @ (TipTap),
 * - préfixes `@type:`,
 * - cache/preview d'entité.
 */
import { getEntityIconUrl } from "@/config/entities";

/**
 * @param {string} entityKey
 * @returns {string}
 */
function resolveEntityIconUrl(entityKey) {
    const url = getEntityIconUrl(entityKey);
    return typeof url === "string" ? url : "";
}

/** @type {ReadonlyArray<{ entityType: string, entityKey: string, label: string, icon: string, iconUrl: string, atPrefix: string }>} */
export const KREF_ENTITY_CONFIGS = Object.freeze([
    {
        entityType: "campaigns",
        entityKey: "campaign",
        label: "Campagnes",
        icon: "fa-solid fa-flag",
        iconUrl: resolveEntityIconUrl("campaign"),
        atPrefix: "campagne",
    },
    {
        entityType: "scenarios",
        entityKey: "scenario",
        label: "Scénarios",
        icon: "fa-solid fa-scroll",
        iconUrl: resolveEntityIconUrl("scenario"),
        atPrefix: "scenario",
    },
    {
        entityType: "spells",
        entityKey: "spell",
        label: "Sorts",
        icon: "fa-solid fa-wand-magic-sparkles",
        iconUrl: resolveEntityIconUrl("spell"),
        atPrefix: "sort",
    },
    {
        entityType: "items",
        entityKey: "item",
        label: "Objets",
        icon: "fa-solid fa-sack-dollar",
        iconUrl: resolveEntityIconUrl("item"),
        atPrefix: "objet",
    },
    {
        entityType: "resources",
        entityKey: "resource",
        label: "Ressources",
        icon: "fa-solid fa-box",
        iconUrl: resolveEntityIconUrl("resource"),
        atPrefix: "ressource",
    },
    {
        entityType: "consumables",
        entityKey: "consumable",
        label: "Consommables",
        icon: "fa-solid fa-flask",
        iconUrl: resolveEntityIconUrl("consumable"),
        atPrefix: "consommable",
    },
    {
        entityType: "monsters",
        entityKey: "monster",
        label: "Monstres",
        icon: "fa-solid fa-dragon",
        iconUrl: resolveEntityIconUrl("monster"),
        atPrefix: "monstre",
    },
    {
        entityType: "npcs",
        entityKey: "npc",
        label: "PNJ",
        icon: "fa-solid fa-user",
        iconUrl: resolveEntityIconUrl("npc"),
        atPrefix: "pnj",
    },
    {
        entityType: "panoplies",
        entityKey: "panoply",
        label: "Panoplies",
        icon: "fa-solid fa-shirt",
        iconUrl: resolveEntityIconUrl("panoply"),
        atPrefix: "panoplie",
    },
    {
        entityType: "capabilities",
        entityKey: "capability",
        label: "Capacités",
        icon: "fa-solid fa-bolt",
        iconUrl: resolveEntityIconUrl("capability"),
        atPrefix: "capacite",
    },
    {
        entityType: "creatures",
        entityKey: "creature",
        label: "Créatures",
        icon: "fa-solid fa-paw",
        iconUrl: resolveEntityIconUrl("creature"),
        atPrefix: "creature",
    },
]);

export const KREF_ENTITY_TYPES = Object.freeze(KREF_ENTITY_CONFIGS.map((cfg) => cfg.entityType));

export const KREF_AT_PREFIX_ALIASES = Object.freeze({
    carac: "characteristic",
    caracteristique: "characteristic",
    caracteristiques: "characteristic",
    section: "section",
    sections: "section",
});

/**
 * @param {string} value
 * @returns {string}
 */
export function normalizeAtPrefix(value) {
    return String(value || "")
        .trim()
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "");
}

/**
 * @param {string} prefix
 * @returns {{ mode: "characteristic"|"section"|"entityType", entityType?: string } | null}
 */
export function resolveAtPrefix(prefix) {
    const normalized = normalizeAtPrefix(prefix);
    if (!normalized) return null;
    const aliasMode = KREF_AT_PREFIX_ALIASES[normalized];
    if (aliasMode === "characteristic" || aliasMode === "section") {
        return { mode: aliasMode };
    }
    const entity = KREF_ENTITY_CONFIGS.find(
        (cfg) => normalized === normalizeAtPrefix(cfg.atPrefix) || normalized === normalizeAtPrefix(cfg.entityType),
    );
    if (entity) {
        return { mode: "entityType", entityType: entity.entityType };
    }
    return null;
}
