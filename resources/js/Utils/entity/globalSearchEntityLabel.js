/**
 * Mappe les clés API global-search vers les clés EntityLabel.
 *
 * @param {string} apiType
 * @returns {string}
 */
export function globalSearchEntityLabelKey(apiType) {
    const map = {
        pages: "page",
        sections: "section",
        spells: "spell",
        monsters: "monster",
        items: "item",
        resources: "resource",
        consumables: "consumable",
        breeds: "breed",
        campaigns: "campaign",
        scenarios: "scenario",
        npcs: "npc",
        shops: "shop",
        conditions: "condition",
        capabilities: "capability",
        specializations: "specialization",
        panoplies: "panoply",
        "creature-traits": "creature-trait",
        creatures: "creature",
        "resource-types": "resource",
    };

    return map[apiType] || apiType.replace(/s$/, "");
}
