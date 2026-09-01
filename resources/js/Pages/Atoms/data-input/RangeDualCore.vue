<script setup>
/**
 * RangeDualCore Atom
 *
 * @description
 * Slider à deux curseurs (min et max) sur une seule barre.
 * Les valeurs s’affichent au-dessus de chaque curseur.
 *
 * @example
 * <RangeDualCore :min="1" :max="20" :model-value="{ min: 3, max: 12 }" accent="var(--color-blue-400)" />
 */
import { computed, ref } from "vue";
import { rangeValuePercent } from "@/Utils/table/tableRangeFilter.js";

const props = defineProps({
    min: { type: Number, default: 0 },
    max: { type: Number, default: 100 },
    step: { type: Number, default: 1 },
    modelValue: { type: Object, default: () => ({ min: 0, max: 100 }) },
    color: { type: String, default: "primary" },
    /**
     * Couleur CSS (hex ou `var(--color-blue-400)`) qui surcharge `--color`.
     */
    accent: { type: String, default: "" },
    disabled: { type: Boolean, default: false },
    ariaLabelMin: { type: String, default: "Minimum" },
    ariaLabelMax: { type: String, default: "Maximum" },
});

const emit = defineEmits(["update:modelValue"]);

const trackRef = ref(null);
const dragging = ref(null);

const boundsMin = computed(() => Number(props.min));
const boundsMax = computed(() => Number(props.max));
const currentMin = computed(() => {
    const n = Number(props.modelValue?.min);
    return Number.isFinite(n) ? n : boundsMin.value;
});
const currentMax = computed(() => {
    const n = Number(props.modelValue?.max);
    return Number.isFinite(n) ? n : boundsMax.value;
});

const minPct = computed(() => rangeValuePercent(currentMin.value, boundsMin.value, boundsMax.value));
const maxPct = computed(() => rangeValuePercent(currentMax.value, boundsMin.value, boundsMax.value));

const colorClass = computed(() => {
    switch (String(props.color || "primary")) {
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
        case "neutral":
            return "color-neutral";
        default:
            return "color-primary";
    }
});

const accentStyle = computed(() => {
    const a = String(props.accent || "").trim();
    return a ? { "--color": a } : {};
});

const fillStyle = computed(() => ({
    left: `${minPct.value}%`,
    width: `${Math.max(0, maxPct.value - minPct.value)}%`,
    background: "var(--color)",
}));

const labelStyle = (pct, otherPct, side) => {
    let tx = "-50%";
    if (pct <= 8) tx = "0";
    else if (pct >= 92) tx = "-100%";
    if (Math.abs(pct - otherPct) < 12) {
        tx = side === "min" ? "-85%" : "-15%";
    }
    return {
        left: `${pct}%`,
        transform: `translateX(${tx})`,
    };
};

const minLabelStyle = computed(() => labelStyle(minPct.value, maxPct.value, "min"));
const maxLabelStyle = computed(() => labelStyle(maxPct.value, minPct.value, "max"));

const snap = (raw) => {
    const step = Number(props.step) || 1;
    const snapped = Math.round(raw / step) * step;
    return Math.min(boundsMax.value, Math.max(boundsMin.value, snapped));
};

const emitRange = (min, max, thumb) => {
    let nextMin = snap(min);
    let nextMax = snap(max);
    if (nextMin > nextMax) {
        if (thumb === "min") nextMax = nextMin;
        else nextMin = nextMax;
    }
    emit("update:modelValue", { min: nextMin, max: nextMax });
};

const valueFromClientX = (clientX) => {
    const el = trackRef.value;
    if (!el) return boundsMin.value;
    const rect = el.getBoundingClientRect();
    if (rect.width <= 0) return boundsMin.value;
    const pct = Math.min(1, Math.max(0, (clientX - rect.left) / rect.width));
    return boundsMin.value + pct * (boundsMax.value - boundsMin.value);
};

const nearestThumb = (clientX) => {
    const value = valueFromClientX(clientX);
    return Math.abs(value - currentMin.value) <= Math.abs(value - currentMax.value) ? "min" : "max";
};

const applyPointer = (clientX, thumb) => {
    const value = valueFromClientX(clientX);
    if (thumb === "min") emitRange(value, currentMax.value, "min");
    else emitRange(currentMin.value, value, "max");
};

const onPointerDown = (event) => {
    if (props.disabled) return;
    const thumb = nearestThumb(event.clientX);
    dragging.value = thumb;
    event.currentTarget?.setPointerCapture?.(event.pointerId);
    applyPointer(event.clientX, thumb);
};

const onPointerMove = (event) => {
    if (!dragging.value || props.disabled) return;
    applyPointer(event.clientX, dragging.value);
};

const onPointerUp = () => {
    dragging.value = null;
};

const onMinKeyInput = (event) => emitRange(event.target.value, currentMax.value, "min");
const onMaxKeyInput = (event) => emitRange(currentMin.value, event.target.value, "max");
</script>

<template>
    <div class="relative w-full pt-3.5 select-none" :class="colorClass" :style="accentStyle">
        <span
            class="absolute top-0 text-[10px] leading-none tabular-nums pointer-events-none"
            :style="{ ...minLabelStyle, color: 'var(--color)' }"
        >
            {{ currentMin }}
        </span>
        <span
            class="absolute top-0 text-[10px] leading-none tabular-nums pointer-events-none"
            :style="{ ...maxLabelStyle, color: 'var(--color)' }"
        >
            {{ currentMax }}
        </span>

        <div
            ref="trackRef"
            class="relative h-4 touch-none"
            :class="{ 'cursor-pointer': !disabled, 'opacity-60 cursor-not-allowed': disabled }"
            @pointerdown="onPointerDown"
            @pointermove="onPointerMove"
            @pointerup="onPointerUp"
            @pointercancel="onPointerUp"
        >
            <div class="absolute top-1/2 inset-x-0 h-1 -translate-y-1/2 rounded-full bg-base-content/15" />
            <div
                class="absolute top-1/2 h-1 -translate-y-1/2 rounded-full pointer-events-none"
                :style="fillStyle"
            />
            <span
                class="range-dual-thumb"
                :class="{ 'z-20': dragging === 'min' }"
                :style="{ left: `${minPct}%` }"
            />
            <span
                class="range-dual-thumb"
                :class="{ 'z-20': dragging === 'max' }"
                :style="{ left: `${maxPct}%` }"
            />
        </div>

        <input
            type="range"
            class="sr-only"
            :min="boundsMin"
            :max="boundsMax"
            :step="step"
            :value="currentMin"
            :disabled="disabled"
            :aria-label="ariaLabelMin"
            @input="onMinKeyInput"
        >
        <input
            type="range"
            class="sr-only"
            :min="boundsMin"
            :max="boundsMax"
            :step="step"
            :value="currentMax"
            :disabled="disabled"
            :aria-label="ariaLabelMax"
            @input="onMaxKeyInput"
        >
    </div>
</template>

<style scoped lang="scss">
.color-primary { --color: var(--color-primary-500); }
.color-secondary { --color: var(--color-secondary-500); }
.color-accent { --color: var(--color-accent-500); }
.color-info { --color: var(--color-info-500); }
.color-success { --color: var(--color-success-500); }
.color-warning { --color: var(--color-warning-500); }
.color-error { --color: var(--color-error-500); }
.color-neutral { --color: var(--color-neutral-500); }

.range-dual-thumb {
    position: absolute;
    top: 50%;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: var(--color);
    border: 2px solid white;
    box-shadow: 0 1px 3px color-mix(in srgb, var(--color) 35%, transparent);
    transform: translate(-50%, -50%);
    pointer-events: none;
}
</style>
