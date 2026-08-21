<script setup>
/**
 * MonsterCreatureItemsList — Équipements liés à la créature du monstre
 *
 * @description
 * Même présentation que les sorts : icône / vignette + nom ; clic → `ItemViewMinimal`.
 * N’affiche rien si la créature n’a pas d’objets (cas rare).
 *
 * @example
 * <MonsterCreatureItemsList :creature="creature" :table-meta="tableMeta" />
 */
import { computed } from "vue";
import EntityViewTextLink from "@/Pages/Molecules/entity/shared/EntityViewTextLink.vue";
import ItemViewMinimal from "@/Pages/Molecules/entity/item/ItemViewMinimal.vue";
import { Item } from "@/Models/Entity/Item";

const props = defineProps({
    creature: { type: Object, default: null },
    tableMeta: { type: Object, default: () => ({}) },
    characteristicRuntime: { type: Object, default: null },
    title: { type: String, default: "Équipements" },
    sectionClass: { type: String, default: "" },
});

const itemModels = computed(() => {
    const raw = props.creature?.items;
    if (!Array.isArray(raw) || raw.length === 0) return [];
    return raw.map((row) => (row instanceof Item ? row : new Item(row)));
});

/**
 * @param {object} item
 * @returns {number}
 */
function quantityOf(item) {
    const q = item?.pivot?.quantity ?? item?._data?.pivot?.quantity ?? 1;
    const n = Number(q);
    return Number.isFinite(n) && n > 1 ? n : 0;
}
</script>

<template>
    <div v-if="itemModels.length" class="monster-creature-items-list" :class="sectionClass">
        <p
            class="mb-1 text-[0.625rem] font-semibold uppercase tracking-wide text-base-content/60"
        >
            {{ title }}
        </p>
        <ul class="flex list-none flex-wrap gap-x-3 gap-y-1.5 p-0 m-0">
            <li v-for="item in itemModels" :key="item.id" class="inline-flex min-w-0 max-w-full items-center gap-1">
                <EntityViewTextLink
                    :entity="item"
                    entity-prop="item"
                    :minimal-component="ItemViewMinimal"
                    fallback-icon="fa-solid fa-shield-halved"
                    ui-color="primary"
                    :show-actions-on-hover="false"
                    hover-width-class="w-80 max-w-[min(92vw,22rem)]"
                    :table-meta="tableMeta"
                    :characteristic-runtime="characteristicRuntime"
                />
                <span
                    v-if="quantityOf(item)"
                    class="shrink-0 text-[0.65rem] text-base-content/55"
                >×{{ quantityOf(item) }}</span>
            </li>
        </ul>
    </div>
</template>
