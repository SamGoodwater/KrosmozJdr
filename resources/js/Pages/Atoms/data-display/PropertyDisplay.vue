<script setup>
/**
 * PropertyDisplay — wrapper léger vers CharacteristicProperty (affichage unifié).
 *
 * @props {Object} property - { icon, label, tooltip, color, value }
 * @props {String} variant - 'badge' | 'icon' | 'inline'
 * @props {String} size - 'xs' | 'sm' | 'md'
 *
 * @example
 * <PropertyDisplay :property="fieldUi" :value="cell.value" variant="badge" />
 */
import { computed } from "vue";
import CharacteristicProperty from "@/Pages/Atoms/data-display/CharacteristicProperty.vue";
import { viewModelFromLegacyProperty } from "@/Composables/entity/useCharacteristicViewModel";
import {
    CHARACTERISTIC_PROPERTY_BADGE,
    CHARACTERISTIC_PROPERTY_DENSITY,
    CHARACTERISTIC_PROPERTY_LAYOUT,
} from "@/Utils/Entity/Constants";

const props = defineProps({
    property: {
        type: Object,
        default: () => ({}),
    },
    value: {
        type: [String, Number, Boolean],
        default: null,
    },
    variant: {
        type: String,
        default: "inline",
        validator: (v) => ["badge", "icon", "inline"].includes(v),
    },
    size: {
        type: String,
        default: "sm",
        validator: (v) => ["xs", "sm", "md"].includes(v),
    },
});

const model = computed(() => viewModelFromLegacyProperty(props.property, props.value));

const badge = computed(() =>
    props.variant === "badge" ? CHARACTERISTIC_PROPERTY_BADGE.solid : CHARACTERISTIC_PROPERTY_BADGE.none,
);

const showValue = computed(() => props.variant !== "icon");
</script>

<template>
    <CharacteristicProperty
        :view-model="model"
        :density="CHARACTERISTIC_PROPERTY_DENSITY.iconOnly"
        :badge="badge"
        :layout="CHARACTERISTIC_PROPERTY_LAYOUT.inline"
        :show-value="showValue"
        :size="size"
    />
</template>
