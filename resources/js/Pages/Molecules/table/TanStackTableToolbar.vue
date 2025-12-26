<script setup>
/**
 * TanStackTableToolbar Molecule
 *
 * @description
 * Barre d'outils générique : recherche + colonnes visibles + export + actions de sélection.
 */

import { computed } from "vue";
import Dropdown from "@/Pages/Atoms/action/Dropdown.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";

const props = defineProps({
    searchEnabled: { type: Boolean, default: false },
    searchValue: { type: String, default: "" },
    searchPlaceholder: { type: String, default: "Rechercher…" },
    /**
     * Taille UI (DaisyUI) appliquée aux contrôles de la toolbar.
     * Valeurs attendues: xs|sm|md|lg (fallback md).
     */
    uiSize: { type: String, default: "md" },

    columnVisibilityEnabled: { type: Boolean, default: false },
    columns: { type: Array, default: () => [] },
    visibleColumns: { type: Object, default: () => ({}) },

    exportEnabled: { type: Boolean, default: false },

    selectionCount: { type: Number, default: 0 },
});

const emit = defineEmits([
    "update:search",
    "toggle-column",
    "export",
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
</script>

<template>
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div class="flex-1">
            <input
                v-if="searchEnabled"
                class="input input-bordered w-full"
                :class="inputSizeClass"
                type="search"
                :placeholder="searchPlaceholder"
                :value="searchValue"
                @input="emit('update:search', $event.target.value)"
            />
        </div>

        <div class="flex items-center gap-2 justify-end">
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

            <Btn
                v-if="exportEnabled"
                :size="btnSize"
                variant="ghost"
                class="gap-2"
                @click="emit('export')"
                title="Exporter en CSV"
            >
                <Icon source="fa-solid fa-file-csv" alt="Exporter CSV" size="sm" />
                <span class="hidden md:inline">Exporter</span>
            </Btn>

            <Dropdown
                v-if="columnVisibilityEnabled"
                placement="bottom-end"
                :close-on-content-click="false"
            >
                <template #trigger>
                    <Btn :size="btnSize" variant="ghost" class="gap-2" title="Colonnes visibles">
                        <Icon source="fa-solid fa-columns" alt="Colonnes" size="sm" />
                        <span class="hidden md:inline">Colonnes</span>
                    </Btn>
                </template>
                <template #content>
                    <div class="p-3 w-64">
                        <div class="text-sm font-semibold mb-2">Colonnes visibles</div>
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
                                <input
                                    type="checkbox"
                                    class="checkbox checkbox-sm"
                                    :checked="visibleColumns[col.id] !== false"
                                    :disabled="!canToggleColumn(col)"
                                    @change="emit('toggle-column', col)"
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


