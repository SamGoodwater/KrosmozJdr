<script setup>
/**
 * SectionContentSkeleton Molecule
 *
 * @description
 * Placeholder de chargement d’une section CMS, calqué sur le template
 * (texte, média, galerie, tableau, cartes d’entités, fichiers, chartes).
 *
 * @example
 * <SectionContentSkeleton template="text" />
 * <SectionContentSkeleton template="entity_table" />
 */
import { computed } from "vue";
import Skeleton from "@/Pages/Atoms/feedback/Skeleton.vue";
import EntityViewSkeleton from "@/Pages/Molecules/entity/shared/EntityViewSkeleton.vue";

const props = defineProps({
    /** Valeur `SectionType` / `section.template`. */
    template: {
        type: String,
        default: "text",
    },
    /** Titre déjà connu (lazy gate) — barre de header optionnelle. */
    title: {
        type: String,
        default: "",
    },
    showHeader: {
        type: Boolean,
        default: true,
    },
});

const layout = computed(() => {
    const t = String(props.template || "text");
    if (t === "entity_table") return "entity_cards";
    if (t.endsWith("_table")) return "table";
    if (t === "gallery") return "gallery";
    if (t === "image" || t === "video") return "media";
    if (t === "download_catalog") return "files";
    if (t === "characteristic_norms" || t === "characteristic_norms_catalog") return "norms";
    return "text";
});

const tableCols = 5;
const tableRows = 8;
</script>

<template>
    <div class="section-content-skeleton space-y-3" aria-busy="true" :aria-label="`Chargement de la section ${title || ''}`.trim()">
        <div v-if="showHeader" class="flex items-center gap-2 px-0.5 py-1" aria-hidden="true">
            <Skeleton class="!h-5 !w-48 max-w-[60%]" />
        </div>

        <div v-if="layout === 'text'" class="space-y-2 py-1" aria-hidden="true">
            <Skeleton element="longtext" class="!h-3 !w-full" />
            <Skeleton element="longtext" class="!h-3 !w-[92%]" />
            <Skeleton element="longtext" class="!h-3 !w-[78%]" />
            <Skeleton element="longtext" class="!h-3 !w-[88%]" />
            <Skeleton element="longtext" class="!h-3 !w-[54%]" />
        </div>

        <div v-else-if="layout === 'media'" class="space-y-2" aria-hidden="true">
            <Skeleton class="!h-48 !w-full rounded-box" />
            <Skeleton element="smalltext" class="!h-3 !w-40" />
        </div>

        <div v-else-if="layout === 'gallery'" class="grid grid-cols-2 gap-3 sm:grid-cols-3" aria-hidden="true">
            <Skeleton v-for="i in 6" :key="i" class="aspect-square !h-auto !w-full rounded-lg" />
        </div>

        <div v-else-if="layout === 'files'" class="grid grid-cols-1 gap-3 md:grid-cols-2" aria-hidden="true">
            <div
                v-for="i in 4"
                :key="i"
                class="flex flex-col gap-2 rounded-box border border-base-content/20 bg-base-100/40 p-4"
            >
                <Skeleton element="text" class="!h-4 !w-2/3" />
                <Skeleton element="longtext" class="!h-3 !w-full" />
                <Skeleton element="smalltext" class="!h-2 !w-24" />
            </div>
        </div>

        <div v-else-if="layout === 'norms'" class="space-y-2" aria-hidden="true">
            <div
                v-for="i in 5"
                :key="i"
                class="flex items-center gap-3 rounded-lg border border-base-300 bg-base-200/30 px-3 py-2"
            >
                <Skeleton class="!h-4 !w-4 shrink-0 rounded" />
                <Skeleton element="text" class="!h-4 !w-40" />
                <Skeleton element="smalltext" class="ml-auto !h-3 !w-16" />
            </div>
        </div>

        <div
            v-else-if="layout === 'table'"
            class="overflow-hidden rounded-box border border-base-content/20 bg-base-100/40"
            aria-hidden="true"
        >
            <div class="grid grid-cols-5 gap-3 bg-base-200/95 px-3 py-2">
                <Skeleton v-for="c in tableCols" :key="`h-${c}`" class="!h-3 !w-16" />
            </div>
            <div
                v-for="r in tableRows"
                :key="`r-${r}`"
                class="grid grid-cols-5 gap-3 border-t border-base-content/10 px-3 py-2.5"
            >
                <Skeleton v-for="c in tableCols" :key="`c-${r}-${c}`" class="!h-3 !w-full max-w-[7rem]" />
            </div>
        </div>

        <EntityViewSkeleton v-else-if="layout === 'entity_cards'" variant="compact" :count="8" />

        <span class="sr-only">Chargement…</span>
    </div>
</template>
