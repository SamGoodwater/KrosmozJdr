<script setup>
/**
 * ResourceIngredientsList — Ingrédients (ressources) en vue texte + aperçu au clic.
 *
 * @description
 * Même présentation que les sorts d’un monstre : icône + nom ; clic → `ResourceViewMinimal`.
 * Quantité affichée à côté du nom si &gt; 1.
 *
 * @props {Array} ingredients - Liste {id, name, image?, pivot?: {quantity}}
 * @example
 * <ResourceIngredientsList :ingredients="item.resources" />
 */
import { computed } from "vue";
import EntityViewTextLink from "@/Pages/Molecules/entity/shared/EntityViewTextLink.vue";
import ResourceViewMinimal from "@/Pages/Molecules/entity/resource/ResourceViewMinimal.vue";
import { Resource } from "@/Models/Entity/Resource";

const props = defineProps({
    ingredients: {
        type: Array,
        default: () => [],
    },
    tableMeta: { type: Object, default: () => ({}) },
});

const resourceModels = computed(() => {
    const raw = props.ingredients || [];
    if (!Array.isArray(raw) || raw.length === 0) return [];
    return raw
        .map((row) => (row instanceof Resource ? row : new Resource(row)))
        .filter((row) => row?.id);
});

const quantityOf = (resource) => {
    const q = resource?.pivot?.quantity ?? resource?._data?.pivot?.quantity ?? resource?._data?.quantity ?? 1;
    const n = Number(q);
    return Number.isFinite(n) && n > 1 ? n : 0;
};
</script>

<template>
    <ul
        v-if="resourceModels.length > 0"
        class="flex list-none flex-wrap items-center gap-x-3 gap-y-1.5 p-0 m-0"
    >
        <li
            v-for="resource in resourceModels"
            :key="resource.id"
            class="inline-flex min-w-0 max-w-full items-center gap-1"
        >
            <EntityViewTextLink
                :entity="resource"
                entity-prop="resource"
                :minimal-component="ResourceViewMinimal"
                fallback-icon="fa-solid fa-gem"
                ui-color="primary"
                :show-actions-on-hover="true"
                hydrate-type="resources"
                hover-width-class="w-[min(92vw,22rem)] max-w-[22rem]"
                :table-meta="tableMeta"
            />
            <span
                v-if="quantityOf(resource)"
                class="shrink-0 text-[0.65rem] text-base-content/55"
            >×{{ quantityOf(resource) }}</span>
        </li>
    </ul>
</template>
