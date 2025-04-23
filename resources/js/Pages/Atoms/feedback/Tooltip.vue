<script setup>
import { computed, ref, onMounted, onUnmounted } from "vue";
import { extractTheme, combinePropsWithTheme } from "@/Utils/extractTheme";
import { commonProps, generateClasses } from "@/Utils/commonProps";

const props = defineProps({
    ...commonProps,
    content: {
        type: [String, Object],
        default: "",
    },
    position: {
        type: String,
        default: "top",
        validator(value) {
            return ["top", "right", "bottom", "left"].includes(value);
        },
    },
    delay: {
        type: Number,
        default: 0,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const tooltipRef = ref(null);
const isVisible = ref(false);
let timeout = null;

const buildTooltipClasses = (props) => {
    const classes = ["tooltip"];

    // Ajout des classes communes
    const baseClasses = generateClasses(props);
    if (baseClasses) {
        classes.push(baseClasses);
    }

    // Position
    classes.push(`tooltip-${props.position}`);

    return classes.join(" ");
};

const themeProps = computed(() => extractTheme(props.theme));
const combinedProps = computed(() => combinePropsWithTheme(props, themeProps.value));
const getClasses = computed(() => buildTooltipClasses(combinedProps.value));

const showTooltip = () => {
    if (props.disabled) return;

    if (timeout) {
        clearTimeout(timeout);
    }

    timeout = setTimeout(() => {
        isVisible.value = true;
    }, props.delay);
};

const hideTooltip = () => {
    if (timeout) {
        clearTimeout(timeout);
    }
    isVisible.value = false;
};

const updatePosition = () => {
    if (!tooltipRef.value || !isVisible.value) return;

    const tooltip = tooltipRef.value;
    const trigger = tooltip.parentElement;
    const triggerRect = trigger.getBoundingClientRect();
    const tooltipRect = tooltip.getBoundingClientRect();

    let top = 0;
    let left = 0;

    switch (props.position) {
        case "top":
            top = triggerRect.top - tooltipRect.height - 8;
            left = triggerRect.left + (triggerRect.width - tooltipRect.width) / 2;
            break;
        case "right":
            top = triggerRect.top + (triggerRect.height - tooltipRect.height) / 2;
            left = triggerRect.right + 8;
            break;
        case "bottom":
            top = triggerRect.bottom + 8;
            left = triggerRect.left + (triggerRect.width - tooltipRect.width) / 2;
            break;
        case "left":
            top = triggerRect.top + (triggerRect.height - tooltipRect.height) / 2;
            left = triggerRect.left - tooltipRect.width - 8;
            break;
    }

    tooltip.style.top = `${top}px`;
    tooltip.style.left = `${left}px`;
};

onMounted(() => {
    window.addEventListener("scroll", updatePosition);
    window.addEventListener("resize", updatePosition);
});

onUnmounted(() => {
    window.removeEventListener("scroll", updatePosition);
    window.removeEventListener("resize", updatePosition);
    if (timeout) {
        clearTimeout(timeout);
    }
});
</script>

<template>
    <div
        class="tooltip-wrapper"
        @mouseenter="showTooltip"
        @mouseleave="hideTooltip"
        @focus="showTooltip"
        @blur="hideTooltip"
    >
        <slot />
        <div
            v-if="isVisible"
            ref="tooltipRef"
            :class="getClasses"
            role="tooltip"
        >
            <div class="tooltip-content">
                <slot v-if="typeof content === 'object'" name="content">
                    <slot name="tooltip" />
                </slot>
                <span v-else>{{ content }}</span>
            </div>
            <div class="tooltip-arrow" />
        </div>
    </div>
</template>

<style scoped>
.tooltip-wrapper {
    position: relative;
    display: inline-block;
}

.tooltip {
    position: fixed;
    z-index: 1000;
    padding: 0.5rem;
    background-color: var(--color-base-900);
    color: var(--color-base-100);
    border-radius: 0.25rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    font-size: 0.875rem;
    line-height: 1.25;
    max-width: 16rem;
    pointer-events: none;
}

.tooltip-content {
    position: relative;
    z-index: 1;
}

.tooltip-arrow {
    position: absolute;
    width: 0.5rem;
    height: 0.5rem;
    background-color: var(--color-base-900);
    transform: rotate(45deg);
}

.tooltip-top .tooltip-arrow {
    bottom: -0.25rem;
    left: 50%;
    transform: translateX(-50%) rotate(45deg);
}

.tooltip-right .tooltip-arrow {
    left: -0.25rem;
    top: 50%;
    transform: translateY(-50%) rotate(45deg);
}

.tooltip-bottom .tooltip-arrow {
    top: -0.25rem;
    left: 50%;
    transform: translateX(-50%) rotate(45deg);
}

.tooltip-left .tooltip-arrow {
    right: -0.25rem;
    top: 50%;
    transform: translateY(-50%) rotate(45deg);
}
</style>
