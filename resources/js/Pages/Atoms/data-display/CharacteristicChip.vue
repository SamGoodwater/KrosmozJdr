<script setup>
/**
 * CharacteristicChip — atome d'affichage d'une caractéristique (icône + label + valeur).
 *
 * @description
 * Utilisé pour les rendus "chips" en tableau et cartes.
 * Supporte icônes personnalisées (icons/caracteristics/) et couleurs hex ou token Tailwind.
 * @props {String} labelMode - 'full' | 'short' | 'icon-only' — full: nom complet, short: abrégé, icon-only: icône seule
 */
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";

const props = defineProps({
    item: {
        type: Object,
        required: true,
    },
    labelMode: {
        type: String,
        default: "full",
        validator: (v) => ["full", "short", "icon-only"].includes(v),
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

/** Tooltip: toujours le nom complet (item.tooltip contient "nom: valeur") */
const tooltipContent = computed(() =>
    props.item?.tooltip || `${props.item?.name || props.item?.label || ""}: ${props.item?.value ?? ""}`
);

/** Label affiché selon labelMode */
const displayLabel = computed(() => {
    if (props.labelMode === "full") return props.item?.name ?? props.item?.label ?? "";
    if (props.labelMode === "short") return props.item?.shortLabel ?? props.item?.name ?? props.item?.label ?? "";
    return "";
});

const showLabelAndValue = computed(() => props.labelMode !== "icon-only");

/** Mode icon-only sans icône : fallback sur la valeur pour garder un contenu visible */
const showValueAsFallback = computed(
    () => props.labelMode === "icon-only" && !props.item?.icon
);
</script>

<template>
    <Tooltip
        :content="tooltipContent"
        placement="top"
        class="inline-flex items-center gap-1"
    >
        <Icon
            v-if="item.icon"
            :source="item.icon"
            :alt="item.tooltip || item.name || item.label || ''"
            size="xs"
            class="shrink-0 opacity-80"
            :style="colorStyle"
        />
        <template v-if="showLabelAndValue || showValueAsFallback">
            <span v-if="showLabelAndValue && displayLabel" class="text-xs" :style="colorStyle">{{ displayLabel }}:</span>
            <span class="text-xs" :style="colorStyle">{{ item.value }}</span>
        </template>
    </Tooltip>
</template>
