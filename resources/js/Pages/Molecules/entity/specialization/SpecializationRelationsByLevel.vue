<script setup>
/**
 * Blocs par niveau pour une spécialisation (sorts, capacités, équipements, etc.).
 */
import { computed } from "vue";
import SpellViewText from "@/Pages/Molecules/entity/spell/SpellViewText.vue";
import CapabilityViewText from "@/Pages/Molecules/entity/capability/CapabilityViewText.vue";
import ItemViewText from "@/Pages/Molecules/entity/item/ItemViewText.vue";
import ResourceViewText from "@/Pages/Molecules/entity/resource/ResourceViewText.vue";
import ConsumableViewText from "@/Pages/Molecules/entity/consumable/ConsumableViewText.vue";
import { groupRelationsByLevel } from "@/Utils/entity/groupRelationsByLevel";

const props = defineProps({
    specialization: { type: Object, required: true },
});

const raw = computed(() => props.specialization?._data ?? props.specialization);

const relationBlocks = computed(() => {
    const s = raw.value;
    return [
        { key: "spells", label: "Sorts", items: s?.spells ?? [], component: SpellViewText, prop: "spell" },
        {
            key: "capabilities",
            label: "Capacités",
            items: s?.capabilities ?? [],
            component: CapabilityViewText,
            prop: "capability",
        },
        { key: "items", label: "Équipements", items: s?.items ?? [], component: ItemViewText, prop: "item" },
        {
            key: "resources",
            label: "Ressources",
            items: s?.resources ?? [],
            component: ResourceViewText,
            prop: "resource",
        },
        {
            key: "consumables",
            label: "Consommables",
            items: s?.consumables ?? [],
            component: ConsumableViewText,
            prop: "consumable",
        },
    ].map((block) => ({
        ...block,
        ...groupRelationsByLevel(block.items),
    }));
});

const hasAnyRelation = computed(() =>
    relationBlocks.value.some(
        (b) => b.withLevel.length > 0 || b.withoutLevel.length > 0
    )
);
</script>

<template>
    <section v-if="hasAnyRelation" class="space-y-6" aria-label="Progression par niveau">
        <h2 class="text-lg font-semibold">Progression par niveau</h2>

        <div
            v-for="block in relationBlocks"
            :key="block.key"
            v-show="block.withLevel.length || block.withoutLevel.length"
            class="space-y-4 rounded-box border border-base-300 bg-base-100/40 p-4"
        >
            <h3 class="text-sm font-semibold uppercase tracking-wide text-primary-300">
                {{ block.label }}
            </h3>

            <div
                v-for="group in block.withLevel"
                :key="`${block.key}-lv-${group.level}`"
                class="space-y-2"
            >
                <h4 class="text-xs font-medium text-base-content/70">Niveau {{ group.level }}</h4>
                <ul class="flex flex-col gap-1.5">
                    <li v-for="item in group.items" :key="`${block.key}-${item.id}`">
                        <component :is="block.component" v-bind="{ [block.prop]: item }" />
                    </li>
                </ul>
            </div>

            <div v-if="block.withoutLevel.length" class="space-y-2">
                <h4 class="text-xs font-medium text-base-content/70">Sans niveau</h4>
                <ul class="flex flex-col gap-1.5">
                    <li v-for="item in block.withoutLevel" :key="`${block.key}-x-${item.id}`">
                        <component :is="block.component" v-bind="{ [block.prop]: item }" />
                    </li>
                </ul>
            </div>
        </div>
    </section>
</template>
