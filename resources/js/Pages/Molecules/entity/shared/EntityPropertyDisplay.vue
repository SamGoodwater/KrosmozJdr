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
 * @props {string} [characteristicLabelImageSource] — remplace le libellé texte (ex. icône CàC à la place de « PO : »)
 * @props {string} [characteristicLabelImageAlt] — alt de l’image libellé
 * @props {string} [characteristicValueTextClass] — classes sur la valeur (ex. text-red-600)
 * @props {Object} [toCellOptions] — fusionné dans les options passées à `entity.toCell` / cellOptions du composable
 */
import { computed, inject, unref } from "vue";
import { CHARACTERISTIC_RUNTIME_INJECT_KEY } from "@/Composables/entity/characteristicRuntimeContext";
import ElementDisplay from "@/Pages/Atoms/data-display/ElementDisplay.vue";
import EntityStateBadge from "@/Pages/Atoms/data-display/EntityStateBadge.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import CharacteristicProperty from "@/Pages/Atoms/data-display/CharacteristicProperty.vue";
import SpellTypeBadge from "@/Pages/Molecules/entity/spell/SpellTypeBadge.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import { useCharacteristicViewModel } from "@/Composables/entity/useCharacteristicViewModel";
import { PROPERTY_DISPLAY_MODES } from "@/Utils/Entity/Constants";
import { resolveEntityFieldUi } from "@/Utils/Entity/entity-view-ui";
import {
    CHARACTERISTIC_PROPERTY_BADGE,
    CHARACTERISTIC_PROPERTY_DENSITY,
    CHARACTERISTIC_PROPERTY_LAYOUT,
} from "@/Utils/Entity/Constants";
import {
    formatConditionDispellable,
    getConditionDispellableIcon,
    resolveEntityDissipable,
} from "@/Composables/condition/conditionDisplay";

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
    characteristicLabelImageSource: { type: String, default: "" },
    characteristicLabelImageAlt: { type: String, default: "" },
    characteristicValueTextClass: { type: String, default: "" },
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

const isStateField = computed(() => props.fieldKey === "state");

const isConditionDissipableField = computed(
    () =>
        props.fieldKey === "dissipable" &&
        ["condition", "conditions"].includes(String(props.entityType || "")),
);

const dissipableValue = computed(() => {
    const data = props.entity?._data ?? props.entity;
    return resolveEntityDissipable(data?.dissipable);
});

const dissipableLabel = computed(() => formatConditionDispellable(dissipableValue.value) || "");

const dissipablePropertyViewModel = computed(() => ({
    key: "dissipable",
    name: dissipableLabel.value,
    shortName: dissipableLabel.value,
    icon: getConditionDispellableIcon(dissipableValue.value) || "",
    color: "",
    helper: dissipableValue.value ? "Retirable par dissipation." : "Impossible à dissiper.",
    descriptions: "",
    subtitle: "",
    unit: "",
    displayValue: dissipableLabel.value,
    rawValue: dissipableValue.value,
    hideWhenEmpty: false,
    hideWhenFalse: false,
    characteristicType: "boolean",
    hiddenWhenEmpty: false,
    formulaBdd: "",
    formulaDisplay: "",
    formulaMetaResolved: "",
    formulaMetaRaw: "",
    runtimeFormula: "",
    substituted: "",
    placeholders: [],
    levelTable: [],
    tooltipLine: dissipableValue.value ? "Retirable par dissipation." : "Impossible à dissiper.",
    hideDisplayValueInTooltip: true,
}));

const entityStateValue = computed(() => {
    const data = props.entity?._data ?? props.entity;
    return data?.state ?? null;
});

const stateFieldUi = computed(() =>
    resolveEntityFieldUi({
        fieldKey: "state",
        descriptors: props.descriptors,
        tableMeta: props.tableMeta,
        entityType: props.entityType,
    }),
);

const statePropertyTextSizeClass = computed(() => {
    const map = { xs: "text-xs", sm: "text-sm", md: "text-base" };
    return map[props.size] ?? "text-sm";
});
</script>

<template>
    <ElementDisplay v-if="isElementField" :element="elementValue" :size="size" />

    <CharacteristicProperty
        v-else-if="isConditionDissipableField"
        :view-model="dissipablePropertyViewModel"
        :density="densityForMode"
        :layout="CHARACTERISTIC_PROPERTY_LAYOUT.inline"
        :badge="effectiveBadge"
        :show-value="variant !== 'icon'"
        :show-label="!hideFieldLabel"
        :show-icon="!hideCharacteristicIcon"
        :size="size"
    />

    <template v-else-if="isStateField">
        <Tooltip
            class="inline-flex max-w-full min-w-0"
            :content="stateFieldUi.tooltip"
            :disabled="!stateFieldUi.tooltip"
            placement="top"
        >
            <span
                class="characteristic-property text-base-content inline-flex max-w-full min-w-0 flex-wrap items-center gap-1"
                :class="statePropertyTextSizeClass"
            >
                <template v-if="displayMode === PROPERTY_DISPLAY_MODES.minimal">
                    <EntityStateBadge :state="entityStateValue" :size="size" variant="soft" :tooltip="false" />
                </template>
                <template v-else-if="displayMode === PROPERTY_DISPLAY_MODES.compact">
                    <Icon
                        v-if="!hideCharacteristicIcon && stateFieldUi.icon"
                        :source="stateFieldUi.icon"
                        :alt="stateFieldUi.label || ''"
                        size="xs"
                        class="shrink-0 opacity-90"
                    />
                    <span v-if="!hideFieldLabel && stateFieldUi.shortLabel" class="truncate text-base-content/90">
                        <template v-if="variant !== 'icon'">{{ stateFieldUi.shortLabel }}:</template>
                        <template v-else>{{ stateFieldUi.shortLabel }}</template>
                    </span>
                    <EntityStateBadge
                        v-if="variant !== 'icon'"
                        :state="entityStateValue"
                        :size="size"
                        variant="soft"
                        :tooltip="false"
                    />
                </template>
                <template v-else>
                    <Icon
                        v-if="!hideCharacteristicIcon && stateFieldUi.icon"
                        :source="stateFieldUi.icon"
                        :alt="stateFieldUi.label || ''"
                        size="xs"
                        class="shrink-0 opacity-90"
                    />
                    <span v-if="!hideFieldLabel && stateFieldUi.label" class="truncate text-base-content/90">
                        <template v-if="variant !== 'icon'">{{ stateFieldUi.label }}:</template>
                        <template v-else>{{ stateFieldUi.label }}</template>
                    </span>
                    <EntityStateBadge
                        v-if="variant !== 'icon'"
                        :state="entityStateValue"
                        :size="size"
                        variant="soft"
                        :tooltip="false"
                    />
                </template>
            </span>
        </Tooltip>
    </template>

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
        :label-image-source="characteristicLabelImageSource"
        :label-image-alt="characteristicLabelImageAlt"
        :value-text-class="characteristicValueTextClass"
    />
</template>
