<script setup>
/**
 * Liste d’équipements en vue Texte (popover minimal au survol).
 *
 * @props {Array<{id: number, name?: string}>} items
 */
import EntityViewTextLink from "@/Pages/Molecules/entity/shared/EntityViewTextLink.vue";
import ItemViewMinimal from "@/Pages/Molecules/entity/item/ItemViewMinimal.vue";

defineProps({
    items: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <ul v-if="items?.length" class="flex flex-col gap-0.5 text-xs list-none p-0 m-0">
        <li v-for="it in items" :key="it.id" class="min-w-0 truncate">
            <EntityViewTextLink
                :entity="it"
                entity-prop="item"
                :minimal-component="ItemViewMinimal"
                fallback-icon="fa-solid fa-shirt"
                class="text-base-content/90 hover:text-base-content"
            />
        </li>
    </ul>
</template>
