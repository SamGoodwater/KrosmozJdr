<script setup>
/**
 * CharacteristicFormula — wrapper de compatibilité vers CharacteristicProperty.
 *
 * @props {Object} def - Définition (key, name, short_name, icon, color, unit, descriptions)
 * @props {string|number} value - Valeur affichée
 * @props {string} [formulaResolved]
 * @props {string} [formulaRaw]
 * @props {Array<{level, value}>} [levelTable]
 * @props {string} [unit]
 * @props {string} [displayMode] - 'minimal'|'compact'|'extended'|'detailed'
 * @props {boolean} [compact] - Legacy: si true, équivaut à displayMode='minimal'
 */
import { computed } from "vue";
import CharacteristicProperty from "@/Pages/Atoms/data-display/CharacteristicProperty.vue";
import { viewModelFromFormulaGroupItem } from "@/Composables/entity/useCharacteristicViewModel";
import {
    CHARACTERISTIC_PROPERTY_BADGE,
    CHARACTERISTIC_PROPERTY_DENSITY,
    CHARACTERISTIC_PROPERTY_LAYOUT,
    PROPERTY_DISPLAY_MODES,
} from "@/Utils/Entity/Constants";

const props = defineProps({
    def: { type: Object, required: true },
    value: { type: [String, Number], default: "" },
    formulaResolved: { type: String, default: "" },
    formulaRaw: { type: String, default: "" },
    levelTable: { type: Array, default: () => [] },
    unit: { type: String, default: "" },
    displayMode: {
        type: String,
        default: null,
        validator: (v) => !v || ["minimal", "compact", "extended", "detailed"].includes(v),
    },
    compact: { type: Boolean, default: false },
});

const effectiveDisplayMode = computed(() => {
    if (props.displayMode) return props.displayMode;
    return props.compact ? PROPERTY_DISPLAY_MODES.minimal : PROPERTY_DISPLAY_MODES.extended;
});

const groupItem = computed(() => ({
    type: "formula",
    def: props.def,
    value: props.value,
    formulaResolved: props.formulaResolved,
    formulaRaw: props.formulaRaw,
    levelTable: props.levelTable,
    unit: props.unit,
}));

const model = computed(() => viewModelFromFormulaGroupItem(groupItem.value));

const density = computed(() => {
    const m = effectiveDisplayMode.value;
    if (m === PROPERTY_DISPLAY_MODES.minimal) return CHARACTERISTIC_PROPERTY_DENSITY.iconOnly;
    if (m === PROPERTY_DISPLAY_MODES.compact) return CHARACTERISTIC_PROPERTY_DENSITY.short;
    return CHARACTERISTIC_PROPERTY_DENSITY.full;
});

const layout = computed(() => {
    const m = effectiveDisplayMode.value;
    if (m === PROPERTY_DISPLAY_MODES.extended || m === PROPERTY_DISPLAY_MODES.detailed) {
        return CHARACTERISTIC_PROPERTY_LAYOUT.card;
    }
    return CHARACTERISTIC_PROPERTY_LAYOUT.inline;
});
</script>

<template>
    <CharacteristicProperty
        :view-model="model"
        :density="density"
        :layout="layout"
        :badge="CHARACTERISTIC_PROPERTY_BADGE.none"
        size="sm"
    />
</template>
