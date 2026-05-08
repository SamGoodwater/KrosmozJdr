/**
 * Favoris locaux des entités — persistance `localStorage`.
 *
 * @description
 * Prépare l'UI des favoris sans contrat backend. Même modèle que l'épinglage local.
 *
 * @example
 * toggleEntityFavorite('items', 12);
 */
import { ref } from "vue";

const STORAGE_PREFIX = "krosmoz-favorite-entities";
const favoriteStateVersion = ref(0);

function storageKey(entityType) {
    return `${STORAGE_PREFIX}:${String(entityType || "").trim()}`;
}

export function readFavoriteSet(entityType) {
    const key = storageKey(entityType);
    if (typeof localStorage === "undefined") return new Set();
    try {
        const raw = localStorage.getItem(key);
        if (!raw) return new Set();
        const arr = JSON.parse(raw);
        if (!Array.isArray(arr)) return new Set();
        return new Set(arr.map((v) => String(v)));
    } catch {
        return new Set();
    }
}

function writeFavoriteSet(entityType, set) {
    if (typeof localStorage === "undefined") return;
    try {
        localStorage.setItem(storageKey(entityType), JSON.stringify([...set]));
    } catch {
        /* quota / private mode */
    }
}

export function toggleEntityFavorite(entityType, entityId) {
    const id = String(entityId ?? "").trim();
    if (!id || !String(entityType || "").trim()) return false;
    const set = readFavoriteSet(entityType);
    if (set.has(id)) {
        set.delete(id);
    } else {
        set.add(id);
    }
    writeFavoriteSet(entityType, set);
    favoriteStateVersion.value++;
    return set.has(id);
}

export function isEntityFavorite(entityType, entityId) {
    favoriteStateVersion.value;
    const id = String(entityId ?? "").trim();
    if (!id) return false;
    return readFavoriteSet(entityType).has(id);
}

export function useFavoriteEntityVersion() {
    return favoriteStateVersion;
}
