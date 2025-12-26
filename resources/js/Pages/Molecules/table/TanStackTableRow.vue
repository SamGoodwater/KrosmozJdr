<script setup>
/**
 * TanStackTableRow Molecule
 *
 * @description
 * Rend une ligne de tableau à partir de `row.cells`.
 */

import CellRenderer from "@/Pages/Atoms/data-display/CellRenderer.vue";

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
});

const emit = defineEmits(["row-click", "row-dblclick", "toggle-select"]);

const getCell = (column) => {
    const cellId = column?.cellId || column?.id;
    return props.row?.cells?.[cellId] || { type: "text", value: null, params: {} };
};

const isInteractiveTarget = (event) => {
    const el = event?.target;
    if (!el || typeof el.closest !== "function") return false;
    return Boolean(el.closest('a,button,input,select,textarea,[role="button"],[data-no-row-select]'));
};
</script>

<template>
    <tr
        class="hover:bg-base-200 transition-colors"
        :class="isSelected ? selectedBgClass : null"
        @click="(e) => { if (!isInteractiveTarget(e)) emit('row-click', row); }"
        @dblclick="(e) => { if (!isInteractiveTarget(e)) emit('row-dblclick', row); }"
    >
        <td v-if="showSelection" class="w-12">
            <input
                type="checkbox"
                class="checkbox checkbox-sm"
                :checked="isSelected"
                @change="emit('toggle-select', row, $event.target.checked)"
                @click.stop
            />
        </td>
        <td v-for="col in columns" :key="col.id">
            <CellRenderer :cell="getCell(col)" />
        </td>
    </tr>
</template>


