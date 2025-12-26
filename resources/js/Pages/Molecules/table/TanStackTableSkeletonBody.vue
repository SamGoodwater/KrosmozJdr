<script setup>
/**
 * TanStackTableSkeletonBody Molecule
 *
 * @description
 * Corps du tableau en mode loading (lignes + cellules skeleton).
 */

import CellSkeleton from "@/Pages/Atoms/data-display/CellSkeleton.vue";

const props = defineProps({
    columns: { type: Array, required: true },
    rowsCount: { type: Number, default: 8 },
    showSelection: { type: Boolean, default: false },
});

const skeletonTypeFor = (col) => {
    const t = col?.cell?.type || "text";
    return String(t);
};
</script>

<template>
    <tbody>
        <tr v-for="i in rowsCount" :key="i">
            <td v-if="showSelection" class="w-12">
                <CellSkeleton type="icon" width-class="h-5 w-5" />
            </td>
            <td v-for="col in columns" :key="col.id">
                <CellSkeleton :type="skeletonTypeFor(col)" />
            </td>
        </tr>
    </tbody>
</template>


