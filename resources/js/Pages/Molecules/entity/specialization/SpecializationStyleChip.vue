<script setup>
/**
 * Marqueur de variante / style de spécialisation.
 * Distinct des badges compétences : fond clair, police plus grande, couleur sur icône + nom.
 */
import { computed } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import {
    resolveSpecializationStyle,
    specializationStyleSurfaceStyle,
} from "@/Utils/entity/specializationStyles";

const props = defineProps({
    styleKey: { type: String, required: true },
    label: { type: String, default: "" },
    specializationSlug: { type: String, default: "artiste" },
    showLabel: { type: Boolean, default: true },
    /**
     * `marker` = titre de colonne / inline lisible ; `badge` = pastille compacte (legacy).
     * @type {'marker'|'badge'}
     */
    appearance: {
        type: String,
        default: "marker",
        validator: (v) => ["marker", "badge"].includes(v),
    },
    size: {
        type: String,
        default: "md",
        validator: (v) => ["xs", "sm", "md"].includes(v),
    },
});

const token = computed(() =>
    resolveSpecializationStyle(props.styleKey, props.specializationSlug),
);

const surfaceStyle = computed(() =>
    specializationStyleSurfaceStyle(token.value, { appearance: props.appearance }),
);

const displayLabel = computed(
    () =>
        String(props.label || token.value?.name || props.styleKey || "").trim() ||
        "Style",
);

const iconSource = computed(() => token.value?.icon || "fa-solid fa-masks-theater");

const sizeClass = computed(() => {
    if (props.appearance === "badge") {
        return props.size === "xs" ? "text-[10px]" : "text-xs";
    }
    if (props.size === "xs") return "text-xs";
    if (props.size === "sm") return "text-sm";
    return "text-base";
});

const iconSize = computed(() => {
    if (props.appearance === "badge" || props.size === "xs") return "xs";
    if (props.size === "sm") return "sm";
    return "sm";
});
</script>

<template>
    <span
        class="spe-style-chip inline-flex max-w-full min-w-0 items-center font-semibold tracking-wide"
        :class="[
            sizeClass,
            appearance === 'marker'
                ? 'spe-style-chip--marker gap-2 rounded-md px-2.5 py-1'
                : 'spe-style-chip--badge gap-1.5 rounded-sm border px-1.5 py-0.5 shadow-sm',
        ]"
        :style="surfaceStyle"
        :title="displayLabel"
    >
        <Icon
            :source="iconSource"
            :alt="displayLabel"
            :size="iconSize"
            class="spe-style-chip__icon shrink-0"
        />
        <span v-if="showLabel" class="spe-style-chip__label min-w-0 truncate">{{ displayLabel }}</span>
    </span>
</template>

<style scoped>
.spe-style-chip--marker {
    border: 1px solid color-mix(in oklch, white 18%, transparent);
    backdrop-filter: blur(4px);
    box-shadow: none;
    line-height: 1.25;
}
.spe-style-chip--marker .spe-style-chip__label {
    font-weight: 700;
    letter-spacing: 0.01em;
}
.spe-style-chip__icon {
    opacity: 1;
    color: inherit;
}
</style>
