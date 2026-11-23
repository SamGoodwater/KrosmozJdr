<script setup>
/**
 * Bloc-colonne de catalogue classe : niveau au-dessus, enfants (icônes) en wrap en dessous.
 *
 * @example
 * <BreedCatalogByLevel level-label="N1">
 *   <button type="button">…</button>
 * </BreedCatalogByLevel>
 */
defineProps({
    /** Libellé principal (ex. « N1 », « Niveau 3 »). */
    levelLabel: { type: String, required: true },
    /** Sous-libellé optionnel. */
    subLabel: { type: String, default: "" },
});
</script>

<template>
    <div
        class="breed-catalog-by-level flex flex-col gap-1.5 items-start shrink-0 max-w-full"
    >
        <div class="min-w-0">
            <div class="text-xs font-semibold uppercase tracking-wide text-base-content/55">
                {{ levelLabel }}
            </div>
            <div
                v-if="subLabel"
                class="text-[10px] font-medium text-base-content/45 leading-snug mt-0.5"
            >
                {{ subLabel }}
            </div>
        </div>
        <div class="min-w-0 flex flex-wrap items-center gap-1.5">
            <slot />
        </div>
    </div>
</template>
