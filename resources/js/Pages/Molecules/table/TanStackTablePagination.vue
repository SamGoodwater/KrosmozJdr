<script setup>
/**
 * TanStackTablePagination Molecule
 *
 * @description
 * Pagination côté client (TanStack Table state).
 */

import { computed } from "vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";

const props = defineProps({
    pageIndex: { type: Number, required: true },
    pageCount: { type: Number, required: true },
    pageSize: { type: Number, required: true },
    totalRows: { type: Number, required: true },
    perPageOptions: { type: Array, default: () => [10, 25, 50, 100] },
    canPrev: { type: Boolean, default: false },
    canNext: { type: Boolean, default: false },
    /**
     * Taille UI (DaisyUI) appliquée aux contrôles de pagination.
     * Valeurs attendues: xs|sm|md|lg (fallback md).
     */
    uiSize: { type: String, default: "md" },
});

const emit = defineEmits(["prev", "next", "set-page-size"]);

const selectSizeClass = computed(() => {
    if (props.uiSize === "xs") return "select-xs";
    if (props.uiSize === "sm") return "select-sm";
    if (props.uiSize === "lg") return "select-lg";
    return "select-md";
});

const btnSize = computed(() => {
    if (props.uiSize === "xs") return "xs";
    if (props.uiSize === "lg") return "lg";
    if (props.uiSize === "md") return "md";
    return "sm";
});
</script>

<template>
    <div class="flex items-center justify-between gap-3">
        <div class="text-sm text-base-content/70">
            {{ totalRows }} lignes
        </div>

        <div class="flex items-center gap-2">
            <label class="flex items-center gap-2 text-sm text-base-content/70">
                <span class="hidden sm:inline">Lignes</span>
                <select
                    class="select select-bordered"
                    :class="selectSizeClass"
                    :value="pageSize"
                    @change="emit('set-page-size', $event.target.value)"
                    title="Lignes par page"
                >
                    <option v-for="opt in perPageOptions" :key="String(opt)" :value="opt">
                        {{ opt }}
                    </option>
                </select>
            </label>

            <Btn :size="btnSize" variant="ghost" :disabled="!canPrev" @click="emit('prev')">
                Précédent
            </Btn>
            <span class="text-sm">
                Page {{ pageIndex + 1 }} / {{ pageCount || 1 }}
            </span>
            <Btn :size="btnSize" variant="ghost" :disabled="!canNext" @click="emit('next')">
                Suivant
            </Btn>
        </div>
    </div>
</template>


