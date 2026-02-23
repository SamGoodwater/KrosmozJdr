/**
 * Composable : prévisualisation batch (convertis + relations).
 * Charge les données converties et relations pour les IDs courants. Ne lance jamais.
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_REFONTE_UI_SCRAPPING.md
 */

import { ref } from "vue";
import { postJson } from "@/utils/scrapping/api";
import { extractRelationsFromRaw } from "@/config/scrapping/relationConfig";
import { TERMINAL_STATUSES } from "@/config/scrapping/statusConfig";

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

    async function fetchConvertedBatch() {
        const ids = (rawItemsRef.value || []).map((it) => Number(it?.id)).filter((n) => Number.isFinite(n) && n > 0);
        if (!ids.length || !entityTypeRef.value) return;

        loadingConverted.value = true;
        const next = {};
        convertedByRelationKey.value = {};
        try {
            const result = await postJson("/api/scrapping/preview/batch", {
                type: entityTypeRef.value,
                ids,
            }, {
                headers: { "X-CSRF-TOKEN": getCsrfToken() || "" },
            });

            if (!result.ok) {
                notifyError(result.error || "Erreur lors de la prévisualisation");
                return;
            }

            const data = result.data;
            const items = Array.isArray(data?.data?.items) ? data.data.items : (Array.isArray(data?.items) ? data.items : null);

            if (data?.success === true && items?.length) {
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
                }
                convertedByItemId.value = next;

                const entityType = entityTypeRef.value;
                if (itemStatusByKeyRef) {
                    const statusNext = { ...itemStatusByKeyRef.value };
                    for (const item of items) {
                        const id = Number(item?.id);
                        if (!Number.isFinite(id)) continue;
                        const key = `${entityType}-${id}`;
                        if (TERMINAL_STATUSES.has(statusNext[key]?.status)) continue;
                        statusNext[key] = { status: "converti" };
                    }
                    itemStatusByKeyRef.value = statusNext;
                } else if (setStatusForEntities) {
                    const entities = items
                        .filter((item) => Number.isFinite(Number(item?.id)))
                        .map((item) => ({ type: entityType, id: Number(item.id) }));
                    setStatusForEntities(entities, "converti");
                }

                let relNext = { ...lastBatchRelationsByKey.value };
                try {
                    for (const item of items) {
                        const id = Number(item?.id);
                        if (!Number.isFinite(id) || !item?.raw) continue;
                        const key = `${entityType}-${id}`;
                        const relations = extractRelationsFromRaw(entityType, item.raw);
                        if (relations.length) relNext[key] = relations;
                    }
                    lastBatchRelationsByKey.value = relNext;
                } catch {
                    // relations optionnelles
                }

                // Charger la conversion pour chaque relation (par type)
                const idsByType = {};
                for (const list of Object.values(relNext)) {
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
                for (const [relType, idSet] of Object.entries(idsByType)) {
                    const relIds = Array.from(idSet);
                    if (!relIds.length) continue;
                    try {
                        const relResult = await postJson("/api/scrapping/preview/batch", {
                            type: relType,
                            ids: relIds,
                        }, {
                            headers: { "X-CSRF-TOKEN": getCsrfToken() || "" },
                        });
                        if (!relResult.ok) continue;
                        const relData = relResult.data;
                        const relItems = Array.isArray(relData?.data?.items) ? relData.data.items : (Array.isArray(relData?.items) ? relData.items : null);
                        if (relItems?.length && (relData?.success === true)) {
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
                                const entities = relItems
                                    .filter((item) => Number.isFinite(Number(item?.id)))
                                    .map((item) => ({ type: relType, id: Number(item.id) }));
                                setStatusForEntities(entities, "converti");
                            } else if (itemStatusByKeyRef) {
                                const statusNext = { ...itemStatusByKeyRef.value };
                                for (const item of relItems) {
                                    const id = Number(item?.id);
                                    if (!Number.isFinite(id)) continue;
                                    const key = `${relType}-${id}`;
                                    if (TERMINAL_STATUSES.has(statusNext[key]?.status)) continue;
                                    statusNext[key] = { status: "converti" };
                                }
                                itemStatusByKeyRef.value = statusNext;
                            }
                        }
                    } catch {
                        // ignorer erreur par type de relation
                    }
                }
                convertedByRelationKey.value = relConv;
            } else {
                notifyError(data?.message || data?.error || "Réponse prévisualisation inattendue.");
            }
        } catch (e) {
            notifyError("Valeurs converties : " + (e?.message ?? "erreur"));
        } finally {
            loadingConverted.value = false;
        }
    }

    return {
        convertedByItemId,
        convertedByRelationKey,
        lastBatchRelationsByKey,
        loadingConverted,
        fetchConvertedBatch,
    };
}
