/**
 * Registres de types gérés dans l’espace contenu (une page, un sous-menu).
 *
 * @typedef {{
 *   key: string,
 *   title: string,
 *   shortTitle: string,
 *   icon: string,
 *   description: string,
 *   listUrl: string,
 *   bulkUrl: string,
 *   deleteUrlBase: string,
 *   moveCategoryUrlBase: string,
 *   currentCategory: string,
 *   spellTypeNameCell: boolean,
 *   canMove: boolean,
 *   hasDofusMeta: boolean,
 * }} TypeRegistryKind
 */

/** @type {TypeRegistryKind[]} */
export const TYPE_REGISTRY_KINDS = [
    {
        key: "equipment",
        title: "Types d’équipements",
        shortTitle: "Équipements",
        icon: "fa-shield-halved",
        description:
            "« En jeu » pré-coche le type dans le catalogue. « Scrap » autorise l’import / la maj DofusDB des entités de ce type. Déplaçable vers ressources ou consommables.",
        listUrl: "/api/scrapping/item-types",
        bulkUrl: "/api/scrapping/item-types/bulk",
        deleteUrlBase: "/api/scrapping/item-types",
        moveCategoryUrlBase: "/api/scrapping/item-types",
        currentCategory: "equipment",
        spellTypeNameCell: false,
        canMove: true,
        hasDofusMeta: true,
    },
    {
        key: "resource",
        title: "Types de ressources",
        shortTitle: "Ressources",
        icon: "fa-cubes",
        description:
            "« En jeu » pré-coche le type dans le catalogue. « Scrap » autorise l’import / la maj DofusDB. Déplaçable vers équipements ou consommables.",
        listUrl: "/api/scrapping/resource-types",
        bulkUrl: "/api/scrapping/resource-types/bulk",
        deleteUrlBase: "/api/scrapping/resource-types",
        moveCategoryUrlBase: "/api/scrapping/resource-types",
        currentCategory: "resource",
        spellTypeNameCell: false,
        canMove: true,
        hasDofusMeta: true,
    },
    {
        key: "consumable",
        title: "Types de consommables",
        shortTitle: "Consommables",
        icon: "fa-flask",
        description:
            "« En jeu » pré-coche le type dans le catalogue. « Scrap » autorise l’import / la maj DofusDB. Déplaçable vers équipements ou ressources.",
        listUrl: "/api/scrapping/consumable-types",
        bulkUrl: "/api/scrapping/consumable-types/bulk",
        deleteUrlBase: "/api/scrapping/consumable-types",
        moveCategoryUrlBase: "/api/scrapping/consumable-types",
        currentCategory: "consumable",
        spellTypeNameCell: false,
        canMove: true,
        hasDofusMeta: true,
    },
    {
        key: "race",
        title: "Races de monstres",
        shortTitle: "Races",
        icon: "fa-paw",
        description:
            "« En jeu » pré-coche la race dans le catalogue monstres. « Scrap » l’inclut dans le filtre d’import monstres. Pas de déplacement vers une autre entité.",
        listUrl: "/api/types/monster-races",
        bulkUrl: "/api/types/monster-races/bulk",
        deleteUrlBase: "/api/types/monster-races",
        moveCategoryUrlBase: "",
        currentCategory: "",
        spellTypeNameCell: false,
        canMove: false,
        hasDofusMeta: false,
    },
    {
        key: "spell",
        title: "Types de sorts",
        shortTitle: "Sorts",
        icon: "fa-wand-sparkles",
        description:
            "« En jeu » pré-coche le type dans le catalogue sorts. « Scrap » indique si les sorts de ce type peuvent être mis à jour via DofusDB. Pas de déplacement.",
        listUrl: "/api/types/spell-types",
        bulkUrl: "/api/types/spell-types/bulk",
        deleteUrlBase: "/api/types/spell-types",
        moveCategoryUrlBase: "",
        currentCategory: "",
        spellTypeNameCell: true,
        canMove: false,
        hasDofusMeta: false,
    },
];

export const TYPE_REGISTRY_KIND_KEYS = TYPE_REGISTRY_KINDS.map((k) => k.key);

/**
 * @param {string} key
 * @returns {TypeRegistryKind|null}
 */
export function getTypeRegistryKind(key) {
    return TYPE_REGISTRY_KINDS.find((k) => k.key === key) ?? null;
}

/** Mapping type d’entité atelier DofusDB → kind de registre. */
export const SCRAPPING_ENTITY_TO_TYPE_KIND = {
    resource: "resource",
    consumable: "consumable",
    equipment: "equipment",
    item: "equipment",
    monster: "race",
    spell: "spell",
};
