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
     */
    serverUrl: { type: String, default: "" },
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
    "action", // Émis pour chaque action d'entité
]);

const permissions = usePermissions();

const isServerEnabled = computed(() => Boolean(String(props.serverUrl || "").trim()));

const loading = ref(false);
const serverRows = ref([]);
const serverMeta = ref({ filterOptions: null, capabilities: null });

const activeRows = computed(() => (isServerEnabled.value ? serverRows.value : props.rows));
const activeFilterOptions = computed(() => serverMeta.value?.filterOptions || null);

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

    // Gating minimal, policy-driven (évite d'afficher sélection/bulk quand pas de droits)
    return {
        ...cfg,
        columns: columnsWithActions,
        features: {
            ...(cfg.features || {}),
            selection: {
                ...((cfg.features || {}).selection || {}),
                // enabled=true dans la config, mais activé seulement si updateAny côté backend
                enabled: Boolean((cfg.features || {}).selection?.enabled) ? Boolean(canUpdateAny.value) : false,
            },
            export: {
                ...((cfg.features || {}).export || {}),
                // Export nécessite au minimum viewAny
                csv: Boolean((cfg.features || {}).export?.csv) ? Boolean(canViewAny.value) : false,
            },
        },
    };
});

async function fetchServer() {
    if (!isServerEnabled.value) return;
    const url = String(props.serverUrl || "").trim();
    if (!url) return;

    loading.value = true;
    try {
        const res = await fetch(url, {
            method: "GET",
            headers: { Accept: "application/json" },
            credentials: "same-origin",
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
        console.error("[EntityTanStackTable] fetch failed", e);
        serverRows.value = [];
        serverMeta.value = {};
        emit("loaded", { rows: serverRows.value, meta: serverMeta.value });
    } finally {
        loading.value = false;
    }
}

watch(
    () => props.serverUrl,
    () => fetchServer(),
    { immediate: true },
);

const handleRowClick = (row) => {
    // Le comportement par défaut est volontairement neutre :
    // - la navigation se fait via les cellules `type=route` (ex: colonne "name")
    // - la sélection peut être activée via config.features.selection.clickToSelect
    emit("row-click", row);
};

const handleAction = (actionKey, entity, row) => {
    emit("action", actionKey, entity, row);
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
        @update:selectedIds="(ids) => { emit('update:selectedIds', ids); emit('update:selected-ids', ids); }"
        @update:selected-ids="(ids) => { emit('update:selectedIds', ids); emit('update:selected-ids', ids); }"
        @row-click="handleRowClick"
        @row-dblclick="(row) => emit('row-dblclick', row)"
        @action="handleAction"
    />
</template>


