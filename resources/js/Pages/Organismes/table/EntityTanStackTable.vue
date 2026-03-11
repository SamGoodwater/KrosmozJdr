<script setup>
/**
 * EntityTanStackTable (Wrapper)
 *
 * @description
 * Glue entité au-dessus de `TanStackTable` :
 * - server opt-in via `serverUrl` complet (Option A)
 * - permissions (policy-driven) pour activer/masquer certaines features/actions
 * - navigation par défaut vers la page show (via entityRouteRegistry)
 *
 * Phase 2 (squelette) :
 * - fetch serveur si `serverUrl` est fourni
 * - sinon, consomme `rows` (dataset local)
 *
 * @see docs/30-UI/TANSTACK_TABLE.md
 */

import { computed, ref, watch } from "vue";
import TanStackTable from "@/Pages/Organismes/table/TanStackTable.vue";
import { usePermissions } from "@/Composables/permissions/usePermissions";

const props = defineProps({
    entityType: { type: String, required: true },
    /**
     * TanStackTableConfig (front = source of truth des colonnes/features)
     */
    config: { type: Object, required: true },
    /**
     * Dataset local (TableRow[]) — utilisé si pas de serverUrl
     */
    rows: { type: Array, default: () => [] },
    /**
     * Option A: URL complète (avec params). Si fournie -> active le fetch serveur.
     * Ignorée si serverSide=true (on utilise serverBaseUrl à la place).
     */
    serverUrl: { type: String, default: "" },
    /**
     * Mode pagination/filtres/tri côté serveur.
     * Si true : serverBaseUrl requis, les params (page, filters, sort, search) sont envoyés à l'API.
     */
    serverSide: { type: Boolean, default: false },
    /**
     * URL de base de l'API (sans query string). Requis quand serverSide=true.
     * Ex: route('api.tables.spells')
     */
    serverBaseUrl: { type: String, default: "" },
    /**
     * Clé pour forcer un refetch (ex: incrémentée après bulk edit).
     * Incluse dans l'URL comme _t pour éviter le cache.
     */
    refreshToken: { type: [Number, String], default: 0 },
    /**
     * Adapter optionnel : transforme la réponse fetch en { meta, rows } (TableResponse).
     */
    responseAdapter: { type: Function, default: null },
    /**
     * IDs sélectionnés (v-model).
     */
    selectedIds: { type: Array, default: null },
});

const emit = defineEmits([
    "row-click",
    "row-dblclick",
    // Compat: remonter aussi la forme kebab-case si certains parents l'écoutent
    "update:selectedIds",
    "update:selected-ids",
    "loaded",
    "refresh",
    "action", // Émis pour chaque action d'entité
]);

const permissions = usePermissions();

const isServerEnabled = computed(() => {
    if (props.serverSide) {
        return Boolean(String(props.serverBaseUrl || "").trim());
    }
    return Boolean(String(props.serverUrl || "").trim());
});

/** Params pour le fetch serveur (page, filters, sort, search). Pilotés par TanStackTable en mode serverSide. */
const serverParams = ref({
    page: 1,
    pageSize: 25,
    filters: {},
    search: "",
    sort: "id",
    order: "desc",
});

/** URL de fetch : statique (serverUrl) ou construite dynamiquement (serverBaseUrl + serverParams). */
const effectiveServerUrl = computed(() => {
    if (!isServerEnabled.value) return "";
    if (props.serverSide && props.serverBaseUrl) {
        const base = String(props.serverBaseUrl).trim();
        const params = new URLSearchParams();
        params.set("format", "entities");
        params.set("limit", String(serverParams.value.pageSize || 25));
        params.set("page", String(serverParams.value.page || 1));
        params.set("sort", String(serverParams.value.sort || "id"));
        params.set("order", String(serverParams.value.order || "desc"));
        if (String(serverParams.value.search || "").trim()) {
            params.set("search", String(serverParams.value.search).trim());
        }
        const filters = serverParams.value.filters || {};
        for (const [key, value] of Object.entries(filters)) {
            if (value === null || typeof value === "undefined" || value === "") continue;
            const normalized = Array.isArray(value)
                ? value.map((v) => String(v)).filter(Boolean).join(",")
                : typeof value === "boolean"
                    ? (value ? "1" : "0")
                    : String(value);
            params.set(`filters[${key}]`, normalized);
        }
        const token = props.refreshToken;
        if (token !== null && token !== undefined && token !== 0 && token !== "0") {
            params.set("_t", String(token));
        }
        return `${base}?${params.toString()}`;
    }
    return String(props.serverUrl || "").trim();
});

const loading = ref(false);
const serverRows = ref([]);
const serverMeta = ref({ filterOptions: null, capabilities: null });
let fetchAbortController = null;

const activeRows = computed(() => (isServerEnabled.value ? serverRows.value : props.rows));
const activeFilterOptions = computed(() => serverMeta.value?.filterOptions || null);
const serverPaginationMeta = computed(() => serverMeta.value?.pagination || null);

const normalizeAbility = (ability) => {
    const a = String(ability || "").trim();
    if (a === "read" || a === "view") return "view";
    if (a === "readAny" || a === "viewAny" || a === "list") return "viewAny";
    if (a === "add" || a === "create") return "create";
    if (a === "addAny" || a === "createAny") return "createAny";
    if (a === "update" || a === "edit") return "update";
    if (a === "updateAny" || a === "editAny") return "updateAny";
    if (a === "delete" || a === "remove") return "delete";
    if (a === "deleteAny" || a === "removeAny") return "deleteAny";
    if (a === "manage" || a === "admin") return "manageAny";
    if (a === "manageAny" || a === "adminAny") return "manageAny";
    return a;
};

const canAbility = (ability, { entityType } = {}) => {
    const a = normalizeAbility(ability);
    const e = entityType || props.entityType;

    const local = permissions.can(e, a);
    const server = serverMeta.value?.capabilities?.[a];
    if (typeof server === "boolean") return local && server;
    return local;
};

const canViewAny = computed(() => canAbility("viewAny"));
const canUpdateAny = computed(() => {
    return canAbility("updateAny");
});

const canManageAny = computed(() => canAbility("manageAny"));

const isColumnAllowed = (col) => {
    const rule = col?.permissions ?? col?.permission ?? null;
    if (!rule) return true;

    if (typeof rule === "function") {
        try {
            return Boolean(rule({
                permissions,
                entityType: props.entityType,
                capabilities: serverMeta.value?.capabilities || null,
            }));
        } catch (e) {
            console.warn("[EntityTanStackTable] column permissions fn failed", e);
            return false;
        }
    }

    // Shorthand: { ability: 'manageAny' } OR { abilities: ['viewAny','manageAny'] }
    const ability = rule?.ability;
    const abilities = Array.isArray(rule?.abilities) ? rule.abilities : null;

    if (ability) return canAbility(ability);
    if (abilities) return abilities.every((a) => canAbility(a));

    // Convenience: { manageOnly: true }
    if (rule?.manageOnly) return canManageAny.value;

    return true;
};

const resolvedConfig = computed(() => {
    const cfg = props.config || {};
    const filteredColumns = Array.isArray(cfg.columns) ? cfg.columns.filter(isColumnAllowed) : [];
    
    // Ajouter automatiquement la colonne Actions au début si elle n'existe pas déjà
    const hasActionsColumn = filteredColumns.some((col) => col.id === "actions");
    const columnsWithActions = hasActionsColumn
        ? filteredColumns
        : [
              {
                  id: "actions",
                  label: "", // Pas de label (colonne sans nom)
                  hideable: false,
                  isMain: false,
                  sort: { enabled: false },
                  search: { enabled: false },
                  cell: { type: "custom" }, // Type custom pour le rendu spécial
              },
              ...filteredColumns,
          ];

    // Contexte fusionné : meta serveur (characteristics, filterOptions, etc.) pour les cellules
    const mergedContext = {
        ...(cfg._metadata?.context || {}),
        ...serverMeta.value,
        capabilities: serverMeta.value?.capabilities || cfg._metadata?.context?.capabilities || {},
    };

    // Gating minimal, policy-driven (évite d'afficher sélection/bulk quand pas de droits)
    return {
        ...cfg,
        columns: columnsWithActions,
        features: {
            ...(cfg.features || {}),
            selection: {
                ...((cfg.features || {}).selection || {}),
                enabled: Boolean((cfg.features || {}).selection?.enabled) ? Boolean(canUpdateAny.value) : false,
            },
            export: {
                ...((cfg.features || {}).export || {}),
                csv: Boolean((cfg.features || {}).export?.csv) ? Boolean(canViewAny.value) : false,
            },
        },
        _metadata: {
            ...(cfg._metadata || {}),
            context: mergedContext,
        },
    };
});

async function fetchServer() {
    if (!isServerEnabled.value) return;
    const url = effectiveServerUrl.value;
    if (!url) return;

    fetchAbortController?.abort();
    fetchAbortController = new AbortController();

    loading.value = true;
    try {
        const res = await fetch(url, {
            method: "GET",
            headers: { Accept: "application/json" },
            credentials: "same-origin",
            signal: fetchAbortController.signal,
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const payload = await res.json();
        const adapted = typeof props.responseAdapter === "function"
            ? props.responseAdapter(payload)
            : payload;

        serverRows.value = Array.isArray(adapted?.rows) ? adapted.rows : [];
        serverMeta.value = adapted?.meta || {};
        
        emit("loaded", { rows: serverRows.value, meta: serverMeta.value });
    } catch (e) {
        if (e?.name === "AbortError") return;
        console.error("[EntityTanStackTable] fetch failed", e);
        serverRows.value = [];
        serverMeta.value = {};
        emit("loaded", { rows: serverRows.value, meta: serverMeta.value });
    } finally {
        loading.value = false;
    }
}

watch(
    () => (props.serverSide ? effectiveServerUrl.value : props.serverUrl),
    () => fetchServer(),
    { immediate: true },
);

const handleServerParamsChange = (params) => {
    if (!props.serverSide || !params) return;
    serverParams.value = { ...serverParams.value, ...params };
};

const handleRowClick = (row) => {
    // Le comportement par défaut est volontairement neutre :
    // - la navigation se fait via les cellules `type=route` (ex: colonne "name")
    // - la sélection peut être activée via config.features.selection.clickToSelect
    emit("row-click", row);
};

const handleAction = (actionKey, entity, row) => {
    emit("action", actionKey, entity, row);
};

const handleRefresh = async () => {
    if (isServerEnabled.value) {
        await fetchServer();
    }
    emit("refresh");
};
</script>

<template>
    <div v-if="!canViewAny" class="text-sm text-base-content/60 py-6">
        Accès refusé.
    </div>

    <TanStackTable
        v-else
        :config="resolvedConfig"
        :rows="activeRows"
        :loading="loading"
        :filter-options="activeFilterOptions"
        :selected-ids="selectedIds"
        :entity-type="entityType"
        :show-actions-column="true"
        :server-side="serverSide"
        :server-pagination-meta="serverPaginationMeta"
        :server-params="serverParams"
        @update:serverParams="handleServerParamsChange"
        @update:selectedIds="(ids) => { emit('update:selectedIds', ids); emit('update:selected-ids', ids); }"
        @update:selected-ids="(ids) => { emit('update:selectedIds', ids); emit('update:selected-ids', ids); }"
        @row-click="handleRowClick"
        @row-dblclick="(row) => emit('row-dblclick', row)"
        @refresh="handleRefresh"
        @action="handleAction"
    />
</template>


