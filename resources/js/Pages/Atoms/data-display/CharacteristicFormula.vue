<script setup>
/**
 * CharacteristicFormula — Atome d'affichage d'une caractéristique à formule (valeur + unité).
 *
 * @description
 * Aligné sur PROPERTY_DISPLAY_MODES (minimal, compact, extended, detailed).
 * - minimal: icône + valeur + unité
 * - compact: icône + label abrégé + valeur + unité
 * - extended: icône + label complet + valeur + unité (carte)
 * - detailed: extended + panneau hover (formule, tableau par niveau)
 *
 * @props {Object} def - Définition (key, name, short_name, icon, color, unit, descriptions)
 * @props {string|number} value - Valeur affichée
 * @props {string} [formulaResolved] - Formule avec variables remplacées
 * @props {string} [formulaRaw] - Formule brute (tooltip)
 * @props {Array<{level, value}>} [levelTable] - Tableau niveau → valeur
 * @props {string} [unit] - Unité (affichée après la valeur)
 * @props {string} [displayMode] - 'minimal'|'compact'|'extended'|'detailed' (PROPERTY_DISPLAY_MODES)
 * @props {boolean} [compact] - Legacy: si true, équivaut à displayMode='minimal'
 */
import { computed, ref } from "vue";
import { PROPERTY_DISPLAY_MODES } from "@/Utils/Entity/Constants";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import {
    getCharacteristicColorStyle,
    getCharacteristicContainerStyle,
} from "@/Composables/entity/useCharacteristicDisplay";

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

const isHovered = ref(false);

/** Mode effectif : displayMode prioritaire, sinon compact → minimal */
const effectiveDisplayMode = computed(() => {
    if (props.displayMode) return props.displayMode;
    return props.compact ? PROPERTY_DISPLAY_MODES.minimal : PROPERTY_DISPLAY_MODES.extended;
});

const shortLabel = computed(() => props.def?.short_name || props.def?.name || props.def?.key || "—");
const fullLabel = computed(() => props.def?.name || props.def?.short_name || props.def?.key || "—");
const description = computed(() => props.def?.descriptions || props.def?.helper || "");
const unitStr = computed(() => props.unit || props.def?.unit || "");
const valueOnly = computed(() => {
    const v = props.value;
    if (v === null || v === undefined || v === "") return "—";
    return String(v);
});
const displayValue = computed(() => {
    const v = valueOnly.value;
    if (v === "—") return v;
    return unitStr.value ? `${v} ${unitStr.value}` : v;
});
const hasFormula = computed(() => !!props.formulaResolved || !!props.formulaRaw);
const hasLevelTable = computed(() => Array.isArray(props.levelTable) && props.levelTable.length > 0);

/** Panneau hover uniquement en mode detailed et si contenu (formule ou levelTable) */
const showHoverPanel = computed(
    () =>
        effectiveDisplayMode.value === PROPERTY_DISPLAY_MODES.detailed &&
        (hasFormula.value || hasLevelTable.value)
);

const isMinimalOrCompact = computed(
    () =>
        effectiveDisplayMode.value === PROPERTY_DISPLAY_MODES.minimal ||
        effectiveDisplayMode.value === PROPERTY_DISPLAY_MODES.compact
);

const valueStyle = computed(() => getCharacteristicColorStyle(props.def?.color) ?? {});
const containerStyle = computed(() =>
    isMinimalOrCompact.value ? {} : getCharacteristicContainerStyle(props.def?.color),
);
</script>

<template>
    <div
        class="characteristic-formula relative inline-block min-w-0 transition-shadow"
        :class="isMinimalOrCompact ? 'rounded px-1 py-0.5' : 'rounded-box border border-base-300 px-2.5 py-2 backdrop-blur-sm'"
        :style="containerStyle"
        @mouseenter="isHovered = true"
        @mouseleave="isHovered = false"
    >
        <!-- Mode minimal : icône + valeur + unité -->
        <div v-if="effectiveDisplayMode === PROPERTY_DISPLAY_MODES.minimal" class="flex items-center gap-1">
            <Icon
                v-if="def?.icon"
                :source="def.icon"
                :alt="fullLabel"
                size="xs"
                class="shrink-0 opacity-90"
                :style="valueStyle"
            />
            <span class="truncate text-sm font-medium" :style="valueStyle">{{ displayValue }}</span>
        </div>

        <!-- Mode compact : icône + label abrégé + valeur + unité -->
        <div v-else-if="effectiveDisplayMode === PROPERTY_DISPLAY_MODES.compact" class="flex items-center gap-1">
            <Icon
                v-if="def?.icon"
                :source="def.icon"
                :alt="fullLabel"
                size="xs"
                class="shrink-0 opacity-90"
                :style="valueStyle"
            />
            <span class="text-xs opacity-80 truncate">{{ shortLabel }}:</span>
            <span class="truncate text-sm font-medium" :style="valueStyle">{{ displayValue }}</span>
        </div>

        <!-- Mode extended/detailed : carte avec label complet -->
        <template v-else>
            <div class="flex items-center justify-between gap-2">
                <span class="min-w-0 truncate text-sm font-medium" :style="valueStyle">{{ displayValue }}</span>
                <Icon
                    v-if="def?.icon"
                    :source="def.icon"
                    :alt="fullLabel"
                    size="xs"
                    class="shrink-0 opacity-80"
                    :style="valueStyle"
                />
            </div>
            <p class="mt-0.5 text-xs opacity-80">{{ fullLabel }}</p>
        </template>

        <!-- Panneau étendu (hover) — uniquement en mode detailed -->
        <div
            v-show="showHoverPanel && isHovered"
            class="characteristic-formula-expanded absolute left-0 top-full z-50 mt-1 min-w-[180px] max-w-[320px] rounded-box border border-base-300 bg-base-100 p-3 shadow-xl"
        >
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <Tooltip v-if="description" :content="description" placement="bottom">
                        <span class="text-sm font-semibold">{{ fullLabel }}</span>
                    </Tooltip>
                    <span v-else class="text-sm font-semibold">{{ fullLabel }}</span>
                    <Tooltip v-if="def?.icon && description" :content="description" placement="bottom">
                        <Icon :source="def.icon" :alt="def.name || def.key" size="sm" />
                    </Tooltip>
                    <Icon v-else-if="def?.icon" :source="def.icon" :alt="def.name || def.key" size="sm" />
                </div>
                <div class="text-lg font-medium" :style="valueStyle">{{ displayValue }}</div>
                <div v-if="hasFormula" class="text-xs opacity-80">
                    <Tooltip v-if="formulaRaw" :content="formulaRaw" placement="bottom">
                        <span class="cursor-help border-b border-dotted border-base-content/30">
                            {{ formulaResolved || formulaRaw }}
                        </span>
                    </Tooltip>
                    <span v-else>{{ formulaResolved }}</span>
                </div>
                <div v-if="hasLevelTable" class="mt-2 overflow-x-auto">
                    <table class="table table-xs">
                        <thead>
                            <tr>
                                <th>Niveau</th>
                                <th>Valeur</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="row in levelTable" :key="row.level">
                                <td>{{ row.level }}</td>
                                <td>{{ row.value }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.characteristic-formula-expanded {
    pointer-events: none;
}
</style>
