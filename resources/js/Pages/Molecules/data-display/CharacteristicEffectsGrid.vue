<script setup>
/**
 * CharacteristicEffectsGrid — Grille d'effets/caractéristiques responsive.
 *
 * @description
 * Affiche une liste d'items (icône + nom + valeur + unité) en grille responsive :
 * - xs (mobile) : 1 colonne
 * - sm (smartphone) : 2 colonnes
 * - md : 3 colonnes
 * - lg+ : 4 colonnes
 * Réutilisable pour effets Resource, Item, Spell, etc.
 * Affiche l'unité (item.unit) après la valeur quand disponible.
 *
 * @props {Array} items - [{ icon, color, name, shortLabel, value, unit, tooltip }]
 * @props {String} labelMode - 'full' | 'short' | 'icon-only' — full: nom complet, short: abrégé, icon-only: icône + valeur + unité
 */
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import { getCharacteristicColorStyle } from "@/Composables/entity/useCharacteristicDisplay";

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
    labelMode: {
        type: String,
        default: "full",
        validator: (v) => ["full", "short", "icon-only"].includes(v),
    },
});

const chipItems = computed(() =>
    (props.items || []).filter((item) => item && item.value != null && item.value !== "")
);

const gridClass = computed(() =>
    "grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-3 gap-y-1.5 items-center"
);

const getColorStyle = (item) => getCharacteristicColorStyle(item?.color);

const displayLabel = (item) => {
    if (props.labelMode === "full") return item?.name ?? item?.label ?? "";
    if (props.labelMode === "short") return item?.shortLabel ?? item?.name ?? item?.label ?? "";
    return "";
};

/** Valeur avec unité si disponible */
const displayValueWithUnit = (item) => {
    const v = item?.value ?? "";
    const unit = item?.unit;
    return unit ? `${v} ${unit}` : v;
};

/** icon-only : icône + valeur + unité (sans label). full/short : icône + label + valeur + unité */
const showLabel = computed(() => props.labelMode !== "icon-only");
</script>

<template>
    <div :class="gridClass">
        <Tooltip
            v-for="(item, idx) in chipItems"
            :key="idx"
            :content="item.tooltip || `${item.name || item.label || ''}: ${displayValueWithUnit(item)}`"
            placement="top"
            class="inline-flex items-center gap-1.5 min-w-0"
        >
            <Icon
                v-if="item.icon"
                :source="item.icon"
                :alt="item.name || item.label || ''"
                size="xs"
                class="shrink-0 opacity-80"
                :style="getColorStyle(item)"
            />
            <template v-if="showLabel">
                <span v-if="displayLabel(item)" class="text-xs truncate shrink min-w-0" :style="getColorStyle(item)">
                    {{ displayLabel(item) }}:
                </span>
            </template>
            <span class="text-xs font-medium truncate min-w-0" :style="getColorStyle(item)">
                {{ displayValueWithUnit(item) }}
            </span>
        </Tooltip>
        <span v-if="!chipItems.length" class="text-base-content/40 text-xs col-span-full">—</span>
    </div>
</template>
