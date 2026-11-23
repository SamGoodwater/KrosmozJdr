<script setup>
/**
 * Rangée de blocs côte à côte (wrap mobile → colonne).
 * Utilisé par le CMS (sections groupées) et la fiche classe (passif + mécanique).
 *
 * @example
 * <AdjacentSectionsRow>
 *   <div class="min-w-0 flex-1">…</div>
 *   <div class="min-w-0 flex-1">…</div>
 * </AdjacentSectionsRow>
 */
defineProps({
    /** Écart Tailwind entre cellules. */
    gapClass: {
        type: String,
        default: "gap-4",
    },
});
</script>

<template>
    <div
        class="adjacent-sections-row w-full min-w-0 flex flex-col md:flex-row md:flex-wrap md:items-stretch"
        :class="gapClass"
    >
        <slot />
    </div>
</template>
