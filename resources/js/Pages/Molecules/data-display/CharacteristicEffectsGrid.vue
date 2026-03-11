<script setup>
/**
 * CharacteristicEffectsGrid — Grille d'effets/caractéristiques responsive.
 *
 * @description
 * Affiche une liste d'items (icône + nom + valeur) en grille responsive :
 * - xs (mobile) : 1 colonne
 * - sm (smartphone) : 2 colonnes
 * - md : 3 colonnes
 * - lg+ : 4 colonnes
 * Réutilisable pour effets Resource, Item, Spell, etc.
 *
 * @props {Array} items - [{ icon, color, label, value, tooltip }]
 */
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
});

const chipItems = computed(() =>
    (props.items || []).filter((item) => item && item.value != null && item.value !== "")
);

const gridClass = computed(() =>
    "grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-3 gap-y-1.5 items-center"
);

const getColorStyle = (item) => {
    const c = item?.color;
    if (!c || typeof c !== "string") return undefined;
    const t = c.trim();
    if (t.startsWith("#")) return { color: t };
    if (t.includes("/")) return undefined;
    return { color: `var(--color-${t})` };
};
</script>

<template>
    <div :class="gridClass">
        <Tooltip
            v-for="(item, idx) in chipItems"
            :key="idx"
            :content="item.tooltip || `${item.label || ''}: ${item.value}`"
            placement="top"
            class="inline-flex items-center gap-1.5 min-w-0"
        >
            <Icon
                v-if="item.icon"
                :source="item.icon"
                :alt="item.label || ''"
                size="xs"
                class="shrink-0 opacity-80"
                :style="getColorStyle(item)"
            />
            <span v-if="item.label" class="text-xs truncate shrink min-w-0" :style="getColorStyle(item)">
                {{ item.label }}:
            </span>
            <span class="text-xs font-medium truncate min-w-0" :style="getColorStyle(item)">
                {{ item.value }}
            </span>
        </Tooltip>
        <span v-if="!chipItems.length" class="text-base-content/40 text-xs col-span-full">—</span>
    </div>
</template>
