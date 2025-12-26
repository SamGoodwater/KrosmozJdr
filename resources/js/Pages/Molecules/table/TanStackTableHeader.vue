<script setup>
/**
 * TanStackTableHeader Molecule
 *
 * @description
 * Header du tableau (labels + indicateur de tri).
 * La mécanique de tri est gérée par `TanStackTable` (Organism).
 */

const props = defineProps({
    columns: { type: Array, required: true },
    sortBy: { type: String, default: "" },
    sortOrder: { type: String, default: "asc" },
    showSelection: { type: Boolean, default: false },
    allSelected: { type: Boolean, default: false },
    someSelected: { type: Boolean, default: false },
});

const emit = defineEmits(["sort", "toggle-all"]);

const isSortable = (col) => Boolean(col?.sort?.enabled);
</script>

<template>
    <thead>
        <tr>
            <th v-if="showSelection" class="w-12">
                <input
                    type="checkbox"
                    class="checkbox checkbox-sm"
                    :checked="allSelected"
                    :indeterminate.prop="someSelected"
                    @change="emit('toggle-all', $event.target.checked)"
                    aria-label="Tout sélectionner"
                />
            </th>
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


