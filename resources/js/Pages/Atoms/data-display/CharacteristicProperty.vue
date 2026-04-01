<script setup>
/**
 * CharacteristicProperty — affichage unifié d'une caractéristique (tooltip riche unique).
 *
 * @description
 * S’appuie sur le view model (`useCharacteristicViewModel` ou `viewModel` pré-calculé).
 * Variantes : densité (full / short / icon-only), habillage badge (none / solid / outline), layout (inline / card).
 *
 * @props {Object} [viewModel] — Si fourni, utilisé tel quel (prioritaire sur le mode entité).
 * @props {string} [fieldKey] — Mode entité : clé champ
 * @props {Object} [entity]
 * @props {string} [entityType]
 * @props {Object} [descriptors]
 * @props {Object} [tableMeta]
 * @props {Object|null} [runtime] — ex. payload resolved-stats racine `{ computed: { [key]: {...} } }`
 * @props {Array} [levelTable]
 * @props {string} [formulaResolved]
 * @props {string} [formulaRaw]
 * @props {string} density — full | short | icon-only
 * @props {string} badge — none | solid | outline
 * @props {string} layout — inline | card
 * @props {boolean} [showValue] — Afficher la valeur dans le déclencheur (désactiver pour l’ancien variant `icon` de PropertyDisplay)
 * @props {string} size — xs | sm | md
 *
 * @example
 * <CharacteristicProperty :view-model="vm" density="short" layout="inline" badge="none" />
 * <CharacteristicProperty field-key="pa" :entity="monster" entity-type="monster" density="full" layout="card" />
 */
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Badge from "@/Pages/Atoms/data-display/Badge.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import CharacteristicPropertyTooltip from "@/Pages/Molecules/data-display/CharacteristicPropertyTooltip.vue";
import { useCharacteristicViewModel } from "@/Composables/entity/useCharacteristicViewModel";
import {
    getCharacteristicColorStyle,
    getCharacteristicContainerStyle,
} from "@/Composables/entity/useCharacteristicDisplay";
import {
    CHARACTERISTIC_PROPERTY_BADGE,
    CHARACTERISTIC_PROPERTY_DENSITY,
    CHARACTERISTIC_PROPERTY_LAYOUT,
} from "@/Utils/Entity/Constants";

const props = defineProps({
    viewModel: {
        type: Object,
        default: null,
    },
    fieldKey: { type: String, default: "" },
    entity: { type: Object, default: null },
    entityType: { type: String, default: "" },
    descriptors: { type: Object, default: () => ({}) },
    tableMeta: { type: Object, default: () => ({}) },
    runtime: { type: Object, default: null },
    levelTable: { type: Array, default: () => [] },
    formulaResolved: { type: String, default: "" },
    formulaRaw: { type: String, default: "" },
    density: {
        type: String,
        default: CHARACTERISTIC_PROPERTY_DENSITY.full,
        validator: (v) => Object.values(CHARACTERISTIC_PROPERTY_DENSITY).includes(v),
    },
    badge: {
        type: String,
        default: CHARACTERISTIC_PROPERTY_BADGE.none,
        validator: (v) => Object.values(CHARACTERISTIC_PROPERTY_BADGE).includes(v),
    },
    layout: {
        type: String,
        default: CHARACTERISTIC_PROPERTY_LAYOUT.inline,
        validator: (v) => Object.values(CHARACTERISTIC_PROPERTY_LAYOUT).includes(v),
    },
    showValue: { type: Boolean, default: true },
    size: {
        type: String,
        default: "sm",
        validator: (v) => ["xs", "sm", "md"].includes(v),
    },
});

const entityOpts = computed(() => ({
    fieldKey: props.fieldKey,
    entity: props.entity,
    entityType: props.entityType,
    descriptors: props.descriptors,
    tableMeta: props.tableMeta,
    runtime: props.runtime,
    levelTable: props.levelTable,
    formulaResolved: props.formulaResolved,
    formulaRaw: props.formulaRaw,
}));

const hasEntityBinding = computed(
    () => Boolean(props.fieldKey && props.entity && props.entityType),
);

const { viewModel: vmFromEntity } = useCharacteristicViewModel(entityOpts);

const model = computed(() => {
    if (props.viewModel && typeof props.viewModel === "object") {
        return props.viewModel;
    }
    if (hasEntityBinding.value) {
        return vmFromEntity.value;
    }
    return {};
});

const displayText = computed(() => {
    const d = model.value?.displayValue;
    if (d === undefined || d === null || d === "") return "—";
    return String(d);
});

const valueStyle = computed(() => getCharacteristicColorStyle(model.value?.color) ?? {});
const containerStyle = computed(() =>
    props.layout === CHARACTERISTIC_PROPERTY_LAYOUT.card
        ? getCharacteristicContainerStyle(model.value?.color) ?? {}
        : {},
);

const effectiveBadgeColor = computed(() => {
    const c = model.value?.color;
    if (!c || typeof c !== "string") return "neutral";
    return c.trim();
});

const badgeSize = computed(() => {
    const map = { xs: "xs", sm: "sm", md: "md" };
    return map[props.size] ?? "sm";
});

const iconSize = computed(() => {
    const map = { xs: "xs", sm: "xs", md: "sm" };
    return map[props.size] ?? "xs";
});

const textSizeClass = computed(() => {
    const map = { xs: "text-xs", sm: "text-sm", md: "text-base" };
    return map[props.size] ?? "text-sm";
});

const isCard = computed(() => props.layout === CHARACTERISTIC_PROPERTY_LAYOUT.card);
const isShort = computed(() => props.density === CHARACTERISTIC_PROPERTY_DENSITY.short);
const isIconOnly = computed(() => props.density === CHARACTERISTIC_PROPERTY_DENSITY.iconOnly);
const useBadge = computed(
    () =>
        props.badge === CHARACTERISTIC_PROPERTY_BADGE.solid ||
        props.badge === CHARACTERISTIC_PROPERTY_BADGE.outline,
);
const badgeVariant = computed(() =>
    props.badge === CHARACTERISTIC_PROPERTY_BADGE.outline ? "outline" : "soft",
);
</script>

<template>
    <Tooltip placement="top" class="inline-flex max-w-full min-w-0">
        <template #content>
            <CharacteristicPropertyTooltip :model="model" />
        </template>

        <!-- Badge -->
        <Badge
            v-if="useBadge"
            :color="effectiveBadgeColor"
            :size="badgeSize"
            :variant="badgeVariant"
            class="inline-flex max-w-full min-w-0 items-center gap-1"
        >
            <Icon
                v-if="model.icon"
                :source="model.icon"
                :alt="model.name || ''"
                :size="iconSize"
                class="shrink-0 opacity-90"
            />
            <span v-if="showValue" class="truncate font-medium" :style="valueStyle">{{ displayText }}</span>
        </Badge>

        <!-- Carte (hors badge) -->
        <div
            v-else-if="isCard"
            class="characteristic-property inline-block min-w-0 rounded-box border border-base-300 px-2.5 py-2 backdrop-blur-sm transition-shadow"
            :style="containerStyle"
        >
            <div class="flex items-center justify-between gap-2">
                <span class="min-w-0 truncate font-medium" :style="valueStyle">{{ displayText }}</span>
                <Icon
                    v-if="model.icon"
                    :source="model.icon"
                    :alt="model.name || ''"
                    :size="iconSize"
                    class="shrink-0 opacity-80"
                    :style="valueStyle"
                />
            </div>
            <p v-if="model.name" class="mt-0.5 text-xs opacity-80">{{ model.name }}</p>
        </div>

        <!-- Inline -->
        <span
            v-else
            class="characteristic-property inline-flex max-w-full min-w-0 items-center gap-1"
            :class="textSizeClass"
        >
            <Icon
                v-if="model.icon"
                :source="model.icon"
                :alt="model.name || ''"
                :size="iconSize"
                class="shrink-0 opacity-80"
                :style="valueStyle"
            />
            <template v-if="isShort">
                <span v-if="model.shortName" class="truncate opacity-80">{{ model.shortName }}:</span>
                <span v-if="showValue" class="truncate font-medium" :style="valueStyle">{{ displayText }}</span>
            </template>
            <template v-else-if="isIconOnly">
                <span v-if="showValue" class="truncate font-medium" :style="valueStyle">{{ displayText }}</span>
            </template>
            <template v-else>
                <span v-if="model.name" class="truncate opacity-80">{{ model.name }}:</span>
                <span v-if="showValue" class="truncate font-medium" :style="valueStyle">{{ displayText }}</span>
            </template>
        </span>
    </Tooltip>
</template>
