<script setup>
/**
 * CharacteristicFormula — Atome d'affichage d'une caractéristique à formule (valeur + unité).
 *
 * @description
 * Deux modes : compact (icône + valeur + unité, padding minimal) et normal (carte avec couleur :
 * fond teinté, ombre discrète, backdrop-blur ; icône à droite, valeur+unité, label en dessous).
 * Panneau étendu au hover (formule résolue, tableau par level).
 *
 * @props {Object} def - Définition (key, name, short_name, icon, color, unit, descriptions)
 * @props {string|number} value - Valeur affichée
 * @props {string} [formulaResolved] - Formule avec variables remplacées
 * @props {string} [formulaRaw] - Formule brute (tooltip)
 * @props {Array<{level, value}>} [levelTable] - Tableau niveau → valeur
 * @props {string} [unit] - Unité (affichée après la valeur)
 * @props {boolean} [compact] - Mode compact (icône + valeur uniquement, padding minimal)
 */
import { computed, ref } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";

const props = defineProps({
    def: { type: Object, required: true },
    value: { type: [String, Number], default: "" },
    formulaResolved: { type: String, default: "" },
    formulaRaw: { type: String, default: "" },
    levelTable: { type: Array, default: () => [] },
    unit: { type: String, default: "" },
    compact: { type: Boolean, default: false },
});

const isHovered = ref(false);

const label = computed(() => props.def?.short_name || props.def?.name || props.def?.key || "—");
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

function hexToRgba(hex, a) {
    if (!hex || typeof hex !== "string") return null;
    let h = hex.replace(/^#/, "");
    if (h.length === 3) h = h[0] + h[0] + h[1] + h[1] + h[2] + h[2];
    if (h.length !== 6) return null;
    const r = parseInt(h.slice(0, 2), 16);
    const g = parseInt(h.slice(2, 4), 16);
    const b = parseInt(h.slice(4, 6), 16);
    return `rgba(${r},${g},${b},${a})`;
}

const valueStyle = computed(() => (props.def?.color ? { color: props.def.color } : {}));
const containerStyle = computed(() => {
    const c = props.def?.color;
    if (!c) return {};
    const rgba = hexToRgba(c, 0.08);
    const shadowRgba = hexToRgba(c, 0.15);
    if (!rgba || !shadowRgba) return {};
    return {
        backgroundColor: rgba,
        boxShadow: `0 1px 3px ${shadowRgba}`,
        ...(hexToRgba(c, 0.2) ? { borderColor: hexToRgba(c, 0.2) } : {}),
    };
});
</script>

<template>
    <div
        class="characteristic-formula relative inline-block min-w-0 transition-shadow"
        :class="compact ? 'rounded px-1 py-0.5' : 'rounded-lg border border-base-300 px-2.5 py-2 backdrop-blur-sm'"
        :style="compact ? {} : containerStyle"
        @mouseenter="isHovered = true"
        @mouseleave="isHovered = false"
    >
        <!-- Mode compact : icône + valeur (+ unité) -->
        <div v-if="compact" class="flex items-center gap-1">
            <Icon
                v-if="def?.icon"
                :source="def.icon"
                :alt="label"
                size="xs"
                class="shrink-0 opacity-90"
                :style="valueStyle"
            />
            <span class="truncate text-sm font-medium" :style="valueStyle">{{ displayValue }}</span>
        </div>

        <!-- Mode normal : ligne valeur+unité avec icône à droite, label en dessous -->
        <template v-else>
            <div class="flex items-center justify-between gap-2">
                <span class="min-w-0 truncate text-sm font-medium" :style="valueStyle">{{ displayValue }}</span>
                <Icon
                    v-if="def?.icon"
                    :source="def.icon"
                    :alt="label"
                    size="xs"
                    class="shrink-0 opacity-80"
                    :style="valueStyle"
                />
            </div>
            <p class="mt-0.5 text-xs opacity-80">{{ label }}</p>
        </template>

        <!-- Panneau étendu (hover) -->
        <div
            v-show="isHovered"
            class="characteristic-formula-expanded absolute left-0 top-full z-50 mt-1 min-w-[180px] max-w-[320px] rounded-lg border border-base-300 bg-base-100 p-3 shadow-xl"
        >
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <Tooltip v-if="description" :content="description" placement="bottom">
                        <span class="text-sm font-semibold">{{ label }}</span>
                    </Tooltip>
                    <span v-else class="text-sm font-semibold">{{ label }}</span>
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
