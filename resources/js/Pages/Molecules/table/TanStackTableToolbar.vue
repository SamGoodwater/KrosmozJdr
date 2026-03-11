<script setup>
/**
 * TanStackTableToolbar Molecule
 *
 * @description
 * Barre d'outils générique : recherche + colonnes visibles + export + actions de sélection.
 */

import { computed, ref, watch } from "vue";
import Dropdown from "@/Pages/Atoms/action/Dropdown.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import ResponsiveActionButton from "@/Pages/Atoms/action/ResponsiveActionButton.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import CheckboxCore from "@/Pages/Atoms/data-input/CheckboxCore.vue";
import { shiftUiSize } from "@/Utils/atomic-design";

const props = defineProps({
    searchEnabled: { type: Boolean, default: false },
    searchValue: { type: String, default: "" },
    searchPlaceholder: { type: String, default: "Rechercher…" },
    /**
     * Taille UI (DaisyUI) appliquée aux contrôles de la toolbar.
     * Valeurs attendues: xs|sm|md|lg (fallback md).
     */
    uiSize: { type: String, default: "md" },
    /**
     * Couleur UI (Design System) appliquée aux contrôles (inputs/checkbox).
     * Valeurs attendues: primary|secondary|accent|info|success|warning|error|neutral (fallback primary).
     */
    uiColor: { type: String, default: "primary" },

    columnVisibilityEnabled: { type: Boolean, default: false },
    columns: { type: Array, default: () => [] },
    visibleColumns: { type: Object, default: () => ({}) },

    /**
     * Colonnes triables (avec sort.enabled). Utilisé pour le dropdown « Trier par ».
     * Indispensable en vue single-column (line) où les en-têtes de colonnes ne sont pas cliquables.
     */
    sortEnabled: { type: Boolean, default: false },
    sortableColumns: { type: Array, default: () => [] },
    sortBy: { type: String, default: "" },
    sortOrder: { type: String, default: "asc" },

    exportEnabled: { type: Boolean, default: false },
    refreshEnabled: { type: Boolean, default: false },

    selectionCount: { type: Number, default: 0 },
});

const emit = defineEmits([
    "update:search",
    "toggle-column",
    "reset-columns",
    "sort",
    "export",
    "refresh",
    "clear-selection",
]);

const canToggleColumn = (col) => Boolean(col?.hideable !== false && !col?.isMain);

const inputSizeClass = computed(() => {
    if (props.uiSize === "xs") return "input-xs";
    if (props.uiSize === "sm") return "input-sm";
    if (props.uiSize === "lg") return "input-lg";
    return "input-md";
});

const btnSize = computed(() => {
    if (props.uiSize === "xs") return "xs";
    if (props.uiSize === "lg") return "lg";
    if (props.uiSize === "md") return "md";
    return "sm";
});

/**
 * Boutons "actions" (Exporter / Colonnes) plus discrets :
 * on prend la taille UI du tableau et on applique SIZE - 1 (clampé).
 */
const actionBtnSize = computed(() => shiftUiSize(props.uiSize, -1));

/** Valeur locale : feedback immédiat à la saisie, sync avec le parent (preset, clear). */
const searchInputValue = ref(props.searchValue);
watch(
    () => props.searchValue,
    (v) => {
        searchInputValue.value = String(v ?? "");
    },
    { immediate: true },
);
const onSearchInput = (e) => {
    const v = String(e?.target?.value ?? "");
    searchInputValue.value = v;
    emit("update:search", v);
};

const onSortChange = (e) => {
    const val = String(e?.target?.value ?? "").trim();
    if (!val) {
        emit("sort", { columnId: "", order: "asc" });
        return;
    }
    const [columnId, order] = val.split("::");
    if (columnId && (order === "asc" || order === "desc")) {
        emit("sort", { columnId, order });
    }
};
</script>

<template>
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div class="flex-1">
            <input
                v-if="searchEnabled"
                type="search"
                class="input input-bordered w-full"
                :class="inputSizeClass"
                :placeholder="searchPlaceholder"
                :value="searchInputValue"
                @input="onSearchInput"
            />
        </div>

        <div class="flex w-full flex-wrap items-center justify-end gap-2 md:w-auto md:flex-nowrap">
            <Btn
                v-if="selectionCount > 0"
                :size="btnSize"
                variant="ghost"
                class="gap-2"
                @click="emit('clear-selection')"
                title="Vider la sélection"
            >
                <Icon source="fa-solid fa-xmark" alt="Vider sélection" size="sm" />
                <span class="hidden md:inline">Sélection ({{ selectionCount }})</span>
            </Btn>

            <ResponsiveActionButton
                v-if="exportEnabled"
                :size="actionBtnSize"
                :color="uiColor"
                icon="fa-solid fa-file-csv"
                label="Exporter"
                ariaLabel="Exporter en CSV"
                @click="emit('export')"
                title="Exporter en CSV"
            />

            <ResponsiveActionButton
                v-if="refreshEnabled"
                :size="actionBtnSize"
                :color="uiColor"
                icon="fa-solid fa-rotate"
                label="Actualiser"
                ariaLabel="Actualiser les données"
                @click="emit('refresh')"
                title="Actualiser les données"
            />

            <select
                v-if="sortEnabled && sortableColumns.length > 0"
                :class="['select select-bordered', inputSizeClass]"
                :value="sortBy ? `${sortBy}::${sortOrder}` : ''"
                aria-label="Trier par"
                title="Choisir le tri"
                @change="onSortChange"
            >
                <option value="">Trier par…</option>
                <option
                    v-for="col in sortableColumns"
                    :key="`${col.id}-asc`"
                    :value="`${col.id}::asc`"
                >
                    {{ col.label }} (A→Z)
                </option>
                <option
                    v-for="col in sortableColumns"
                    :key="`${col.id}-desc`"
                    :value="`${col.id}::desc`"
                >
                    {{ col.label }} (Z→A)
                </option>
            </select>

            <Dropdown
                v-if="columnVisibilityEnabled"
                placement="bottom-end"
                :close-on-content-click="false"
            >
                <template #trigger>
                    <ResponsiveActionButton
                        :size="actionBtnSize"
                        :color="uiColor"
                        icon="fa-solid fa-columns"
                        label="Colonnes"
                        ariaLabel="Colonnes visibles"
                        title="Colonnes visibles"
                    />
                </template>
                <template #content>
                    <div class="p-3 w-64">
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <div class="text-sm font-semibold">Colonnes visibles</div>
                            <Btn
                                :size="actionBtnSize"
                                variant="ghost"
                                class="gap-2"
                                title="Réinitialiser (revenir aux colonnes par défaut)"
                                @click="emit('reset-columns')"
                            >
                                <Icon source="fa-solid fa-rotate-left" alt="Réinitialiser" size="sm" />
                                <span class="hidden md:inline">Reset</span>
                            </Btn>
                        </div>
                        <div class="space-y-2 max-h-64 overflow-y-auto pr-1">
                            <label
                                v-for="col in columns"
                                :key="col.id"
                                class="flex items-center gap-2"
                                :class="{
                                    'opacity-60 cursor-not-allowed': !canToggleColumn(col),
                                    'cursor-pointer': canToggleColumn(col),
                                }"
                            >
                                <CheckboxCore
                                    :model-value="props.visibleColumns[col.id] !== false"
                                    size="sm"
                                    :color="uiColor"
                                    :disabled="!canToggleColumn(col)"
                                    @update:model-value="(v) => emit('toggle-column', col, Boolean(v))"
                                />
                                <span class="text-sm">{{ col.label }}</span>
                            </label>
                        </div>
                    </div>
                </template>
            </Dropdown>
        </div>
    </div>
</template>


