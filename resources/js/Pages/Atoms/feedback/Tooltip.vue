<script setup>
defineOptions({ inheritAttrs: false });
import { computed, useSlots } from "vue";
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from "@/Utils/atomic-design/uiHelper";
import { colorList } from "@/Pages/Atoms/atomMap";
import OverlayTrigger from "@/Pages/Molecules/overlay/OverlayTrigger.vue";

const props = defineProps({
    ...getCommonProps({ exclude: ["tooltip", "tooltip_placement"] }),
    ...getCustomUtilityProps(),
    content: {
        type: String,
        default: "",
    },
    placement: {
        type: String,
        default: "top",
        validator: (v) => ["top", "right", "bottom", "left", "end", "start"].includes(v),
    },
    color: {
        type: String,
        default: "",
        validator: (v) => colorList.includes(v),
    },
    open: {
        type: Boolean,
        default: false,
    },
    glass: {
        type: Boolean,
        default: true,
    },
    responsive: {
        type: String,
        default: "",
    },
    accentClass: {
        type: String,
        default: "",
    },
    accentStyle: {
        type: Object,
        default: () => ({}),
    },
});

const slots = useSlots();

const hasTooltip = computed(() => {
    const c = typeof props.content === "string" ? props.content.trim() : "";
    if (c !== "") {
        return true;
    }
    const nodes = slots.content?.();
    return Array.isArray(nodes) && nodes.length > 0;
});

/** Accent sémantique (variable `--color` pour bordure / ombre, voir `_tooltip.scss`). */
const surfaceColorClass = computed(() => {
    switch (props.color) {
        case "neutral":
            return "color-neutral";
        case "primary":
            return "color-primary";
        case "secondary":
            return "color-secondary";
        case "accent":
            return "color-accent";
        case "info":
            return "color-info";
        case "success":
            return "color-success";
        case "warning":
            return "color-warning";
        case "error":
            return "color-error";
        default:
            return "color-neutral";
    }
});

/** `accentClass` (ex. thème caracs) remplace la couleur Daisy pour l’ombre / bordure. */
const tooltipAccentColorClass = computed(() => {
    const a = props.accentClass != null ? String(props.accentClass).trim() : "";
    return a !== "" ? a : surfaceColorClass.value;
});

const floatingPanelClasses = computed(() => {
    const base = "pointer-events-auto";
    if (!props.glass) {
        return mergeClasses(base, "tooltip-floating-chromeless");
    }
    return mergeClasses(base, "tooltip-floating-surface", tooltipAccentColorClass.value);
});

/** Trigger : pas de classes `tooltip-*` Daisy (évite le pseudo ::before clippé). */
const triggerClasses = computed(() =>
    mergeClasses("inline-flex max-w-full min-w-0", getCustomUtilityClasses(props), props.class),
);

const attrs = computed(() => getCommonAttrs(props));
const overlayContent = computed(() =>
    slots.content
        ? { component: { render: () => slots.content() } }
        : String(props.content || "")
);
</script>

<template>
    <OverlayTrigger
        v-if="hasTooltip"
        :content="overlayContent"
        trigger="hover"
        :placement="placement"
        :interactive="false"
        :close-on-outside="false"
        :close-on-escape="true"
        :panel-class="floatingPanelClasses"
    >
        <span :class="triggerClasses" v-bind="attrs">
            <slot />
        </span>
    </OverlayTrigger>
    <div v-else :class="triggerClasses" v-bind="attrs">
        <slot />
    </div>
</template>

