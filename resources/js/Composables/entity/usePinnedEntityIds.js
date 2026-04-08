/**
 * Épinglage local des entités (liste, cartes) — persistance `localStorage`.
 *
 * @description
 * Clé par type d’entité plural (ex. `spells`). Utilisé pour l’action « Épingler »
 * dans {@link EntityActionsDropdown}.
 *
 * @example
 * toggleEntityPin('spells', 12);
 * isEntityPinned('spells', 12);
 */
import { ref } from "vue";

const STORAGE_PREFIX = "krosmoz-pinned-entities";

/** Version globale pour réactivité des libellés/icônes « épinglé ». */
const pinStateVersion = ref(0);

function storageKey(entityType) {
    return `${STORAGE_PREFIX}:${String(entityType || "").trim()}`;
}

/**
 * @param {string} entityType
 * @returns {Set<string>}
 */
export function readPinnedSet(entityType) {
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

/**
 * @param {string} entityType
 * @param {Set<string>} set
 */
function writePinnedSet(entityType, set) {
    if (typeof localStorage === "undefined") return;
    try {
        localStorage.setItem(storageKey(entityType), JSON.stringify([...set]));
    } catch {
        /* quota / private mode */
    }
}

/**
 * @param {string} entityType
 * @param {string|number} entityId
 * @returns {boolean} true si épinglé après bascule
 */
export function toggleEntityPin(entityType, entityId) {
    const id = String(entityId ?? "").trim();
    if (!id || !String(entityType || "").trim()) return false;
    const set = readPinnedSet(entityType);
    if (set.has(id)) {
        set.delete(id);
    } else {
        set.add(id);
    }
    writePinnedSet(entityType, set);
    pinStateVersion.value++;
    return set.has(id);
}

/**
 * @param {string} entityType
 * @param {string|number|null|undefined} entityId
 * @returns {boolean}
 */
export function isEntityPinned(entityType, entityId) {
    pinStateVersion.value;
    const id = String(entityId ?? "").trim();
    if (!id) return false;
    return readPinnedSet(entityType).has(id);
}

/**
 * @returns {import('vue').Ref<number>}
 */
export function usePinnedEntityVersion() {
    return pinStateVersion;
}
