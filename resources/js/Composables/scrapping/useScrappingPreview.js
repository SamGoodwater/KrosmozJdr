/**
 * Composable : prévisualisation batch (convertis + relations).
 * Charge les données converties et relations pour les IDs courants par paquets pour :
 * - mise à jour progressive de l'UI (avancement visible),
 * - éviter les timeouts PHP (requêtes plus courtes).
 * Ne lance jamais.
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_REFONTE_UI_SCRAPPING.md
 */

import { ref } from "vue";
import { postJson } from "@/utils/scrapping/api";
import { extractRelationsFromRaw } from "@/config/scrapping/relationConfig";
import { TERMINAL_STATUSES } from "@/config/scrapping/statusConfig";

/** Taille des paquets pour preview batch (réduit timeout et permet mise à jour progressive). */
const PREVIEW_CHUNK_SIZE = 5;

/**
 * Découpe un tableau en paquets de taille maximale chunkSize.
 * @param {T[]} arr
 * @param {number} chunkSize
 * @returns {T[][]}
 */
function chunkArray(arr, chunkSize) {
    const size = Math.max(1, Math.floor(chunkSize));
    const out = [];
    for (let i = 0; i < arr.length; i += size) {
        out.push(arr.slice(i, i + size));
    }
    return out;
}

/**
 * @param {{
 *   entityTypeRef: import('vue').Ref<string>,
 *   rawItemsRef: import('vue').Ref<Array<{ id?: number }>>,
 *   notifyError: (msg: string) => void,
 *   getCsrfToken: () => string | null,
 *   setStatusForEntities?: (entities: Array<{ type: string, id: number }>, status: string) => void,
 *   itemStatusByKeyRef?: import('vue').Ref<Record<string, { status?: string }>>
 * }} options
 */
export function useScrappingPreview(options) {
    const { entityTypeRef, rawItemsRef, notifyError, getCsrfToken, setStatusForEntities, itemStatusByKeyRef } = options;

    const convertedByItemId = ref({});
    const convertedByRelationKey = ref({});
    const lastBatchRelationsByKey = ref({});
    const loadingConverted = ref(false);
    /** Progression pour l'UI : { phase: 'main'|'relations', total, done } ou null si inactif. */
    const conversionProgress = ref(null);

    function mergeChunkIntoConverted(items, entityType, next, relNext, statusNext) {
        for (const item of items) {
            const id = Number(item?.id);
            if (Number.isFinite(id)) {
                next[id] = {
                    raw: item.raw ?? null,
                    converted: item.converted ?? null,
                    existing: item.existing ?? null,
                    error: item.error ?? null,
                };
            }
            if (!Number.isFinite(id)) continue;
            const key = `${entityType}-${id}`;
            if (itemStatusByKeyRef && !TERMINAL_STATUSES.has(statusNext[key]?.status)) {
                statusNext[key] = { status: "converti" };
            }
            if (item?.raw) {
                try {
                    const relations = extractRelationsFromRaw(entityType, item.raw);
                    if (relations.length) relNext[key] = relations;
                } catch {
                    // ignorer
                }
            }
        }
    }

    async function fetchConvertedBatch() {
        const ids = (rawItemsRef.value || []).map((it) => Number(it?.id)).filter((n) => Number.isFinite(n) && n > 0);
        if (!ids.length || !entityTypeRef.value) return;

        loadingConverted.value = true;
        conversionProgress.value = { phase: "main", total: ids.length, done: 0 };
        convertedByItemId.value = {};
        convertedByRelationKey.value = {};
        lastBatchRelationsByKey.value = {};
        const entityType = entityTypeRef.value;
        let statusNext = { ...(itemStatusByKeyRef?.value ?? {}) };
        let relNext = {};

        try {
            // 1) Entités principales : par paquets pour mise à jour progressive et éviter timeout
            const mainChunks = chunkArray(ids, PREVIEW_CHUNK_SIZE);
            for (const chunkIds of mainChunks) {
                const result = await postJson(
                    "/api/scrapping/preview/batch",
                    { type: entityType, ids: chunkIds },
                    { headers: { "X-CSRF-TOKEN": getCsrfToken() || "" } }
                );
                if (!result.ok) {
                    notifyError(result.error || "Erreur lors de la prévisualisation");
                    return;
                }
                const data = result.data;
                const items = Array.isArray(data?.data?.items) ? data.data.items : (Array.isArray(data?.items) ? data.items : []);
                if (data?.success !== true || !items.length) {
                    if (mainChunks.length === 1) {
                        notifyError(data?.message || data?.error || "Réponse prévisualisation inattendue.");
                    }
                    continue;
                }
                const next = { ...convertedByItemId.value };
                mergeChunkIntoConverted(items, entityType, next, relNext, statusNext);
                convertedByItemId.value = next;
                lastBatchRelationsByKey.value = { ...lastBatchRelationsByKey.value, ...relNext };
                conversionProgress.value = { phase: "main", total: ids.length, done: Object.keys(next).length };
                if (itemStatusByKeyRef) itemStatusByKeyRef.value = { ...statusNext };
                else if (setStatusForEntities) {
                    const entities = items.filter((i) => Number.isFinite(Number(i?.id))).map((i) => ({ type: entityType, id: Number(i.id) }));
                    setStatusForEntities(entities, "converti");
                }
            }

            // 2) Relations : collecte par type puis chargement par paquets
            const idsByType = {};
            for (const list of Object.values(lastBatchRelationsByKey.value)) {
                if (!Array.isArray(list)) continue;
                for (const r of list) {
                    const t = String(r?.type ?? "").toLowerCase();
                    const rid = Number(r?.id);
                    if (!t || !Number.isFinite(rid) || rid <= 0) continue;
                    if (!idsByType[t]) idsByType[t] = new Set();
                    idsByType[t].add(rid);
                }
            }
            const relConv = { ...convertedByRelationKey.value };
            const relationTotal = Object.values(idsByType).reduce((acc, set) => acc + set.size, 0);
            let relationDone = 0;
            if (relationTotal > 0) {
                conversionProgress.value = { phase: "relations", total: relationTotal, done: 0 };
            }
            for (const [relType, idSet] of Object.entries(idsByType)) {
                const relIds = Array.from(idSet);
                const relChunks = chunkArray(relIds, PREVIEW_CHUNK_SIZE);
                for (const relChunkIds of relChunks) {
                    try {
                        const relResult = await postJson(
                            "/api/scrapping/preview/batch",
                            { type: relType, ids: relChunkIds },
                            { headers: { "X-CSRF-TOKEN": getCsrfToken() || "" } }
                        );
                        if (!relResult.ok) continue;
                        const relData = relResult.data;
                        const relItems = Array.isArray(relData?.data?.items) ? relData.data.items : (Array.isArray(relData?.items) ? relData.items : []);
                        if (!relItems?.length || relData?.success !== true) continue;
                        for (const relItem of relItems) {
                            const rid = Number(relItem?.id);
                            if (!Number.isFinite(rid)) continue;
                            const rk = `${relType}-${rid}`;
                            relConv[rk] = {
                                raw: relItem.raw ?? null,
                                converted: relItem.converted ?? null,
                                existing: relItem.existing ?? null,
                                error: relItem.error ?? null,
                            };
                        }
                        if (setStatusForEntities) {
                            const entities = relItems.filter((i) => Number.isFinite(Number(i?.id))).map((i) => ({ type: relType, id: Number(i.id) }));
                            setStatusForEntities(entities, "converti");
                        } else if (itemStatusByKeyRef) {
                            const nextStatus = { ...itemStatusByKeyRef.value };
                            for (const i of relItems) {
                                const id = Number(i?.id);
                                if (!Number.isFinite(id)) continue;
                                const key = `${relType}-${id}`;
                                if (TERMINAL_STATUSES.has(nextStatus[key]?.status)) continue;
                                nextStatus[key] = { status: "converti" };
                            }
                            itemStatusByKeyRef.value = nextStatus;
                        }
                        relationDone += relChunkIds.length;
                        conversionProgress.value = { phase: "relations", total: relationTotal, done: relationDone };
                    } catch {
                        // ignorer erreur sur un paquet de relations
                    }
                }
            }
            convertedByRelationKey.value = relConv;
        } catch (e) {
            notifyError("Valeurs converties : " + (e?.message ?? "erreur"));
        } finally {
            loadingConverted.value = false;
            conversionProgress.value = null;
        }
    }

    return {
        convertedByItemId,
        convertedByRelationKey,
        lastBatchRelationsByKey,
        loadingConverted,
        conversionProgress,
        fetchConvertedBatch,
    };
}
