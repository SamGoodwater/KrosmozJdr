<script setup>
/**
 * EntityPropertyDisplay — Affichage unifié des propriétés d'entité
 *
 * @description
 * Composant dédié et paramétrable pour afficher une propriété selon le mode
 * (minimal, compact, extended, detailed). Utilise le service caractéristiques
 * (slug → couleur, description, icône, unité, nom) et resolveEntityFieldUi.
 *
 * @props {string} fieldKey - Clé du champ
 * @props {Object} entity - Entité (modèle ou objet)
 * @props {string} entityType - Type d'entité (resource, spell, monster, etc.)
 * @props {string} displayMode - minimal | compact | extended | detailed (PROPERTY_DISPLAY_MODES)
 * @props {Object} [descriptors] - Descriptors (getXFieldDescriptors())
 * @props {Object} [tableMeta] - Meta tableau
 * @props {string} [variant] - Override présentation: badge | icon | inline (pour PropertyDisplay)
 * @props {string} [size] - xs | sm | md
 * @props {string} [formulaResolved] - Formule résolue (mode detailed)
 * @props {string} [formulaRaw] - Formule brute (mode detailed)
 * @props {Array} [levelTable] - Tableau niveau→valeur (mode detailed)
 */
import { computed } from "vue";
import { PROPERTY_DISPLAY_MODES } from "@/Utils/Entity/Constants";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import CharacteristicFormula from "@/Pages/Atoms/data-display/CharacteristicFormula.vue";
import ElementDisplay from "@/Pages/Atoms/data-display/ElementDisplay.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import { useEntityPropertyDisplay } from "@/Composables/entity/useEntityPropertyDisplay";
import { getCharacteristicColorStyle } from "@/Composables/entity/useCharacteristicDisplay";

const props = defineProps({
    fieldKey: { type: String, required: true },
    entity: { type: Object, required: true },
    entityType: { type: String, required: true },
    displayMode: {
        type: String,
        default: PROPERTY_DISPLAY_MODES.extended,
        validator: (v) =>
            [
                PROPERTY_DISPLAY_MODES.minimal,
                PROPERTY_DISPLAY_MODES.compact,
                PROPERTY_DISPLAY_MODES.extended,
                PROPERTY_DISPLAY_MODES.detailed,
            ].includes(v),
    },
    descriptors: { type: Object, default: () => ({}) },
    tableMeta: { type: Object, default: () => ({}) },
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
    formulaResolved: { type: String, default: "" },
    formulaRaw: { type: String, default: "" },
    levelTable: { type: Array, default: () => [] },
});

const displayOptions = computed(() => ({
    fieldKey: props.fieldKey,
    entity: props.entity,
    entityType: props.entityType,
    descriptors: props.descriptors,
    tableMeta: props.tableMeta,
    formulaResolved: props.formulaResolved,
    formulaRaw: props.formulaRaw,
    levelTable: props.levelTable,
}));

const {
    property,
    value,
    displayValue,
    unit,
    characteristic,
    hasFormula,
    formulaResolved: resolvedFormula,
    formulaRaw: rawFormula,
    levelTable: tableLevel,
} = useEntityPropertyDisplay(displayOptions);

const propertyConfig = computed(() => ({
    icon: property.value?.icon,
    label: property.value?.label,
    shortLabel: property.value?.shortLabel,
    tooltip: property.value?.tooltip,
    color: property.value?.color,
}));

const defForFormula = computed(() => ({
    key: props.fieldKey,
    name: property.value?.label,
    short_name: property.value?.shortLabel,
    icon: property.value?.icon,
    color: property.value?.color,
    unit: unit.value || characteristic.value?.unit,
    descriptions: property.value?.tooltip,
}));

const useCharacteristicFormula = computed(
    () =>
        props.displayMode === PROPERTY_DISPLAY_MODES.detailed &&
        (hasFormula.value || (Array.isArray(tableLevel.value) && tableLevel.value.length > 0))
);

const isElementField = computed(() => props.fieldKey === "element");

const elementValue = computed(() => {
    const data = props.entity?._data ?? props.entity;
    return data?.element ?? 0;
});
</script>

<template>
    <!-- Champ element : ElementDisplay dédié -->
    <ElementDisplay
        v-if="isElementField"
        :element="elementValue"
        :size="size"
    />

    <!-- Mode detailed avec formule/levelTable : CharacteristicFormula -->
    <CharacteristicFormula
        v-else-if="useCharacteristicFormula"
        :def="defForFormula"
        :value="value"
        :formula-resolved="resolvedFormula"
        :formula-raw="rawFormula"
        :level-table="tableLevel"
        :unit="unit"
        :display-mode="displayMode"
    />

    <!-- Modes minimal, compact, extended : PropertyDisplay adapté -->
    <Tooltip
        v-else
        :content="propertyConfig.tooltip ? `${propertyConfig.tooltip}\n${displayValue}` : displayValue"
        placement="top"
        class="inline-flex"
    >
        <span
            class="inline-flex items-center gap-1 text-xs"
            :style="propertyConfig.color ? getCharacteristicColorStyle(propertyConfig.color) : undefined"
        >
            <Icon
                v-if="propertyConfig.icon"
                :source="propertyConfig.icon"
                :alt="propertyConfig.label || fieldKey"
                :size="size === 'md' ? 'sm' : 'xs'"
                class="shrink-0 opacity-80"
            />
            <template v-if="displayMode === PROPERTY_DISPLAY_MODES.compact && propertyConfig.shortLabel">
                <span class="opacity-80">{{ propertyConfig.shortLabel }}:</span>
            </template>
            <template
                v-else-if="
                    displayMode === PROPERTY_DISPLAY_MODES.extended &&
                    propertyConfig.label
                "
            >
                <span class="opacity-80">{{ propertyConfig.label }}:</span>
            </template>
            <!-- minimal : pas de label, uniquement icône + valeur + unité -->
            <span class="font-medium">{{ displayValue }}</span>
        </span>
    </Tooltip>
</template>
