<script setup>
/**
 * TanStackTableHeader Molecule
 *
 * @description
 * Header du tableau (labels + indicateur de tri).
 * La mécanique de tri est gérée par `TanStackTable` (Organism).
 */

import CheckboxCore from "@/Pages/Atoms/data-input/CheckboxCore.vue";

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
                class="select-none"
                :class="{ 'cursor-pointer hover:bg-base-200': isSortable(col) }"
                @click="isSortable(col) && emit('sort', col)"
            >
                <div class="flex items-center gap-2">
                    <span>{{ col.label }}</span>
                    <span v-if="isSortable(col) && sortBy === col.id" class="text-xs opacity-70">
                        {{ sortOrder === 'asc' ? '▲' : '▼' }}
                    </span>
                </div>
            </th>
        </tr>
    </thead>
</template>


