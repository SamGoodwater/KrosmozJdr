/**
 * Cache mémoire pour l’aperçu léger des entités référencées par kref (API au survol uniquement).
 *
 * - Une entrée par couple `entityType` + `id` (ex. `spells:42`), `entityType` au format API (pluriel Laravel).
 * - Pas de préchargement : uniquement après un premier survol / ouverture d’infobulle.
 * - Les requêtes simultanées pour la même clé sont fusionnées (une seule requête HTTP).
 * - Après édition d’une entité, appeler {@link invalidateKrefEntityPreviewCache} pour éviter un aperçu périmé.
 *
 * @example
 * const data = await loadKrefEntityPreview("spells", 12);
 * // second appel : résolu depuis le cache, sans fetch
 * invalidateKrefEntityPreviewCache("spells", 12);
 */

/** Types d’entité acceptés par l’API {@link CmsKrefEntityPreviewController} (pluriel). */
export const KREF_PREVIEW_API_ENTITY_TYPES = new Set([
    "campaigns",
    "scenarios",
    "spells",
    "items",
    "resources",
    "consumables",
    "monsters",
    "npcs",
    "panoplies",
    "capabilities",
    "creatures",
]);

const cache = new Map();

/** @type {Map<string, Promise<object>>} */
const inflight = new Map();

/**
 * @param {string} entityType — format API (ex. spells)
 * @param {string|number} id
 * @returns {string}
 */
export function krefEntityPreviewCacheKey(entityType, id) {
    return `${String(entityType || "").trim()}:${String(id)}`;
}

/**
 * Convertit le `entityType` des formulaires (singulier ou cas spéciaux) vers le pluriel utilisé par l’API kref / les routes `entities.*`.
 *
 * @param {string} formEntityType — ex. spell, panoply, capability
 * @returns {string} ex. spells, panoplies, capabilities
 */
export function toKrefPreviewApiEntityType(formEntityType) {
    const et = String(formEntityType || "").trim();
    if (et === "") {
        return "";
    }
    if (KREF_PREVIEW_API_ENTITY_TYPES.has(et)) {
        return et;
    }
    if (et === "panoply") {
        return "panoplies";
    }
    if (et === "capability" || et === "capabilities") {
        return "capabilities";
    }
    return `${et}s`;
}

/**
 * Retire une entrée du cache (succès et promesse en vol) pour forcer un refetch au prochain survol.
 * Sans effet si le type n’est pas servi par l’API d’aperçu kref.
 *
 * @param {string} entityType — format API (pluriel), ex. spells
 * @param {string|number} id
 */
export function invalidateKrefEntityPreviewCache(entityType, id) {
    const et = String(entityType || "").trim();
    if (!et || id == null || id === "") {
        return;
    }
    if (!KREF_PREVIEW_API_ENTITY_TYPES.has(et)) {
        return;
    }
    const key = krefEntityPreviewCacheKey(et, id);
    cache.delete(key);
    inflight.delete(key);
}

/**
 * @param {string} entityType
 * @param {string|number} id
 * @returns {object|null}
 */
export function getCachedKrefEntityPreview(entityType, id) {
    const key = krefEntityPreviewCacheKey(entityType, id);
    return cache.has(key) ? cache.get(key) : null;
}

/**
 * Charge l’aperçu (réseau une seule fois par clé, puis cache).
 *
 * @param {string} entityType — pluriel API (voir {@link KREF_PREVIEW_API_ENTITY_TYPES})
 * @param {string|number} id
 * @returns {Promise<object>} payload JSON API
 * @rejects {Error} si le type n’est pas supporté ou si `id` est absent
 */
export function loadKrefEntityPreview(entityType, id) {
    const et = String(entityType || "").trim();
    if (!KREF_PREVIEW_API_ENTITY_TYPES.has(et)) {
        return Promise.reject(new Error(`kref preview: type d'entité non supporté « ${et} »`));
    }
    if (id == null || id === "") {
        return Promise.reject(new Error("kref preview: id manquant"));
    }
    const key = krefEntityPreviewCacheKey(et, id);
    if (cache.has(key)) {
        return Promise.resolve(cache.get(key));
    }
    if (inflight.has(key)) {
        return inflight.get(key);
    }
    const promise = fetchPreview(et, id)
        .then((data) => {
            cache.set(key, data);
            inflight.delete(key);
            return data;
        })
        .catch((err) => {
            inflight.delete(key);
            throw err;
        });
    inflight.set(key, promise);
    return promise;
}

/**
 * Vide le cache (tests, bulk, déconnexion, rare cas de mise à jour forcée).
 */
export function clearKrefEntityPreviewCache() {
    cache.clear();
    inflight.clear();
}

/**
 * @param {string} entityType
 * @param {string|number} id
 * @returns {Promise<object>}
 */
async function fetchPreview(entityType, id) {
    const url = route("api.cms.kref-entity-preview", {
        entityType,
        id,
    });
    const res = await fetch(url, {
        method: "GET",
        credentials: "same-origin",
        headers: {
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
        },
    });
    if (res.status === 401) {
        const err = new Error("UNAUTHORIZED");
        err.code = 401;
        throw err;
    }
    if (!res.ok) {
        const err = new Error("FETCH_FAILED");
        err.code = res.status;
        throw err;
    }
    return res.json();
}
