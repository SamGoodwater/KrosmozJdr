/**
 * Accès aux métadonnées des caractéristiques (Pinia + API, fallback props legacy).
 *
 * @description
 * Préfère le store Pinia (`useCharacteristicsPiniaStore`) alimenté par GET /api/characteristics.
 *
 * @see docs/40-DevGuides/PERFORMANCE_GLOBAL.md
 */

import { usePage } from "@inertiajs/vue3";
import { useCharacteristicsPiniaStore } from "@/Composables/store/useCharacteristicsPiniaStore";

/**
 * @returns {Record<string, { byDbColumn?: Record<string, object>, byComputedKey?: Record<string, object>, byCharacteristicKey?: Record<string, object>, byDofusdbId?: Record<string, object> }>}
 */
function getRawData() {
    try {
        const piniaStore = useCharacteristicsPiniaStore();
        const fromPinia = piniaStore.characteristics;
        if (fromPinia && typeof fromPinia === "object" && Object.keys(fromPinia).length > 0) {
            return fromPinia;
        }
    } catch {
        // Pinia non initialisé
    }

    try {
        const page = usePage();
        const legacy = page?.props?.characteristics;
        return legacy && typeof legacy === "object" ? legacy : {};
    } catch {
        return {};
    }
}

/**
 * @param {string} group - creature, spell, capability, item, consumable, resource, panoply
 * @param {string} key - db_column ou characteristic_key
 * @returns {Object|null}
 */
export function getByDbColumn(group, key) {
    const data = getRawData();
    const byDb = data?.[group]?.byDbColumn;
    return (byDb && typeof byDb === "object" && key) ? byDb[key] ?? null : null;
}

/**
 * @param {string} group - creature, spell, capability, item, consumable, resource, panoply
 * @param {string} key - characteristic_key
 * @returns {Object|null}
 */
export function getByCharacteristicKey(group, key) {
    const data = getRawData();
    const byKey = data?.[group]?.byCharacteristicKey;
    return (byKey && typeof byKey === "object" && key) ? byKey[key] ?? null : null;
}

/**
 * @param {string} group - creature, item, consumable, resource, panoply
 * @param {string|number} id - dofusdb_characteristic_id
 * @returns {Object|null}
 */
export function getByDofusdbId(group, id) {
    const data = getRawData();
    const byId = data?.[group]?.byDofusdbId;
    const key = id != null ? String(id) : "";
    return (byId && typeof byId === "object" && key) ? byId[key] ?? null : null;
}

/**
 * @param {string} group - creature, spell, capability
 * @param {string} key - characteristic_key (modifier_*_creature, save_*_creature)
 * @returns {Object|null}
 */
export function getByComputedKey(group, key) {
    const data = getRawData();
    const byComp = data?.[group]?.byComputedKey;
    return (byComp && typeof byComp === "object" && key) ? byComp[key] ?? null : null;
}

/**
 * Récupère le byDbColumn brut pour un groupe (pour compatibilité).
 *
 * @param {string} group
 * @returns {Record<string, object>}
 */
export function getByDbColumnMap(group) {
    const data = getRawData();
    const byDb = data?.[group]?.byDbColumn;
    return (byDb && typeof byDb === "object") ? byDb : {};
}

/**
 * Récupère le byComputedKey brut pour un groupe.
 *
 * @param {string} group
 * @returns {Record<string, object>}
 */
export function getByComputedKeyMap(group) {
    const data = getRawData();
    const byComp = data?.[group]?.byComputedKey;
    return (byComp && typeof byComp === "object") ? byComp : {};
}

/**
 * Récupère le byCharacteristicKey brut pour un groupe.
 *
 * @param {string} group
 * @returns {Record<string, object>}
 */
export function getByCharacteristicKeyMap(group) {
    const data = getRawData();
    const byKey = data?.[group]?.byCharacteristicKey;
    return (byKey && typeof byKey === "object") ? byKey : {};
}

/**
 * Récupère le byDofusdbId brut pour un groupe.
 *
 * @param {string} group
 * @returns {Record<string, object>}
 */
export function getByDofusdbIdMap(group) {
    const data = getRawData();
    const byId = data?.[group]?.byDofusdbId;
    return (byId && typeof byId === "object") ? byId : {};
}

/**
 * Champs fiche monstre (taille, race) — métadonnées service caractéristiques.
 *
 * @returns {Record<string, object>}
 */
export function getMonsterFieldMeta() {
    const data = getRawData();
    const raw = data?.creature?.byMonsterField;
    return raw && typeof raw === "object" ? raw : {};
}

export { getRawData };

export function useCharacteristicsStore() {
    return {
        getRawData,
        getByDbColumn,
        getByCharacteristicKey,
        getByDofusdbId,
        getByComputedKey,
        getByDbColumnMap,
        getByComputedKeyMap,
        getByCharacteristicKeyMap,
        getByDofusdbIdMap,
        getMonsterFieldMeta,
    };
}
