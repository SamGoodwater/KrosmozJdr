<script setup>
/**
 * TanStackTableRow Molecule
 *
 * @description
 * Rend une ligne de tableau à partir de `row.cells`.
 * Supporte la colonne Actions et le menu contextuel (clic droit).
 */

import { ref, computed, onUnmounted, nextTick } from "vue";
import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";
import CheckboxCore from "@/Pages/Atoms/data-input/CheckboxCore.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import { focusTableRowById } from "@/Composables/table/useTableRowFocusRestore.js";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import {
    getEntityStateBadgeColor,
    getEntityStateDisplayLabel,
    getEntityStateDotClass,
} from "@/Utils/Entity/SharedConstants.js";

/** Colonnes à contenu riche : max-width pour forcer le wrap et éviter scroll cellule */
const RICH_CONTENT_COLUMNS = new Set(["spell_summary_profile", "effect_summary"]);

const NAME_COLUMN_RE = /\b(name|nom|title|titre|libelle|label)\b/;
const buildColumnHaystack = (col) => {
    const id = String(col?.id || "").toLowerCase();
    const cellId = String(col?.cellId || "").toLowerCase();
    const label = String(col?.label || "").toLowerCase();
    return `${id} ${cellId} ${label}`;
};
const isNameColumn = (col) => NAME_COLUMN_RE.test(buildColumnHaystack(col));

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
    return Boolean(el.closest('a,button,input,select,textarea,[role="button"],[role="link"],[contenteditable="true"],[data-no-row-select]'));
};

const focusSiblingRow = (event, direction = 1) => {
    const current = event?.currentTarget;
    if (!current || typeof current !== "object") return;
    const candidate = direction > 0 ? current.nextElementSibling : current.previousElementSibling;
    if (candidate && typeof candidate.focus === "function") {
        candidate.focus();
    }
};

const focusEdgeRow = (event, edge = "first") => {
    const current = event?.currentTarget;
    const parent = current?.parentElement;
    if (!parent) return;
    const rows = parent.querySelectorAll("tr[tabindex='0']");
    if (!rows?.length) return;
    const target = edge === "last" ? rows[rows.length - 1] : rows[0];
    if (target && typeof target.focus === "function") target.focus();
};

const rowAriaLabel = computed(() => {
    const name = String(
        rowEntity.value?.name
        || rowEntity.value?._data?.name
        || props.row?.name
        || props.row?.id
        || ""
    ).trim();
    if (name) return `Ligne ${name}`;
    return "Ligne du tableau";
});

const selectionAriaLabel = computed(() => {
    const state = props.isSelected ? "désélectionner" : "sélectionner";
    return `${state} la ligne`;
});

const handleRowKeydown = (event) => {
    if (isInteractiveTarget(event)) return;
    const k = event.key;
    if (k === " " || k === "Spacebar") {
        event.preventDefault();
        event.stopPropagation();
        if (props.showSelection) {
            emit("toggle-select", props.row, !props.isSelected);
        }
        return;
    }
    // Entrée : laisser remonter au conteneur `tableRootRef` (aperçu / page / édition rapide)
    if (k === "Enter") {
        return;
    }
    if (event.key === "Escape") {
        closeContextMenu();
    }
    if (event.key === "ArrowDown") {
        event.preventDefault();
        event.stopPropagation();
        focusSiblingRow(event, 1);
    }
    if (event.key === "ArrowUp") {
        event.preventDefault();
        event.stopPropagation();
        focusSiblingRow(event, -1);
    }
    if (event.key === "Home") {
        event.preventDefault();
        event.stopPropagation();
        focusEdgeRow(event, "first");
    }
    if (event.key === "End") {
        event.preventDefault();
        event.stopPropagation();
        focusEdgeRow(event, "last");
    }
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
    nextTick(() => focusTableRowById(props.row?.id));
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

const stateRaw = computed(() => {
    const e = rowEntity.value;
    if (!e) return undefined;
    // BaseModel stocke les valeurs brutes dans _data
    if (e?._data && Object.prototype.hasOwnProperty.call(e._data, "state")) return e._data.state;
    // fallback si certains modèles exposent un getter state
    if (typeof e?.state !== "undefined") return e.state;
    return undefined;
});

const hasState = computed(() => typeof stateRaw.value !== "undefined");
const stateValue = computed(() => {
    const v = stateRaw.value;
    if (typeof v !== "string") return null;
    return v;
});

const dotBgClass = computed(() => getEntityStateDotClass(stateValue.value));

const dotTooltip = computed(() => {
    if (!stateValue.value) return null;
    return getEntityStateDisplayLabel(stateValue.value);
});

const dotColor = computed(() => getEntityStateBadgeColor(stateValue.value));

const handleAction = (actionKey, entity) => {
    closeContextMenu();
    emit("action", actionKey, entity || rowEntity.value, props.row);
};

const handleNameRouteClick = (event) => {
    event?.preventDefault?.();
    event?.stopPropagation?.();
    emit("action", "quick-view", rowEntity.value, props.row);
};
</script>

<template>
    <tr
        data-table-row-focus
        data-row-contextmenu-target
        :data-row-id="String(props.row?.id ?? '')"
        class="group hover:bg-base-200 transition-[colors,box-shadow] duration-200 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/60"
        :class="isSelected ? selectedBgClass : null"
        tabindex="0"
        :aria-label="rowAriaLabel"
        :aria-selected="showSelection ? String(isSelected) : undefined"
        @click="(e) => { if (!isInteractiveTarget(e)) emit('row-click', row, e); }"
        @dblclick="(e) => { if (!isInteractiveTarget(e)) emit('row-dblclick', row); }"
        @keydown="handleRowKeydown"
        @contextmenu="handleContextMenu"
    >
        <td v-if="showSelection" class="w-8 relative">
            <Tooltip v-if="hasState && dotTooltip" :content="dotTooltip" placement="right" :color="dotColor" responsive="md">
                <span
                    data-no-row-select
                    class="absolute -top-6 -left-3 w-2.5 h-2.5 rounded-full ring-1 ring-base-300 opacity-90"
                    :class="[dotBgClass]"
                />
            </Tooltip>
            <div
                class="inline-flex items-center justify-center transition-opacity duration-150"
                :class="
                    isSelected
                        ? 'opacity-100 pointer-events-auto'
                        : 'pointer-events-none opacity-0 group-hover:pointer-events-auto group-hover:opacity-100 group-focus-within:pointer-events-auto group-focus-within:opacity-100'
                "
            >
                <CheckboxCore
                    :model-value="isSelected"
                    size="xs"
                    :color="uiColor"
                    :aria-label="selectionAriaLabel"
                    @click.stop
                    @update:model-value="(v) => emit('toggle-select', row, Boolean(v))"
                />
            </div>
        </td>
        <!-- Colonne Actions - au début -->
        <td v-if="showActionsColumn && entityType" class="w-12 relative">
            <Tooltip v-if="!showSelection && hasState && dotTooltip" :content="dotTooltip" placement="right" :color="dotColor" responsive="md">
                <span
                    data-no-row-select
                    class="absolute -top-6 -left-3 w-2.5 h-2.5 rounded-full ring-1 ring-base-300 opacity-90"
                    :class="[dotBgClass]"
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
                :show-inline-shortcuts="false"
                @action="handleAction"
            />
        </td>
        <td
            v-for="(col, idx) in columns"
            :key="col.id"
            :class="{
              'max-w-md': RICH_CONTENT_COLUMNS.has(col.id),
            }"
        >
            <div class="relative min-w-0 max-w-full wrap-break-word">
                <Tooltip v-if="!showSelection && !showActionsColumn && idx === 0 && hasState && dotTooltip" :content="dotTooltip" placement="right" :color="dotColor" responsive="md">
                    <span
                        data-no-row-select
                        class="absolute -top-6 -left-3 w-2.5 h-2.5 rounded-full ring-1 ring-base-300 opacity-90"
                        :class="[dotBgClass]"
                    />
                </Tooltip>
                <CellRenderer
                    :cell="getCell(col)"
                    :ui-color="uiColor"
                    :entity="rowEntity"
                    :on-route-click="isNameColumn(col) && entityType && getCell(col)?.type === 'route' ? handleNameRouteClick : undefined"
                />
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
            @close="closeContextMenu"
            @action="handleAction"
        />
    </Teleport>
</template>


