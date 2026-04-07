<script setup>
// Tooltip transparent - ne capture pas les événements natifs
defineOptions({ inheritAttrs: false });

/**
 * Tooltip Atom (DaisyUI + Floating UI)
 *
 * @description
 * Info-bulle : trigger en slot par défaut, contenu string (`content`) ou slot `#content`.
 * Le positionnement utilise **Floating UI** (`strategy: fixed`) et **Teleport vers `body`**
 * pour rester visible dans les zones scroll / `overflow-hidden` (ex. TanStackTable).
 * Plusieurs tooltips : **z-index incrémental** (`allocateTooltipZIndex`) — le dernier ouvert passe au-dessus.
 * Style : surface globale `tooltip-floating-surface` (SCSS) + utilitaire `color-*` pour l’accent.
 *
 * @see https://daisyui.com/components/tooltip/
 * @see https://floating-ui.com/docs/vue
 *
 * @props {String} content - Texte simple (si pas de slot #content)
 * @props {String} placement - top | right | bottom | left | end | start
 * @props {String} color - neutral | primary | … (atomMap)
 * @props {Boolean} open - Force l’ouverture
 * @props {String} responsive - sm | md | lg | xl | 2xl : tooltip actif seulement à partir du breakpoint (matchMedia)
 * @slot default - Trigger
 * @slot content - Contenu riche
 */
import { computed, nextTick, onUnmounted, ref, useSlots, watch } from "vue";
import { useFloating, offset, flip, shift, autoUpdate } from "@floating-ui/vue";
import {
    getCommonProps,
    getCommonAttrs,
    getCustomUtilityProps,
    getCustomUtilityClasses,
    mergeClasses,
} from "@/Utils/atomic-design/uiHelper";
import { colorList } from "@/Pages/Atoms/atomMap";
import { allocateTooltipZIndex } from "@/Composables/ui/allocateTooltipZIndex";

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
});

const slots = useSlots();

const hasTooltip = computed(() => {
    if (slots.content?.()?.length) {
        return true;
    }
    const c = props.content;
    return typeof c === "string" && c.trim() !== "";
});

const triggerRef = ref(null);
const floatingRef = ref(null);
const internalOpen = ref(false);

/** Breakpoint Daisy → min-width (px) pour `responsive` */
const RESPONSIVE_MIN_PX = Object.freeze({
    sm: 640,
    md: 768,
    lg: 1024,
    xl: 1280,
    "2xl": 1536,
});

const responsiveAllow = ref(true);
let removeMediaListener = null;

function teardownMediaQuery() {
    if (typeof removeMediaListener === "function") {
        removeMediaListener();
        removeMediaListener = null;
    }
}

watch(
    () => props.responsive,
    (r) => {
        teardownMediaQuery();
        if (!r || typeof window === "undefined") {
            responsiveAllow.value = true;
            return;
        }
        const minW = RESPONSIVE_MIN_PX[r];
        if (minW == null) {
            responsiveAllow.value = true;
            return;
        }
        const mq = window.matchMedia(`(min-width: ${minW}px)`);
        const sync = () => {
            responsiveAllow.value = mq.matches;
        };
        sync();
        mq.addEventListener("change", sync);
        removeMediaListener = () => mq.removeEventListener("change", sync);
    },
    { immediate: true },
);

onUnmounted(() => {
    teardownMediaQuery();
    clearTimers();
});

const isOpen = computed(() => {
    if (props.disabled) {
        return false;
    }
    if (props.responsive && !responsiveAllow.value) {
        return false;
    }
    if (props.open) {
        return true;
    }
    return internalOpen.value;
});

const floatingPlacement = computed(() => {
    const p = props.placement;
    if (p === "end") {
        return "right";
    }
    if (p === "start") {
        return "left";
    }
    return p;
});

const { floatingStyles } = useFloating(triggerRef, floatingRef, {
    open: isOpen,
    placement: floatingPlacement,
    strategy: "fixed",
    middleware: [offset(8), flip(), shift({ padding: 8 })],
    whileElementsMounted: autoUpdate,
});

/** Dernier tooltip ouvert au-dessus des autres (pile globale). */
const stackZIndex = ref(1100);
watch(
    () => isOpen.value,
    (open, wasOpen) => {
        if (open && wasOpen !== true) {
            stackZIndex.value = allocateTooltipZIndex();
        }
    },
    { immediate: true },
);

const floatingStylesWithZ = computed(() => ({
    ...floatingStyles.value,
    zIndex: stackZIndex.value,
}));

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

const floatingPanelClasses = computed(() => {
    const base = "pointer-events-auto";
    if (!props.glass) {
        return mergeClasses(base, "tooltip-floating-chromeless");
    }
    return mergeClasses(base, "tooltip-floating-surface", surfaceColorClass.value);
});

/** Trigger : pas de classes `tooltip-*` Daisy (évite le pseudo ::before clippé). */
const triggerClasses = computed(() =>
    mergeClasses("inline-flex max-w-full min-w-0", getCustomUtilityClasses(props), props.class),
);

const attrs = computed(() => getCommonAttrs(props));

let closeTimer = null;

function clearTimers() {
    if (closeTimer != null) {
        clearTimeout(closeTimer);
        closeTimer = null;
    }
}

function onTriggerEnter() {
    if (props.disabled || props.open) {
        return;
    }
    if (props.responsive && !responsiveAllow.value) {
        return;
    }
    clearTimers();
    internalOpen.value = true;
}

function onTriggerLeave() {
    if (props.open) {
        return;
    }
    clearTimers();
    closeTimer = window.setTimeout(() => {
        closeTimer = null;
        internalOpen.value = false;
    }, 100);
}

function onFloatingEnter() {
    clearTimers();
}

function onFloatingLeave() {
    if (props.open) {
        return;
    }
    clearTimers();
    closeTimer = window.setTimeout(() => {
        closeTimer = null;
        internalOpen.value = false;
    }, 100);
}

function onTriggerFocusIn() {
    if (props.disabled || props.open) {
        return;
    }
    if (props.responsive && !responsiveAllow.value) {
        return;
    }
    clearTimers();
    internalOpen.value = true;
}

function onTriggerFocusOut(ev) {
    if (props.open) {
        return;
    }
    nextTick(() => {
        const panel = floatingRef.value;
        const next = ev.relatedTarget;
        if (panel && next instanceof Node && panel.contains(next)) {
            return;
        }
        internalOpen.value = false;
    });
}
</script>

<template>
    <div
        v-if="hasTooltip"
        ref="triggerRef"
        :class="triggerClasses"
        v-bind="attrs"
        @mouseenter="onTriggerEnter"
        @mouseleave="onTriggerLeave"
        @focusin="onTriggerFocusIn"
        @focusout="onTriggerFocusOut"
    >
        <slot />
        <Teleport to="body">
            <div
                v-if="isOpen"
                ref="floatingRef"
                :class="floatingPanelClasses"
                role="tooltip"
                :style="floatingStylesWithZ"
                @mouseenter="onFloatingEnter"
                @mouseleave="onFloatingLeave"
            >
                <slot v-if="$slots.content" name="content" />
                <template v-else>{{ content }}</template>
            </div>
        </Teleport>
    </div>
    <div v-else :class="triggerClasses" v-bind="attrs">
        <slot />
    </div>
</template>

