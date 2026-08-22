<script setup>
/**
 * Liste d’équipements en vue Texte (popover minimal au clic).
 *
 * @props {Array} items - Lignes `{id, name, image?}` ou instances `Item`
 */
import { computed } from "vue";
import EntityViewTextLink from "@/Pages/Molecules/entity/shared/EntityViewTextLink.vue";
import ItemViewMinimal from "@/Pages/Molecules/entity/item/ItemViewMinimal.vue";
import { Item } from "@/Models/Entity/Item";

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
    tableMeta: {
        type: Object,
        default: () => ({}),
    },
});

const itemModels = computed(() => {
    const raw = Array.isArray(props.items) ? props.items : [];
    return raw
        .map((row) => (row instanceof Item ? row : new Item(row)))
        .filter((row) => row?.id);
});
</script>

<template>
    <ul
        v-if="itemModels.length"
        class="flex list-none flex-wrap items-center gap-x-3 gap-y-1.5 p-0 m-0"
    >
        <li
            v-for="item in itemModels"
            :key="item.id"
            class="inline-flex min-w-0 max-w-full items-center"
        >
            <EntityViewTextLink
                :entity="item"
                entity-prop="item"
                :minimal-component="ItemViewMinimal"
                fallback-icon="fa-solid fa-shirt"
                ui-color="primary"
                hydrate-type="items"
                :show-actions-on-hover="true"
                hover-width-class="w-80 max-w-[min(92vw,22rem)]"
                :table-meta="tableMeta"
            />
        </li>
    </ul>
</template>
