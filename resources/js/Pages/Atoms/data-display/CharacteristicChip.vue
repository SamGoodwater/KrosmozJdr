<script setup>
/**
 * CharacteristicChip — wrapper vers CharacteristicProperty (chips tableaux / listes).
 *
 * @props {Object} item — { icon, name, label, shortLabel, value, unit, color, tooltip, area? } — si `area` est défini, icône de zone en fin de chip (après le texte).
 * @props {String} labelMode - 'full' | 'short' | 'icon-only'
 */
import { computed } from "vue";
import AreaDisplay from "@/Pages/Molecules/entity/spell/AreaDisplay.vue";
import CharacteristicProperty from "@/Pages/Atoms/data-display/CharacteristicProperty.vue";
import { viewModelFromChipItem } from "@/Composables/entity/useCharacteristicViewModel";
import {
    CHARACTERISTIC_PROPERTY_BADGE,
    CHARACTERISTIC_PROPERTY_DENSITY,
    CHARACTERISTIC_PROPERTY_LAYOUT,
} from "@/Utils/Entity/Constants";

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

const chipModel = computed(() => viewModelFromChipItem(props.item));

const hasArea = computed(
    () => props.item.area != null && String(props.item.area).trim() !== "",
);

const hasValue = computed(() => {
    const v = props.item?.value;
    return v != null && String(v).trim() !== "";
});

/** Affiche le bloc texte/icône caractéristique ; masqué si seule la zone est présente. */
const showProperty = computed(() => !hasArea.value || hasValue.value);

const density = computed(() => {
    if (props.labelMode === "full") return CHARACTERISTIC_PROPERTY_DENSITY.full;
    if (props.labelMode === "short") return CHARACTERISTIC_PROPERTY_DENSITY.short;
    return CHARACTERISTIC_PROPERTY_DENSITY.iconOnly;
});
</script>

<template>
    <span class="inline-flex min-w-0 max-w-full items-center gap-x-1">
        <CharacteristicProperty
            v-if="showProperty"
            :view-model="chipModel"
            :density="density"
            :layout="CHARACTERISTIC_PROPERTY_LAYOUT.inline"
            :badge="CHARACTERISTIC_PROPERTY_BADGE.none"
            size="xs"
            class="min-w-0"
        />
        <AreaDisplay
            v-if="hasArea"
            :area="String(item.area)"
            :icon-only="labelMode === 'icon-only'"
            class="shrink-0"
        />
    </span>
</template>
