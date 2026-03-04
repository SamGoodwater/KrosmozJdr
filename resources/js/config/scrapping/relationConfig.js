/**
 * Config des relations par type d'entité (extraction depuis raw DofusDB).
 * Aligné avec le backend (Orchestrator). extractRelationsFromRaw ne lance jamais.
 */

function recipeIdFrom(r) {
    const fromIngredientIds = Array.isArray(r?.ingredientIds)
        ? r.ingredientIds.map((id) => Number(id)).filter((id) => Number.isFinite(id) && id > 0)
        : [];
    const fromIngredients = Array.isArray(r?.ingredients)
        ? r.ingredients.map((i) => Number(i?.id ?? i?.ingredientId ?? 0)).filter((id) => Number.isFinite(id) && id > 0)
        : [];
    const single = Number(r?.id ?? 0) > 0 ? [Number(r.id)] : [];
    return fromIngredientIds.concat(fromIngredients, single);
}

export const RELATION_EXTRACT_CONFIG = {
    class: [
        { key: "breedSpellsId", relationType: "spell", idFrom: (id) => Number(id) },
    ],
    breed: [
        { key: "breedSpellsId", relationType: "spell", idFrom: (id) => Number(id) },
    ],
    monster: [
        { key: "spells", relationType: "spell", idFrom: (s) => (typeof s === "object" ? Number(s?.id ?? 0) : Number(s)) },
        { key: "drops", relationType: "item", idFrom: (d) => Number(d?.itemId ?? d?.objectId ?? d?.id ?? 0) },
    ],
    spell: [
        { key: "summon", relationType: "monster", single: true, idFrom: (s) => Number(s?.id ?? 0) },
    ],
    resource: [
        { key: "recipeIds", relationType: "resource", idFrom: (id) => Number(id) },
        { key: "recipe", relationType: "resource", single: true, idFrom: recipeIdFrom },
    ],
    consumable: [
        { key: "recipeIds", relationType: "item", idFrom: (id) => Number(id) },
        { key: "recipe", relationType: "item", single: true, idFrom: recipeIdFrom },
    ],
    item: [
        { key: "recipeIds", relationType: "item", idFrom: (id) => Number(id) },
        { key: "recipe", relationType: "item", single: true, idFrom: recipeIdFrom },
    ],
    equipment: [
        { key: "recipeIds", relationType: "item", idFrom: (id) => Number(id) },
        { key: "recipe", relationType: "item", single: true, idFrom: recipeIdFrom },
    ],
};

export const RELATION_TYPE_LABELS = {
    spell: "Sort",
    item: "Drop",
    monster: "Invoqué",
    resource: "Ressource",
    consumable: "Consommable",
    equipment: "Équipement",
};

/**
 * Extrait les relations depuis raw selon la config. Ne lance jamais.
 * @param {string} entityType
 * @param {Object} raw
 * @returns {{ type: string, id: number }[]}
 */
export function extractRelationsFromRaw(entityType, raw) {
    try {
        if (!raw || typeof raw !== "object") return [];
        const rules = RELATION_EXTRACT_CONFIG[entityType];
        if (!Array.isArray(rules) || rules.length === 0) return [];
        const out = [];
        for (const rule of rules) {
            try {
                const value = raw[rule.key];
                if (value == null) continue;
                const idFrom = rule.idFrom ?? ((x) => Number(x?.id ?? x));
                if (rule.single) {
                    const extracted = idFrom(value);
                    const idList = Array.isArray(extracted) ? extracted : [extracted];
                    for (const id of idList) {
                        if (Number.isFinite(id) && id > 0) out.push({ type: rule.relationType, id });
                    }
                    continue;
                }
                const ids = Array.isArray(value) ? value : [value];
                for (const item of ids) {
                    const extracted = idFrom(item);
                    const idList = Array.isArray(extracted) ? extracted : [extracted];
                    for (const id of idList) {
                        if (Number.isFinite(id) && id > 0) out.push({ type: rule.relationType, id });
                    }
                }
            } catch {
                // règle échoue : ignorer
            }
        }
        return out;
    } catch {
        return [];
    }
}
