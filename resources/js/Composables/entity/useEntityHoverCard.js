/**
 * useEntityHoverCard — Chargement et cache d'entités pour hover cards.
 *
 * @description
 * Charge une entité via api.tables.{entityType} uniquement au survol (lazy).
 * Cache en mémoire pour éviter les requêtes répétées. Annule les requêtes obsolètes.
 *
 * @param {Object} options
 * @param {string} options.entityType - Type d'entité (resources, items, consumables, etc.)
 * @param {number|string} options.entityId - ID de l'entité
 * @param {Object} [options.entity] - Entité déjà chargée (évite le fetch)
 * @returns {{ entityData: Ref, fetchedMeta: Ref, loading: Ref, error: Ref, fetchEntity: Function }}
 *
 * @example
 * const { entityData, loading, fetchEntity } = useEntityHoverCard({
 *   entityType: 'resources',
 *   entityId: 42,
 * });
 */
import { ref, watch } from "vue";

/** Cache global : Map<`${entityType}:${id}`, { entity, meta }> */
const entityCache = new Map();

const API_ROUTE_BY_TYPE = {
    resources: "api.tables.resources",
    items: "api.tables.items",
    consumables: "api.tables.consumables",
};

function cacheKey(entityType, id) {
    return `${entityType}:${id}`;
}

export function useEntityHoverCard(options) {
    const { entityType, entityId, entity: entityProp } = options;
    const entityData = ref(entityProp ?? null);
    const fetchedMeta = ref(null);
    const loading = ref(false);
    const error = ref(null);

    /** AbortController pour annuler les requêtes obsolètes */
    let abortController = null;

    async function fetchEntity() {
        const id = entityId ?? entityProp?.id;
        if (!id) return;

        const key = cacheKey(entityType, id);
        const cached = entityCache.get(key);
        if (cached) {
            entityData.value = cached.entity;
            fetchedMeta.value = cached.meta ?? null;
            return;
        }

        if (entityData.value?.id) return;

        abortController?.abort();
        abortController = new AbortController();
        loading.value = true;
        error.value = null;

        try {
            const routeName = API_ROUTE_BY_TYPE[entityType];
            if (!routeName) {
                entityData.value = { id: Number(id), name: "—" };
                return;
            }

            const baseUrl = route(routeName);
            const url = `${baseUrl}?format=entities&filters[id]=${id}&limit=1`;
            const res = await fetch(url, {
                credentials: "include",
                headers: { Accept: "application/json" },
                signal: abortController.signal,
            });
            const json = await res.json();
            const entities = json?.entities ?? [];
            const meta = json?.meta ?? {};
            const entity = entities[0] ?? null;

            if (entity) {
                entityData.value = entity;
                fetchedMeta.value = meta;
                entityCache.set(key, { entity, meta });
            } else {
                entityData.value = { id: Number(id), name: "—" };
            }
        } catch (e) {
            if (e?.name === "AbortError") return;
            error.value = e;
            entityData.value = { id: Number(id), name: "—" };
        } finally {
            loading.value = false;
            abortController = null;
        }
    }

    watch(
        () => entityProp,
        (val) => {
            if (val?.id) entityData.value = val;
        },
        { immediate: true },
    );

    return {
        entityData,
        fetchedMeta,
        loading,
        error,
        fetchEntity,
    };
}
