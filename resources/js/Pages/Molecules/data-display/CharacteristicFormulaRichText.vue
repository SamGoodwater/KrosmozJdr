<script setup>
/**
 * Formule de caractéristique : expression (puces) ou tableau de paliers lisible.
 *
 * @props {string} formula - Expression ou JSON `{"characteristic":"level","1":"0","3":"1"}`.
 * @example
 * <CharacteristicFormulaRichText formula='{"characteristic":"level","1":"0","3":"1"}' />
 */
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import {
    getCharacteristicColorStyle,
    resolveDef,
} from "@/Composables/entity/useCharacteristicDisplay";
import { parseCharacteristicFormulaRichText } from "@/Composables/characteristic/useCharacteristicFormulaRichText";
import { buildFormulaTableView } from "@/Utils/characteristic/formulaConfig";

defineOptions({ name: "CharacteristicFormulaRichText" });

const props = defineProps({
    formula: { type: String, default: "" },
    sourceGroups: { type: Array, default: () => [] },
    /** `descriptions_first` : tooltip = description (+ helper si différent). `helper_first` : ordre inverse. */
    tooltipOrder: {
        type: String,
        default: "descriptions_first",
        validator: (v) => v === "descriptions_first" || v === "helper_first",
    },
});

const tableView = computed(() => buildFormulaTableView(props.formula));

const referenceGroups = computed(() =>
    props.sourceGroups?.length
        ? props.sourceGroups
        : ["creature", "item", "resource", "spell", "capability"],
);

const referenceDef = computed(() => {
    const key = tableView.value?.characteristic;
    if (!key) return null;
    return resolveDef(key, undefined, { sourceGroups: referenceGroups.value });
});

const referenceLabel = computed(() => {
    const def = referenceDef.value;
    const short = def?.short_name || def?.shortName;
    if (short && String(short).trim()) return String(short).trim();
    if (def?.name && String(def.name).trim()) return String(def.name).trim();
    return tableView.value?.characteristic || "";
});

const referenceIcon = computed(
    () => referenceDef.value?._resolvedIcon || referenceDef.value?.icon || "",
);

const referenceStyle = computed(
    () => getCharacteristicColorStyle(referenceDef.value?._resolvedColor || referenceDef.value?.color) || {},
);

const segments = computed(() => {
    if (tableView.value) return [];
    return parseCharacteristicFormulaRichText(props.formula, {
        sourceGroups: props.sourceGroups,
        tooltipOrder: props.tooltipOrder,
    });
});

function segmentStyle(segment) {
    return getCharacteristicColorStyle(segment?.color) || {};
}

/**
 * Une valeur de palier qui n’est pas un simple nombre reste une formule (puces).
 *
 * @param {number|string} value
 * @returns {boolean}
 */
function valueIsExpression(value) {
    const text = String(value ?? "").trim();
    if (text === "") return false;
    return !/^-?\d+(?:[.,]\d+)?$/.test(text);
}
</script>

<template>
    <div v-if="tableView" class="formula-table w-full min-w-44 whitespace-normal text-inherit">
        <p class="mb-1 flex flex-wrap items-center gap-1 text-xs opacity-80">
            <span>Selon</span>
            <span class="inline-flex items-center gap-1 font-semibold" :style="referenceStyle">
                <Icon
                    v-if="referenceIcon"
                    :source="referenceIcon"
                    :alt="referenceLabel"
                    size="xs"
                    :style="referenceStyle"
                />
                {{ referenceLabel }}
            </span>
        </p>
        <table class="w-full border-collapse text-xs">
            <thead>
                <tr class="opacity-70">
                    <th class="pb-1 pr-3 text-left font-medium">{{ referenceLabel }}</th>
                    <th class="pb-1 text-right font-medium">Valeur</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="(row, index) in tableView.rows"
                    :key="`${row.range}-${index}`"
                    class="border-t border-current/15"
                >
                    <td class="py-0.5 pr-3 tabular-nums">{{ row.range }}</td>
                    <td class="py-0.5 text-right font-semibold tabular-nums">
                        <CharacteristicFormulaRichText
                            v-if="valueIsExpression(row.value)"
                            :formula="String(row.value)"
                            :source-groups="sourceGroups"
                            :tooltip-order="tooltipOrder"
                        />
                        <template v-else>{{ row.value }}</template>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <span v-else class="inline-flex flex-wrap items-center gap-x-1 gap-y-0.5">
        <template v-for="(segment, idx) in segments" :key="`seg-${idx}`">
            <span v-if="segment.type === 'text'" class="whitespace-pre-wrap text-base-content/85">
                {{ segment.text }}
            </span>
            <Tooltip
                v-else
                :content="segment.tooltip"
                placement="top"
            >
                <span class="inline-flex items-center gap-1 rounded px-1 py-0.5 bg-base-200/40">
                    <Icon
                        v-if="segment.icon"
                        :source="segment.icon"
                        :alt="segment.label"
                        size="xs"
                        :style="segmentStyle(segment)"
                    />
                    <span class="font-semibold" :style="segmentStyle(segment)">
                        {{ segment.label }}
                    </span>
                </span>
            </Tooltip>
        </template>
    </span>
</template>

