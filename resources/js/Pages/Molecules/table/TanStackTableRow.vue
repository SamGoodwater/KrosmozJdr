<script setup>
/**
 * TanStackTableRow Molecule
 *
 * @description
 * Rend une ligne de tableau à partir de `row.cells`.
 * Supporte la colonne Actions et le menu contextuel (clic droit).
 */

import { ref, computed, onUnmounted } from "vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import CheckboxCore from "@/Pages/Atoms/data-input/CheckboxCore.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";

const props = defineProps({
    row: { type: Object, required: true },
    columns: { type: Array, required: true },
    showSelection: { type: Boolean, default: false },
    isSelected: { type: Boolean, default: false },
    /**
     * Classe Tailwind/DaisyUI appliquée à la ligne quand elle est sélectionnée.
     * Ex: "bg-primary/10"
     */
    selectedBgClass: { type: String, default: "bg-primary/10" },
    /**
     * Couleur UI (Design System) appliquée aux contrôles de sélection et aux fallback cells.
     */
    uiColor: { type: String, default: "primary" },
    /**
     * Type d'entité (pour la colonne Actions et le menu contextuel).
     */
    entityType: { type: String, default: null },
    /**
     * Afficher la colonne Actions.
     */
    showActionsColumn: { type: Boolean, default: false },
    /**
     * Fonction pour obtenir une cellule (depuis TanStackTable parent)
     */
    getCellFor: { type: Function, default: null },
});

const emit = defineEmits([
    "row-click",
    "row-dblclick",
    "toggle-select",
    "action", // Émis pour chaque action
]);

const getCell = (column) => {
    // Si getCellFor est fourni, l'utiliser (génération à la volée)
    if (props.getCellFor && typeof props.getCellFor === 'function') {
        return props.getCellFor(props.row, column) || { type: "text", value: "—", params: {} };
    }
    
    // Fallback : utiliser row.cells si disponible (ancien système)
    const cellId = column?.cellId || column?.id;
    return props.row?.cells?.[cellId] || { type: "text", value: "—", params: {} };
};

const isInteractiveTarget = (event) => {
    const el = event?.target;
    if (!el || typeof el.closest !== "function") return false;
    return Boolean(el.closest('a,button,input,select,textarea,[role="button"],[data-no-row-select]'));
};

// Menu contextuel (clic droit)
const contextMenuVisible = ref(false);
const contextMenuPosition = ref({ x: 0, y: 0 });

const handleContextMenu = (event) => {
    if (!props.entityType) return;
    
    event.preventDefault();
    event.stopPropagation();
    
    contextMenuPosition.value = {
        x: event.clientX,
        y: event.clientY,
    };
    contextMenuVisible.value = true;
};

const closeContextMenu = () => {
    contextMenuVisible.value = false;
};

// Fermer le menu contextuel au clic ailleurs
const handleDocumentClick = () => {
    if (contextMenuVisible.value) {
        closeContextMenu();
    }
};

// Écouter les clics sur le document
onUnmounted(() => {
    if (typeof window !== "undefined") {
        document.removeEventListener("click", handleDocumentClick);
    }
});

if (typeof window !== "undefined") {
    document.addEventListener("click", handleDocumentClick);
}

// Récupérer l'entité depuis la row
const rowEntity = computed(() => {
    return props.row?.rowParams?.entity || props.row?.original?.entity || null;
});

const usableRaw = computed(() => {
    const e = rowEntity.value;
    if (!e) return undefined;
    // BaseModel stocke les valeurs brutes dans _data
    if (e?._data && Object.prototype.hasOwnProperty.call(e._data, "usable")) return e._data.usable;
    // fallback si certains modèles exposent un getter usable
    if (typeof e?.usable !== "undefined") return e.usable;
    return undefined;
});

const hasUsable = computed(() => typeof usableRaw.value !== "undefined");
const isUsable = computed(() => {
    const v = usableRaw.value;
    if (typeof v === "boolean") return v;
    const s = String(v ?? "").toLowerCase();
    if (s === "1" || s === "true" || s === "yes" || s === "oui") return true;
    if (s === "0" || s === "false" || s === "no" || s === "non") return false;
    return Boolean(v);
});

const usableDotColor = computed(() => (isUsable.value ? "success" : "error"));
const usableTooltip = computed(() => (
    isUsable.value
        ? "Adapté au JDR"
        : "Non adapté au JDR"
));

const handleAction = (actionKey, entity) => {
    closeContextMenu();
    emit("action", actionKey, entity || rowEntity.value, props.row);
};
</script>

<template>
    <tr
        class="hover:bg-base-200 transition-colors"
        :class="isSelected ? selectedBgClass : null"
        @click="(e) => { if (!isInteractiveTarget(e)) emit('row-click', row); }"
        @dblclick="(e) => { if (!isInteractiveTarget(e)) emit('row-dblclick', row); }"
        @contextmenu="handleContextMenu"
    >
        <td v-if="showSelection" class="w-8 relative">
            <Tooltip v-if="hasUsable" :content="usableTooltip" placement="right" :color="usableDotColor" responsive="md">
                <span
                    data-no-row-select
                    class="absolute -top-6 -left-3 w-2.5 h-2.5 rounded-full ring-1 ring-base-300 opacity-90"
                    :class="[isUsable ? 'bg-success' : 'bg-error']"
                />
            </Tooltip>
            <CheckboxCore
                :model-value="isSelected"
                size="xs"
                :color="uiColor"
                @click.stop
                @update:model-value="(v) => emit('toggle-select', row, Boolean(v))"
            />
        </td>
        <!-- Colonne Actions - au début -->
        <td v-if="showActionsColumn && entityType" class="w-12 relative">
            <Tooltip v-if="!showSelection && hasUsable" :content="usableTooltip" placement="right" :color="usableDotColor" responsive="md">
                <span
                    data-no-row-select
                    class="absolute -top-6 -left-3 w-2.5 h-2.5 rounded-full ring-1 ring-base-300 opacity-90"
                    :class="[isUsable ? 'bg-success' : 'bg-error']"
                />
            </Tooltip>
            <EntityActions
                :entity-type="entityType"
                :entity="rowEntity"
                format="dropdown"
                display="icon-text"
                size="sm"
                color="primary"
                :context="{ inPanel: false }"
                @action="handleAction"
            />
        </td>
        <td
            v-for="(col, idx) in columns"
            :key="col.id"
        >
            <div class="relative">
                <Tooltip v-if="!showSelection && !showActionsColumn && idx === 0 && hasUsable" :content="usableTooltip" placement="right" :color="usableDotColor" responsive="md">
                    <span
                        data-no-row-select
                        class="absolute -top-6 -left-3 w-2.5 h-2.5 rounded-full ring-1 ring-base-300 opacity-90"
                        :class="[isUsable ? 'bg-success' : 'bg-error']"
                    />
                </Tooltip>
                <CellRenderer :cell="getCell(col)" :ui-color="uiColor" :entity="rowEntity" />
            </div>
        </td>
    </tr>
    
    <!-- Menu contextuel (clic droit) - Portail pour affichage au-dessus de tout -->
    <Teleport to="body">
        <EntityActions
            v-if="entityType && contextMenuVisible"
            :entity-type="entityType"
            :entity="rowEntity"
            format="context"
            display="icon-text"
            size="sm"
            color="primary"
            :context="{ inPanel: false }"
            :context-position="contextMenuPosition"
            :context-visible="contextMenuVisible"
            @action="handleAction"
        />
    </Teleport>
</template>


