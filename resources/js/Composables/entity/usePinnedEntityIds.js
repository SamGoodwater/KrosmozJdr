/**
 * Épinglage local des entités — fenêtres flottantes (positions + payload).
 *
 * @description
 * Persistance `localStorage`. Chaque entrée : `{ entityType, id, x, y, z, entity }`.
 * Compatible avec l’ancienne clé par type (`krosmoz-pinned-entities:<type>` → migration).
 *
 * @example
 * toggleEntityPin('monsters', 12, { entity: monsterPayload });
 * listPinnedWindows();
 */
import { ref } from "vue";

const STORAGE_KEY = "krosmoz-pinned-windows-v2";
const LEGACY_PREFIX = "krosmoz-pinned-entities";

/** Version globale pour réactivité. */
const pinStateVersion = ref(0);

let zCounter = 20;

/**
 * @typedef {Object} PinnedWindow
 * @property {string} entityType
 * @property {string} id
 * @property {number} x
 * @property {number} y
 * @property {number} z
 * @property {Object|null} entity
 */

/**
 * @returns {PinnedWindow[]}
 */
export function listPinnedWindows() {
    pinStateVersion.value;
    return readWindows();
}

/**
 * @param {string} entityType
 * @returns {Set<string>}
 */
export function readPinnedSet(entityType) {
    const type = String(entityType || "").trim();
    return new Set(
        readWindows()
            .filter((w) => w.entityType === type)
            .map((w) => w.id),
    );
}

/**
 * @param {string} entityType
 * @param {string|number} entityId
 * @param {{ entity?: Object|null }} [options]
 * @returns {boolean} true si épinglé après bascule
 */
export function toggleEntityPin(entityType, entityId, options = {}) {
    const type = String(entityType || "").trim();
    const id = String(entityId ?? "").trim();
    if (!type || !id) return false;

    const windows = readWindows();
    const idx = windows.findIndex((w) => w.entityType === type && w.id === id);
    if (idx >= 0) {
        windows.splice(idx, 1);
        writeWindows(windows);
        pinStateVersion.value++;
        return false;
    }

    zCounter += 1;
    const offset = (windows.length % 8) * 28;
    windows.push({
        entityType: type,
        id,
        x: 48 + offset,
        y: 96 + offset,
        z: zCounter,
        entity: sanitizeEntityPayload(options.entity ?? null),
    });
    writeWindows(windows);
    pinStateVersion.value++;
    return true;
}

/**
 * @param {string} entityType
 * @param {string|number|null|undefined} entityId
 * @returns {boolean}
 */
export function isEntityPinned(entityType, entityId) {
    pinStateVersion.value;
    const type = String(entityType || "").trim();
    const id = String(entityId ?? "").trim();
    if (!type || !id) return false;
    return readWindows().some((w) => w.entityType === type && w.id === id);
}

/**
 * @param {string} entityType
 * @param {string|number} entityId
 * @param {number} x
 * @param {number} y
 */
export function updatePinnedWindowPosition(entityType, entityId, x, y) {
    const type = String(entityType || "").trim();
    const id = String(entityId ?? "").trim();
    const windows = readWindows();
    const win = windows.find((w) => w.entityType === type && w.id === id);
    if (!win) return;
    win.x = Math.round(Number(x) || 0);
    win.y = Math.round(Number(y) || 0);
    writeWindows(windows);
    pinStateVersion.value++;
}

/**
 * @param {string} entityType
 * @param {string|number} entityId
 */
export function bringPinnedWindowToFront(entityType, entityId) {
    const type = String(entityType || "").trim();
    const id = String(entityId ?? "").trim();
    const windows = readWindows();
    const win = windows.find((w) => w.entityType === type && w.id === id);
    if (!win) return;
    zCounter += 1;
    win.z = zCounter;
    writeWindows(windows);
    pinStateVersion.value++;
}

/**
 * @param {string} entityType
 * @param {string|number} entityId
 * @param {Object|null} entity
 */
export function updatePinnedWindowEntity(entityType, entityId, entity) {
    const type = String(entityType || "").trim();
    const id = String(entityId ?? "").trim();
    const windows = readWindows();
    const win = windows.find((w) => w.entityType === type && w.id === id);
    if (!win) return;
    win.entity = sanitizeEntityPayload(entity);
    writeWindows(windows);
    pinStateVersion.value++;
}

/**
 * @returns {import('vue').Ref<number>}
 */
export function usePinnedEntityVersion() {
    return pinStateVersion;
}

/**
 * @returns {PinnedWindow[]}
 */
function readWindows() {
    if (typeof localStorage === "undefined") return [];
    try {
        const raw = localStorage.getItem(STORAGE_KEY);
        if (raw) {
            const arr = JSON.parse(raw);
            if (Array.isArray(arr)) {
                return arr
                    .map(normalizeWindow)
                    .filter(Boolean);
            }
        }
        return migrateLegacyPins();
    } catch {
        return [];
    }
}

/**
 * @param {PinnedWindow[]} windows
 */
function writeWindows(windows) {
    if (typeof localStorage === "undefined") return;
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(windows));
    } catch {
        /* quota / private mode */
    }
}

/**
 * @param {unknown} raw
 * @returns {PinnedWindow|null}
 */
function normalizeWindow(raw) {
    if (!raw || typeof raw !== "object") return null;
    const entityType = String(raw.entityType || "").trim();
    const id = String(raw.id ?? "").trim();
    if (!entityType || !id) return null;
    const z = Number(raw.z);
    if (Number.isFinite(z) && z > zCounter) zCounter = z;
    return {
        entityType,
        id,
        x: Number.isFinite(Number(raw.x)) ? Number(raw.x) : 48,
        y: Number.isFinite(Number(raw.y)) ? Number(raw.y) : 96,
        z: Number.isFinite(z) ? z : 20,
        entity: raw.entity && typeof raw.entity === "object" ? raw.entity : null,
    };
}

/**
 * @returns {PinnedWindow[]}
 */
function migrateLegacyPins() {
    if (typeof localStorage === "undefined") return [];
    const migrated = [];
    try {
        for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);
            if (!key || !key.startsWith(`${LEGACY_PREFIX}:`)) continue;
            const entityType = key.slice(LEGACY_PREFIX.length + 1);
            const raw = localStorage.getItem(key);
            if (!raw) continue;
            const ids = JSON.parse(raw);
            if (!Array.isArray(ids)) continue;
            ids.forEach((id, index) => {
                const sid = String(id ?? "").trim();
                if (!sid) return;
                zCounter += 1;
                migrated.push({
                    entityType,
                    id: sid,
                    x: 48 + index * 28,
                    y: 96 + index * 28,
                    z: zCounter,
                    entity: null,
                });
            });
            localStorage.removeItem(key);
        }
        if (migrated.length > 0) {
            writeWindows(migrated);
        }
    } catch {
        return migrated;
    }
    return migrated;
}

/**
 * @param {unknown} entity
 * @returns {Object|null}
 */
function sanitizeEntityPayload(entity) {
    if (!entity || typeof entity !== "object") return null;
    const raw = entity._data && typeof entity._data === "object" ? entity._data : entity;
    try {
        return JSON.parse(JSON.stringify(raw));
    } catch {
        return null;
    }
}
