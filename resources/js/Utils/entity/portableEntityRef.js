/**
 * Refs few-shot portables (official_id / nom), alignées sur FewShotExamplePool::portableRef.
 *
 * L’id SQL n’est pas renvoyé : il n’est pas portable d’une base à l’autre.
 *
 * @example
 * portableEntityRef({ id: 12, official_id: "jdr:item:cape", name: "Cape" });
 * // "jdr:item:cape"
 */

/** Filtre d’état par défaut du sélecteur d’étalons IA (catalogues `api.tables.*`). */
export const IA_EXAMPLE_STATE_FILTER = Object.freeze({ state: "playable" });

/**
 * Nom affichable d’une fiche (colonne `name` ou créature liée).
 *
 * @param {object|null|undefined} entity
 * @returns {string}
 */
export function entityDisplayName(entity) {
    if (!entity || typeof entity !== "object") {
        return "";
    }
    for (const candidate of [entity.name, entity.creature?.name]) {
        if (typeof candidate === "string" && candidate.trim() !== "") {
            return candidate.trim();
        }
    }
    return "";
}

/**
 * Ref à persister dans `example_ids` : `official_id`, sinon le nom.
 *
 * @param {object|null|undefined} entity
 * @returns {string|null}
 */
export function portableEntityRef(entity) {
    if (!entity || typeof entity !== "object") {
        return null;
    }
    const official = typeof entity.official_id === "string" ? entity.official_id.trim() : "";
    if (official !== "") {
        return official;
    }
    const name = entityDisplayName(entity);
    return name !== "" ? name : null;
}
