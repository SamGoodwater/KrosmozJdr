<script setup>
/**
 * EntityViewSkeleton Molecule
 *
 * @description
 * Placeholder de chargement calqué sur la vue Minimal compacte (vignette + titre)
 * ou sur une ligne dense (thumb plus large + plusieurs barres).
 *
 * @example
 * <EntityViewSkeleton variant="compact" :count="8" />
 * <EntityViewSkeleton variant="line" :count="6" />
 */
import { computed } from "vue";
import Skeleton from "@/Pages/Atoms/feedback/Skeleton.vue";

const props = defineProps({
    /**
     * `compact` = carte grille Minimal ; `line` = rangée vue Line.
     */
    variant: {
        type: String,
        default: "compact",
        validator: (v) => ["compact", "line"].includes(v),
    },
    /** Nombre de cartes / lignes fantômes. */
    count: {
        type: Number,
        default: 8,
    },
});

const items = computed(() => {
    const n = Number(props.count);
    const length = Number.isFinite(n) && n > 0 ? Math.min(n, 16) : 8;
    return Array.from({ length }, (_, i) => i + 1);
});
</script>

<template>
    <div
        :class="
            variant === 'line'
                ? 'flex flex-col gap-2 p-2'
                : 'flex flex-wrap gap-3 p-2'
        "
        aria-busy="true"
        aria-label="Chargement des entités"
    >
        <div
            v-for="i in items"
            :key="i"
            class="rounded-box border border-base-300 bg-glass-2xl"
            :class="
                variant === 'line'
                    ? 'w-full p-3'
                    : 'flex-[1_1_280px] min-w-[280px] max-w-full p-2'
            "
            aria-hidden="true"
        >
            <div class="flex" :class="variant === 'line' ? 'gap-3' : 'gap-2'">
                <Skeleton
                    element="image"
                    :class="
                        variant === 'line'
                            ? '!h-20 !w-20 shrink-0 rounded-box'
                            : '!h-14 !w-14 shrink-0 rounded-box'
                    "
                />
                <div class="flex min-w-0 flex-1 flex-col justify-center gap-1.5">
                    <Skeleton element="text" class="!h-4 !w-2/3 max-w-[12rem]" />
                    <Skeleton element="smalltext" class="!h-3 !w-1/3 max-w-[7rem]" />
                    <Skeleton
                        v-if="variant === 'line'"
                        element="longtext"
                        class="!h-3 !w-4/5"
                    />
                </div>
            </div>
        </div>
        <span class="sr-only">Chargement…</span>
    </div>
</template>
