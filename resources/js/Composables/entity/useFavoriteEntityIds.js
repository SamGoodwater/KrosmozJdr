/**
 * Favoris utilisateur — cache front + API BDD (`api.favorites.*`).
 *
 * @description
 * Remplace le stockage localStorage. Les invit·es ne peuvent pas favoriser :
 * le toggle renvoie `{ ok: false, reason: 'auth' }`.
 *
 * @example
 * await ensureFavoritesLoaded();
 * await toggleEntityFavorite('items', 12);
 */
import { ref } from "vue";
import axios from "axios";
import { normalizeEntityType } from "@/Entities/entity-registry";
import { normalizeActionEntityType } from "@/Entities/entity-actions-config";

const favoriteStateVersion = ref(0);

/** @type {Map<string, Set<string>>} */
let idsByType = new Map();

let loadPromise = null;
let loadedForUserId = null;

/**
 * Message UX (sans le mot « entité ») quand l’utilisateur n’est pas connecté.
 */
export const FAVORITES_AUTH_REQUIRED_MESSAGE =
    "Connectez-vous pour ajouter une fiche à vos favoris.";

export const FAVORITES_ACCESS_AUTH_REQUIRED_MESSAGE =
    "Connectez-vous pour accéder à vos favoris.";

function favoriteKeyType(entityType) {
    const raw = String(entityType || "").trim();
    if (!raw) return "";
    try {
        return normalizeActionEntityType(normalizeEntityType(raw) || raw);
    } catch {
        return raw;
    }
}

function bump() {
    favoriteStateVersion.value++;
}

function setIdsByTypeFromPayload(payload) {
    const next = new Map();
    const raw = payload && typeof payload === "object" ? payload : {};
    for (const [type, ids] of Object.entries(raw)) {
        if (!Array.isArray(ids)) continue;
        next.set(
            favoriteKeyType(type),
            new Set(ids.map((v) => String(v)).filter(Boolean)),
        );
    }
    idsByType = next;
    bump();
}

function readAuthUserId() {
    try {
        const page = typeof window !== "undefined" ? window?.__INERTIA_PAGE__ : null;
        // Fallback via DOM / inertia not always available — callers pass user when possible.
        return null;
    } catch {
        return null;
    }
}

/**
 * @param {{ id?: number|string }|null} [user]
 */
export async function ensureFavoritesLoaded(user = null) {
    const userId = user?.id ?? readAuthUserId();
    if (!userId) {
        idsByType = new Map();
        loadedForUserId = null;
        bump();
        return { ids_by_type: {}, items: [], count: 0 };
    }

    if (loadedForUserId === String(userId) && loadPromise === null) {
        return { ids_by_type: Object.fromEntries([...idsByType].map(([k, v]) => [k, [...v]])), items: [], count: 0 };
    }

    if (loadPromise) return loadPromise;

    loadPromise = (async () => {
        try {
            const response = await axios.get(route("api.favorites.index"), {
                params: { hydrate: 0 },
            });
            setIdsByTypeFromPayload(response?.data?.ids_by_type || {});
            loadedForUserId = String(userId);
            return response?.data || { ids_by_type: {}, items: [], count: 0 };
        } catch (error) {
            idsByType = new Map();
            loadedForUserId = null;
            bump();
            throw error;
        } finally {
            loadPromise = null;
        }
    })();

    return loadPromise;
}

/**
 * Charge la liste hydratée (hits recherche) pour le panneau Favoris.
 */
export async function fetchHydratedFavorites() {
    const response = await axios.get(route("api.favorites.index"), {
        params: { hydrate: 1 },
    });
    setIdsByTypeFromPayload(response?.data?.ids_by_type || {});
    return {
        items: Array.isArray(response?.data?.items) ? response.data.items : [],
        count: Number(response?.data?.count || 0),
        ids_by_type: response?.data?.ids_by_type || {},
    };
}

export function invalidateFavoritesCache() {
    loadedForUserId = null;
    loadPromise = null;
}

export function readFavoriteSet(entityType) {
    favoriteStateVersion.value;
    const type = favoriteKeyType(entityType);
    if (!type) return new Set();
    return new Set(idsByType.get(type) || []);
}

export function listFavoritesByType() {
    favoriteStateVersion.value;
    /** @type {Record<string, string[]>} */
    const out = {};
    for (const [type, set] of idsByType.entries()) {
        out[type] = [...set];
    }
    return out;
}

export function isEntityFavorite(entityType, entityId) {
    favoriteStateVersion.value;
    const id = String(entityId ?? "").trim();
    if (!id) return false;
    return readFavoriteSet(entityType).has(id);
}

/**
 * @param {string} entityType
 * @param {number|string} entityId
 * @param {{ authenticated?: boolean }} [options]
 * @returns {Promise<{ ok: boolean, favorited: boolean, reason?: 'auth'|'error' }>}
 */
export async function toggleEntityFavorite(entityType, entityId, options = {}) {
    const type = favoriteKeyType(entityType);
    const id = String(entityId ?? "").trim();
    if (!type || !id) {
        return { ok: false, favorited: false, reason: "error" };
    }

    if (options.authenticated === false) {
        return { ok: false, favorited: false, reason: "auth" };
    }

    const currently = isEntityFavorite(type, id);
    const set = readFavoriteSet(type);

    // Optimistic update
    if (currently) {
        set.delete(id);
    } else {
        set.add(id);
    }
    idsByType.set(type, set);
    bump();

    try {
        if (currently) {
            await axios.delete(route("api.favorites.destroy"), {
                data: { entity_type: type, entity_id: Number(id) },
            });
            return { ok: true, favorited: false };
        }
        await axios.post(route("api.favorites.store"), {
            entity_type: type,
            entity_id: Number(id),
        });
        return { ok: true, favorited: true };
    } catch (error) {
        // Rollback
        if (currently) {
            set.add(id);
        } else {
            set.delete(id);
        }
        idsByType.set(type, set);
        bump();
        const status = error?.response?.status;
        if (status === 401 || status === 419) {
            return { ok: false, favorited: currently, reason: "auth" };
        }
        return { ok: false, favorited: currently, reason: "error" };
    }
}

export function useFavoriteEntityVersion() {
    return favoriteStateVersion;
}
