<script setup>
/**
 * EntityPropertyDisplay — façade par `fieldKey` vers CharacteristicProperty + view model unifié.
 *
 * @props {string} fieldKey
 * @props {Object} entity
 * @props {string} entityType
 * @props {string} displayMode - minimal | compact | extended | detailed
 * @props {Object} [descriptors]
 * @props {Object} [tableMeta]
 * @props {string} [variant] - badge | icon | inline
 * @props {string} [size]
 * @props {string} [formulaResolved]
 * @props {string} [formulaRaw]
 * @props {Array} [levelTable]
 * @props {Object|null|undefined} [runtime] — ex. resolved-stats ; si omis, inject du contexte (provideCharacteristicRuntime)
 * @props {string} [presentation] - `default` | `spell-types-icons-only` (types de sort : icônes seules, via toCell)
 * @props {boolean} [hideFieldLabel] — masque « Cat. », « Types », etc. sur {@link CharacteristicProperty}
 * @props {boolean} [hideCharacteristicIcon] — masque l’icône BDD (ex. PO remplacée par une icône externe)
 * @props {Object} [toCellOptions] — fusionné dans les options passées à `entity.toCell` / cellOptions du composable
 */
import { computed, inject, unref } from "vue";
import { CHARACTERISTIC_RUNTIME_INJECT_KEY } from "@/Composables/entity/characteristicRuntimeContext";
import ElementDisplay from "@/Pages/Atoms/data-display/ElementDisplay.vue";
import CharacteristicProperty from "@/Pages/Atoms/data-display/CharacteristicProperty.vue";
import SpellTypeBadge from "@/Pages/Molecules/entity/spell/SpellTypeBadge.vue";
import { useCharacteristicViewModel } from "@/Composables/entity/useCharacteristicViewModel";
import { PROPERTY_DISPLAY_MODES } from "@/Utils/Entity/Constants";
import {
    CHARACTERISTIC_PROPERTY_BADGE,
    CHARACTERISTIC_PROPERTY_DENSITY,
    CHARACTERISTIC_PROPERTY_LAYOUT,
} from "@/Utils/Entity/Constants";

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
    /** Prioritaire sur le payload injecté par la page (provideCharacteristicRuntime) */
    runtime: { type: Object, default: undefined },
    presentation: {
        type: String,
        default: "default",
        validator: (v) => ["default", "spell-types-icons-only"].includes(v),
    },
    hideFieldLabel: { type: Boolean, default: false },
    hideCharacteristicIcon: { type: Boolean, default: false },
    toCellOptions: { type: Object, default: () => ({}) },
});

const injectedRuntime = inject(CHARACTERISTIC_RUNTIME_INJECT_KEY, null);

/** Prop explicite > contexte fourni par la page fiche */
const resolvedRuntime = computed(() => {
    if (props.runtime !== undefined) {
        return props.runtime;
    }
    if (injectedRuntime == null) {
        return null;
    }
    return unref(injectedRuntime);
});

const mergedCellOptions = computed(() => ({
    size: props.size,
    context:
        props.displayMode === PROPERTY_DISPLAY_MODES.minimal
            ? "minimal"
            : props.displayMode === PROPERTY_DISPLAY_MODES.compact
              ? "compact"
              : "extended",
    ctx: props.tableMeta && typeof props.tableMeta === "object" ? props.tableMeta : {},
    ...props.toCellOptions,
}));

const displayOptions = computed(() => ({
    fieldKey: props.fieldKey,
    entity: props.entity,
    entityType: props.entityType,
    descriptors: props.descriptors,
    tableMeta: props.tableMeta,
    formulaResolved: props.formulaResolved,
    formulaRaw: props.formulaRaw,
    levelTable: props.levelTable,
    runtime: resolvedRuntime.value,
    cellOptions: mergedCellOptions.value,
}));

const spellTypesIconItems = computed(() => {
    if (props.presentation !== "spell-types-icons-only" || props.fieldKey !== "spell_types") {
        return [];
    }
    const ent = props.entity;
    if (!ent || typeof ent.toCell !== "function") {
        return [];
    }
    try {
        const c = ent.toCell("spell_types", mergedCellOptions.value);
        if (c?.type !== "spell_types" || !Array.isArray(c.params?.items)) {
            return [];
        }
        return c.params.items;
    } catch {
        return [];
    }
});

const { viewModel, hasFormula, levelTable: tableLevel } = useCharacteristicViewModel(displayOptions);

const useCharacteristicFormula = computed(
    () =>
        props.displayMode === PROPERTY_DISPLAY_MODES.detailed &&
        (hasFormula.value || (Array.isArray(tableLevel.value) && tableLevel.value.length > 0)),
);

const densityForMode = computed(() => {
    switch (props.displayMode) {
        case PROPERTY_DISPLAY_MODES.minimal:
            return CHARACTERISTIC_PROPERTY_DENSITY.iconOnly;
        case PROPERTY_DISPLAY_MODES.compact:
            return CHARACTERISTIC_PROPERTY_DENSITY.short;
        default:
            return CHARACTERISTIC_PROPERTY_DENSITY.full;
    }
});

const badgeForVariant = computed(() =>
    props.variant === "badge" ? CHARACTERISTIC_PROPERTY_BADGE.solid : CHARACTERISTIC_PROPERTY_BADGE.none,
);

/** Mode detailed + formule : carte (comme l’ancien CharacteristicFormula), sans badge */
const effectiveBadge = computed(() =>
    useCharacteristicFormula.value ? CHARACTERISTIC_PROPERTY_BADGE.none : badgeForVariant.value,
);

const isElementField = computed(() => props.fieldKey === "element");

const elementValue = computed(() => {
    const data = props.entity?._data ?? props.entity;
    return data?.element ?? 0;
});
</script>

<template>
    <ElementDisplay v-if="isElementField" :element="elementValue" :size="size" />

    <template v-else-if="fieldKey === 'spell_types' && presentation === 'spell-types-icons-only'">
        <span
            v-if="spellTypesIconItems.length > 0"
            class="inline-flex min-w-0 max-w-full flex-wrap items-center gap-1"
        >
            <SpellTypeBadge
                v-for="(it, idx) in spellTypesIconItems"
                :key="String(it.id ?? idx)"
                :name="it.name"
                :color="it.color"
                :icon-hint="it.icon ?? null"
                :show-label="false"
                size="xs"
            />
        </span>
    </template>

    <CharacteristicProperty
        v-else
        :view-model="viewModel"
        :density="densityForMode"
        :layout="useCharacteristicFormula ? CHARACTERISTIC_PROPERTY_LAYOUT.card : CHARACTERISTIC_PROPERTY_LAYOUT.inline"
        :badge="effectiveBadge"
        :show-value="variant !== 'icon'"
        :show-label="!hideFieldLabel"
        :show-icon="!hideCharacteristicIcon"
        :size="size"
    />
</template>
