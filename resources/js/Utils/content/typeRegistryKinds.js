/**
 * Registres de types gérés dans l’espace contenu (une page, un sous-menu).
 *
 * @typedef {{
 *   key: string,
 *   title: string,
 *   shortTitle: string,
 *   icon: string,
 *   description: string,
 *   mode: 'decision'|'state',
 *   listUrl: string,
 *   bulkUrl: string,
 *   deleteUrlBase: string,
 *   moveCategoryUrlBase: string,
 *   currentCategory: string,
 *   spellTypeNameCell: boolean,
 *   canMove: boolean,
 *   canToggleScrap: boolean,
 *   canToggleCatalog: boolean,
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
            "« Scrap » inclut le type dans l’import / la maj de masse. « En jeu » le pré-coche dans le catalogue objets.",
        mode: "decision",
        listUrl: "/api/scrapping/item-types",
        bulkUrl: "/api/scrapping/item-types/bulk",
        deleteUrlBase: "/api/scrapping/item-types",
        moveCategoryUrlBase: "/api/scrapping/item-types",
        currentCategory: "equipment",
        spellTypeNameCell: false,
        canMove: true,
        canToggleScrap: true,
        canToggleCatalog: true,
    },
    {
        key: "resource",
        title: "Types de ressources",
        shortTitle: "Ressources",
        icon: "fa-cubes",
        description:
            "« Scrap » inclut le type dans l’import / la maj de masse. « En jeu » le pré-coche dans le catalogue ressources.",
        mode: "decision",
        listUrl: "/api/scrapping/resource-types",
        bulkUrl: "/api/scrapping/resource-types/bulk",
        deleteUrlBase: "/api/scrapping/resource-types",
        moveCategoryUrlBase: "/api/scrapping/resource-types",
        currentCategory: "resource",
        spellTypeNameCell: false,
        canMove: true,
        canToggleScrap: true,
        canToggleCatalog: true,
    },
    {
        key: "consumable",
        title: "Types de consommables",
        shortTitle: "Consommables",
        icon: "fa-flask",
        description:
            "« Scrap » inclut le type dans l’import / la maj de masse. « En jeu » le pré-coche dans le catalogue consommables.",
        mode: "decision",
        listUrl: "/api/scrapping/consumable-types",
        bulkUrl: "/api/scrapping/consumable-types/bulk",
        deleteUrlBase: "/api/scrapping/consumable-types",
        moveCategoryUrlBase: "/api/scrapping/consumable-types",
        currentCategory: "consumable",
        spellTypeNameCell: false,
        canMove: true,
        canToggleScrap: true,
        canToggleCatalog: true,
    },
    {
        key: "race",
        title: "Races de monstres",
        shortTitle: "Races",
        icon: "fa-paw",
        description:
            "« En jeu » (`playable`) rend la race disponible au catalogue et au filtre de scrap monstres. Pas de déplacement vers une autre entité.",
        mode: "state",
        listUrl: "/api/types/monster-races",
        bulkUrl: "/api/types/monster-races/bulk",
        deleteUrlBase: "/api/types/monster-races",
        moveCategoryUrlBase: "",
        currentCategory: "",
        spellTypeNameCell: false,
        canMove: false,
        canToggleScrap: false,
        canToggleCatalog: true,
    },
    {
        key: "spell",
        title: "Types de sorts",
        shortTitle: "Sorts",
        icon: "fa-wand-sparkles",
        description:
            "« En jeu » (`playable`) rend le type disponible au catalogue. Pas de déplacement vers une autre entité.",
        mode: "state",
        listUrl: "/api/types/spell-types",
        bulkUrl: "/api/types/spell-types/bulk",
        deleteUrlBase: "/api/types/spell-types",
        moveCategoryUrlBase: "",
        currentCategory: "",
        spellTypeNameCell: true,
        canMove: false,
        canToggleScrap: false,
        canToggleCatalog: true,
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
