<script setup>
/**
 * TanStackTableHeader Molecule
 *
 * @description
 * Header du tableau (labels + indicateur de tri).
 * La mécanique de tri est gérée par `TanStackTable` (Organism).
 */

import CheckboxCore from "@/Pages/Atoms/data-input/CheckboxCore.vue";

/** Colonnes à contenu riche : max-width aligné avec les cellules */
const RICH_CONTENT_COLUMNS = new Set(["spell_summary_profile", "effect_summary"]);

const props = defineProps({
    columns: { type: Array, required: true },
    sortBy: { type: String, default: "" },
    sortOrder: { type: String, default: "asc" },
    showSelection: { type: Boolean, default: false },
    allSelected: { type: Boolean, default: false },
    someSelected: { type: Boolean, default: false },
    /**
     * Couleur UI (Design System) appliquée aux checkboxes de sélection.
     */
    uiColor: { type: String, default: "primary" },
    /**
     * Afficher la colonne Actions (sans label).
     */
    showActionsColumn: { type: Boolean, default: false },
});

const emit = defineEmits(["sort", "toggle-all"]);

const isSortable = (col) => Boolean(col?.sort?.enabled);
const getAriaSort = (col) => {
    if (!isSortable(col)) return "none";
    if (props.sortBy !== col?.id) return "none";
    return props.sortOrder === "desc" ? "descending" : "ascending";
};
</script>

<template>
    <thead>
        <tr>
            <th v-if="showSelection" class="w-8">
                <CheckboxCore
                    :model-value="allSelected"
                    :indeterminate="someSelected"
                    size="xs"
                    :color="uiColor"
                    aria-label="Tout sélectionner"
                    @update:model-value="(v) => emit('toggle-all', Boolean(v))"
                />
            </th>
            <!-- Colonne Actions (sans label) - au début -->
            <th v-if="showActionsColumn" class="w-12"></th>
            <th
                v-for="col in columns"
                :key="col.id"
                scope="col"
                class="select-none"
                :class="{ 'max-w-md': RICH_CONTENT_COLUMNS.has(col.id) }"
                :aria-sort="getAriaSort(col)"
            >
                <button
                    v-if="isSortable(col)"
                    type="button"
                    class="w-full flex items-center gap-2 text-left hover:bg-base-200 rounded px-1 py-0.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/60"
                    :aria-label="`Trier par ${col.label}`"
                    @click="emit('sort', col)"
                >
                    <span>{{ col.label }}</span>
                    <span v-if="sortBy === col.id" class="text-xs opacity-70">
                        {{ sortOrder === 'asc' ? '▲' : '▼' }}
                    </span>
                </button>
                <div v-else class="flex items-center gap-2 px-1 py-0.5">
                    <span>{{ col.label }}</span>
                </div>
            </th>
        </tr>
    </thead>
</template>


