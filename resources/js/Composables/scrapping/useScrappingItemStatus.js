/**
 * Composable : statuts par ligne (recherché, converti, simulé, importé, erreur).
 * Utilise statusConfig ; ne fait aucun appel API.
 * @see docs/50-Fonctionnalités/Scrapping/PLAN_REFONTE_UI_SCRAPPING.md
 */

import { ref } from "vue";
import { STATUS_LABELS, STATUS_COLORS, TERMINAL_STATUSES } from "@/config/scrapping/statusConfig";

/**
 * @param {{ entityTypeRef: import('vue').Ref<string>|(() => string) }} options
 * @returns {{
 *   itemStatusByKey: import('vue').Ref<Record<string, { status: string, error?: string }>>,
 *   statusKey: (item: { id?: number }) => string,
 *   getStatusEntry: (item: { id?: number }) => { status: string, error?: string } | null,
 *   getStatusLabel: (item: { id?: number }) => string | null,
 *   getStatusColor: (item: { id?: number }) => string,
 *   setStatusForEntities: (entities: Array<{ type?: string, id?: number }>, status: string, error?: string | null) => void,
 *   setStatusFromBatchResults: (results: Array<{ type?: string, id?: number, success?: boolean, error?: string }>, isSimulate: boolean) => void,
 *   clearStatusForEntityType: (entityType: string) => void,
 *   TERMINAL_STATUSES: Set<string>
 * }}
 */
export function useScrappingItemStatus(options) {
    const entityTypeRef = options.entityTypeRef;
    const getEntityType = typeof entityTypeRef === "function" ? entityTypeRef : () => entityTypeRef?.value ?? "";

    const itemStatusByKey = ref({});

    function statusKey(item) {
        const type = item?.type != null && String(item.type).trim() !== "" ? String(item.type) : getEntityType();
        const id = Number(item?.id ?? 0);
        return `${type}-${id}`;
    }

    function getStatusEntry(item) {
        return itemStatusByKey.value[statusKey(item)] ?? null;
    }

    function getStatusLabel(item) {
        const entry = getStatusEntry(item);
        if (!entry) return null;
        return STATUS_LABELS[entry.status] ?? entry.status;
    }

    function getStatusColor(item) {
        const entry = getStatusEntry(item);
        if (!entry) return "neutral-300";
        return STATUS_COLORS[entry.status] ?? "neutral-300";
    }

    function setStatusForEntities(entities, status, error = null) {
        if (!Array.isArray(entities)) return;
        const entityType = getEntityType();
        const next = { ...itemStatusByKey.value };
        for (const e of entities) {
            const id = Number(e?.id ?? e);
            const type = e?.type ?? entityType;
            const key = `${type}-${id}`;
            next[key] = { status, error: error ?? undefined };
        }
        itemStatusByKey.value = next;
    }

    function setStatusFromBatchResults(results, isSimulate) {
        if (!Array.isArray(results)) return;
        const entityType = getEntityType();
        const next = { ...itemStatusByKey.value };
        const doneStatus = isSimulate ? "simulé" : "importé";
        const errStatus = isSimulate ? "simulation erreur" : "erreur";
        for (const r of results) {
            if (!r || typeof r.id === "undefined") continue;
            const key = `${r.type ?? entityType}-${Number(r.id)}`;
            next[key] = r.success
                ? { status: doneStatus }
                : { status: errStatus, error: r.error ?? "Erreur" };
        }
        itemStatusByKey.value = next;
    }

    function clearStatusForEntityType(entityType) {
        const prefix = `${String(entityType)}-`;
        itemStatusByKey.value = Object.fromEntries(
            Object.entries(itemStatusByKey.value).filter(([k]) => !k.startsWith(prefix))
        );
    }

    return {
        itemStatusByKey,
        statusKey,
        getStatusEntry: getStatusEntry,
        getStatusLabel,
        getStatusColor,
        setStatusForEntities,
        setStatusFromBatchResults,
        clearStatusForEntityType,
        TERMINAL_STATUSES,
    };
}
