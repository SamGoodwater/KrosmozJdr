<script setup>
/**
 * CharacteristicChip — atome d'affichage d'une caractéristique (icône + valeur).
 *
 * @description
 * Utilisé pour les rendus "chips" en tableau et cartes.
 * Supporte icônes personnalisées (icons/caracteristics/) et couleurs hex ou token Tailwind.
 */
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";

const props = defineProps({
    item: {
        type: Object,
        required: true,
    },
});

/** Style couleur : hex direct, token Tailwind → var(--color-xxx) */
const colorStyle = computed(() => {
    const c = props.item?.color;
    if (!c || typeof c !== "string") return undefined;
    const t = c.trim();
    if (t.startsWith("#")) return { color: t };
    if (t.includes("-")) return { color: `var(--color-${t})` };
    return undefined;
});
</script>

<template>
    <Tooltip
        :content="item.tooltip || item.label || String(item.value)"
        placement="top"
        class="inline-flex items-center gap-1"
    >
        <Icon
            v-if="item.icon"
            :source="item.icon"
            :alt="item.tooltip || item.label || ''"
            size="xs"
            class="shrink-0 opacity-80"
            :style="colorStyle"
        />
        <span class="text-xs" :style="colorStyle">{{ item.value }}</span>
    </Tooltip>
</template>
