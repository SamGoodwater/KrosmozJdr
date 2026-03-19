<script setup>
/**
 * CharacteristicChip — atome d'affichage d'une caractéristique (icône + label + valeur + unité).
 *
 * @description
 * Utilisé pour les rendus "chips" en tableau et cartes.
 * Supporte icônes personnalisées (icons/caracteristics/) et couleurs hex ou token Tailwind.
 * Affiche l'unité (item.unit) après la valeur quand disponible.
 *
 * @props {String} labelMode - 'full' | 'short' | 'icon-only' — full: nom complet, short: abrégé, icon-only: icône + valeur + unité
 */
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import { getCharacteristicColorStyle } from "@/Composables/entity/useCharacteristicDisplay";

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

const colorStyle = computed(() => getCharacteristicColorStyle(props.item?.color));

/** Valeur affichée avec unité si disponible */
const displayValue = computed(() => {
    const v = props.item?.value ?? "";
    const unit = props.item?.unit;
    return unit ? `${v} ${unit}` : v;
});

/** Tooltip: toujours le nom complet (item.tooltip contient "nom: valeur") */
const tooltipContent = computed(() =>
    props.item?.tooltip || `${props.item?.name || props.item?.label || ""}: ${displayValue.value}`
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
            <span class="text-xs" :style="colorStyle">{{ displayValue }}</span>
        </template>
    </Tooltip>
</template>
