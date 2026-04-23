<script setup>
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import { getCharacteristicColorStyle } from "@/Composables/entity/useCharacteristicDisplay";
import { parseCharacteristicFormulaRichText } from "@/Composables/characteristic/useCharacteristicFormulaRichText";

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

const segments = computed(() =>
    parseCharacteristicFormulaRichText(props.formula, {
        sourceGroups: props.sourceGroups,
        tooltipOrder: props.tooltipOrder,
    }),
);

function segmentStyle(segment) {
    return getCharacteristicColorStyle(segment?.color) || {};
}
</script>

<template>
    <span class="inline-flex flex-wrap items-center gap-x-1 gap-y-0.5">
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

