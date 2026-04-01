<script setup>
/**
 * CharacteristicChip — wrapper vers CharacteristicProperty (chips tableaux / listes).
 *
 * @props {Object} item — { icon, name, label, shortLabel, value, unit, color, tooltip, area? }
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

const density = computed(() => {
    if (props.labelMode === "full") return CHARACTERISTIC_PROPERTY_DENSITY.full;
    if (props.labelMode === "short") return CHARACTERISTIC_PROPERTY_DENSITY.short;
    return CHARACTERISTIC_PROPERTY_DENSITY.iconOnly;
});
</script>

<template>
    <AreaDisplay
        v-if="item.area != null && String(item.area).trim() !== ''"
        :area="String(item.area)"
        :icon-only="labelMode === 'icon-only'"
    />
    <CharacteristicProperty
        v-else
        :view-model="chipModel"
        :density="density"
        :layout="CHARACTERISTIC_PROPERTY_LAYOUT.inline"
        :badge="CHARACTERISTIC_PROPERTY_BADGE.none"
        size="xs"
    />
</template>
