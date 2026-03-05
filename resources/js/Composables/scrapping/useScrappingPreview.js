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
 *   itemStatusByKeyRef?: import('vue').Ref<Record<string, { status?: string }>>,
 *   includeRelationsRef?: import('vue').Ref<boolean>,
 *   onProgress?: (payload: { phase: string, done: number, total: number, label: string }) => void,
 *   onRunMeta?: (payload: { runId?: string|null, unknownCharacteristics?: Record<string, any>|null }) => void
 * }} options
 */
export function useScrappingPreview(options) {
    const { entityTypeRef, rawItemsRef, notifyError, getCsrfToken, setStatusForEntities, itemStatusByKeyRef, includeRelationsRef, onProgress, onRunMeta } = options;

    const convertedByItemId = ref({});
    const convertedByRelationKey = ref({});
    const lastBatchRelationsByKey = ref({});
    const lastRunId = ref(null);
    const lastUnknownCharacteristics = ref(null);
    const loadingConverted = ref(false);
    const mergeUnknown = (target, incoming) => {
        if (!incoming || typeof incoming !== "object") return target;
        const next = target && typeof target === "object"
            ? { ...target, ids: { ...(target.ids || {}) } }
            : { total_occurrences: 0, distinct_ids: 0, ids: {}, contains_id_38: false };
        const ids = incoming.ids && typeof incoming.ids === "object" ? incoming.ids : {};
        for (const [id, count] of Object.entries(ids)) {
            const n = Number(count);
            if (!Number.isFinite(n) || n <= 0) continue;
            next.ids[id] = (Number(next.ids[id] || 0) || 0) + n;
        }
        next.total_occurrences = Object.values(next.ids).reduce((acc, n) => acc + (Number(n) || 0), 0);
        next.distinct_ids = Object.keys(next.ids).length;
        next.contains_id_38 = Boolean(next.ids["38"]) || Boolean(next.contains_id_38);
        return next;
    };

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
            try {
                const backendRelations = Array.isArray(item?.relations)
                    ? item.relations.filter((r) => Number.isFinite(Number(r?.id)) && Number(r.id) > 0 && String(r?.type || "").trim() !== "")
                        .map((r) => ({ type: String(r.type), id: Number(r.id) }))
                    : [];
                if (backendRelations.length) {
                    relNext[key] = backendRelations;
                } else if (item?.raw) {
                    const relations = extractRelationsFromRaw(entityType, item.raw);
                    if (relations.length) relNext[key] = relations;
                }
            } catch {
                // ignorer
            }
        }
    }

    /**
     * Charge les données converties et relations par paquets.
     * @param {{ signal?: AbortSignal }} [options] - optionnel : annulation via signal (vérifié entre chaque paquet)
     */
    async function fetchConvertedBatch(options = {}) {
        const signal = options.signal ?? null;
        const ids = (rawItemsRef.value || []).map((it) => Number(it?.id)).filter((n) => Number.isFinite(n) && n > 0);
        if (!ids.length || !entityTypeRef.value) return;

        loadingConverted.value = true;
        conversionProgress.value = { phase: "main", total: ids.length, done: 0 };
        onProgress?.({ phase: "main", total: ids.length, done: 0, label: "Previsualisation" });
        convertedByItemId.value = {};
        convertedByRelationKey.value = {};
        lastBatchRelationsByKey.value = {};
        const entityType = entityTypeRef.value;
        let statusNext = { ...(itemStatusByKeyRef?.value ?? {}) };
        let relNext = {};
        let unknownSummary = null;

        try {
            // 1) Entités principales : par paquets pour mise à jour progressive et éviter timeout
            const mainChunks = chunkArray(ids, PREVIEW_CHUNK_SIZE);
            for (const chunkIds of mainChunks) {
                if (signal?.aborted) break;
                const result = await postJson(
                    "/api/scrapping/preview/batch",
                    { type: entityType, ids: chunkIds },
                    { headers: { "X-CSRF-TOKEN": getCsrfToken() || "" }, signal }
                );
                if (result.aborted) break;
                if (!result.ok) {
                    notifyError(result.error || "Erreur lors de la prévisualisation");
                    return;
                }
                const data = result.data;
                lastRunId.value = data?.run_id ?? lastRunId.value;
                unknownSummary = mergeUnknown(unknownSummary, data?.debug?.unknown_characteristics ?? null);
                onRunMeta?.({ runId: data?.run_id ?? null, unknownCharacteristics: unknownSummary });
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
                onProgress?.({
                    phase: "main",
                    total: ids.length,
                    done: Object.keys(next).length,
                    label: "Conversion principale",
                });
                if (itemStatusByKeyRef) itemStatusByKeyRef.value = { ...statusNext };
                else if (setStatusForEntities) {
                    const entities = items.filter((i) => Number.isFinite(Number(i?.id))).map((i) => ({ type: entityType, id: Number(i.id) }));
                    setStatusForEntities(entities, "converti");
                }
            }

            // 2) Relations : collecte par type puis chargement par paquets (sauf si "Inclure les relations" désactivé)
            const relConv = { ...convertedByRelationKey.value };
            const includeRelations = includeRelationsRef?.value !== false;
            if (includeRelations) {
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
                const relationTotal = Object.values(idsByType).reduce((acc, set) => acc + set.size, 0);
                let relationDone = 0;
                if (relationTotal > 0) {
                    conversionProgress.value = { phase: "relations", total: relationTotal, done: 0 };
                    onProgress?.({ phase: "relations", total: relationTotal, done: 0, label: "Relations" });
                }
                for (const [relType, idSet] of Object.entries(idsByType)) {
                    if (signal?.aborted) break;
                    const relIds = Array.from(idSet);
                    const relChunks = chunkArray(relIds, PREVIEW_CHUNK_SIZE);
                    for (const relChunkIds of relChunks) {
                        if (signal?.aborted) break;
                        try {
                            const relResult = await postJson(
                                "/api/scrapping/preview/batch",
                                { type: relType, ids: relChunkIds },
                                { headers: { "X-CSRF-TOKEN": getCsrfToken() || "" }, signal }
                            );
                            if (relResult.aborted) break;
                            if (!relResult.ok) continue;
                            const relData = relResult.data;
                            lastRunId.value = relData?.run_id ?? lastRunId.value;
                            unknownSummary = mergeUnknown(unknownSummary, relData?.debug?.unknown_characteristics ?? null);
                            onRunMeta?.({ runId: relData?.run_id ?? null, unknownCharacteristics: unknownSummary });
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
                                    resolvedEntityType: relItem.resolved_entity_type ?? null,
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
                            onProgress?.({ phase: "relations", total: relationTotal, done: relationDone, label: "Relations" });
                        } catch {
                            // ignorer erreur sur un paquet de relations
                        }
                    }
                }
            }
            convertedByRelationKey.value = relConv;
            lastUnknownCharacteristics.value = unknownSummary;
            onRunMeta?.({ runId: lastRunId.value, unknownCharacteristics: unknownSummary });
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
        lastRunId,
        lastUnknownCharacteristics,
        loadingConverted,
        conversionProgress,
        fetchConvertedBatch,
    };
}
