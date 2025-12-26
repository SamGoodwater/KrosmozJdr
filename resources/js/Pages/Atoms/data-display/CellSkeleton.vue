<script setup>
/**
 * CellSkeleton Atom
 *
 * @description
 * Skeleton générique pour une cellule de tableau (loading state).
 * Le rendu s'adapte au `type` de cellule (text/badge/icon/image/route/custom).
 *
 * @example
 * <CellSkeleton type="text" />
 * <CellSkeleton type="image" />
 */

import { computed } from "vue";

const props = defineProps({
    /**
     * Type de cellule (doit correspondre à la spec TanStack Table v2).
     */
    type: {
        type: String,
        default: "text",
    },
    /**
     * Permet de forcer une largeur (classes Tailwind), sinon auto.
     * @example "w-24"
     */
    widthClass: {
        type: String,
        default: "",
    },
});

const shapeClasses = computed(() => {
    const t = String(props.type || "text");

    if (t === "image") return "h-8 w-8 rounded";
    if (t === "badge") return "h-5 w-16 rounded-full";
    if (t === "icon") return "h-5 w-5 rounded";
    if (t === "route") return "h-3 w-40 rounded";
    if (t === "custom") return "h-3 w-32 rounded";
    return "h-3 w-40 rounded"; // text
});

const classes = computed(() => {
    const base = "skeleton bg-base-200/70";
    const width = props.widthClass ? props.widthClass : "";
    return [base, shapeClasses.value, width].filter(Boolean).join(" ");
});
</script>

<template>
    <div class="flex items-center">
        <div :class="classes" />
    </div>
</template>


