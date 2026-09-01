<script setup>
/**
 * RangeDualCore Atom
 *
 * @description
 * Deux curseurs natifs superposés (min / max) pour un filtre de plage.
 * Inputs natifs : `RangeCore` n’applique pas les classes du parent (`inheritAttrs: false`),
 * ce qui rendait les pouces inactifs.
 *
 * @example
 * <RangeDualCore v-model="range" :min="1" :max="200" color="primary" accent="var(--color-blue-400)" />
 *
 * @props {Object} modelValue - `{ min: number, max: number }`
 * @props {Number} min - Borne basse du slider
 * @props {Number} max - Borne haute du slider
 * @props {Number} step - Pas
 * @props {String} accent - Couleur CSS qui surcharge `--range-color`
 */

import { computed } from "vue";
import { normalizeTableRangeValue } from "@/Utils/table/tableRangeFilter.js";

const RANGE_COLOR_CLASS = {
    primary: "color-primary",
    secondary: "color-secondary",
    accent: "color-accent",
    info: "color-info",
    success: "color-success",
    warning: "color-warning",
    error: "color-error",
    neutral: "color-neutral",
};

const props = defineProps({
    modelValue: { type: Object, default: null },
    min: { type: Number, default: 0 },
    max: { type: Number, default: 20 },
    step: { type: Number, default: 1 },
    color: { type: String, default: "primary" },
    /**
     * Couleur CSS (hex ou `var(--color-blue-400)`) qui surcharge `--range-color`.
     */
    accent: { type: String, default: "" },
    disabled: { type: Boolean, default: false },
});

const emit = defineEmits(["update:modelValue"]);

const bounds = computed(() => {
    const a = Number(props.min);
    const b = Number(props.max);
    const min = Number.isFinite(a) ? a : 0;
    const max = Number.isFinite(b) ? b : 20;

    return min <= max ? { min, max } : { min: max, max: min };
});

const current = computed(() => normalizeTableRangeValue(props.modelValue, bounds.value));

const span = computed(() => {
    const s = bounds.value.max - bounds.value.min;
    return s > 0 ? s : 1;
});

const fillStyle = computed(() => {
    const start = ((current.value.min - bounds.value.min) / span.value) * 100;
    const end = ((current.value.max - bounds.value.min) / span.value) * 100;
    return {
        left: `${Math.min(start, end)}%`,
        width: `${Math.abs(end - start)}%`,
    };
});

const minOnTop = computed(() => (current.value.min - bounds.value.min) / span.value > 0.5);

const colorClass = computed(
    () => RANGE_COLOR_CLASS[props.color] || RANGE_COLOR_CLASS.primary,
);

const accentStyle = computed(() => {
    const a = String(props.accent || "").trim();
    return a ? { "--range-color": a } : {};
});

const valueLabelStyle = computed(() => {
    const a = String(props.accent || "").trim();
    return a ? { color: a } : undefined;
});

const emitRange = (min, max) => {
    let a = Number(min);
    let b = Number(max);
    if (!Number.isFinite(a)) a = bounds.value.min;
    if (!Number.isFinite(b)) b = bounds.value.max;
    if (a > b) {
        const swap = a;
        a = b;
        b = swap;
    }
    const lo = bounds.value.min;
    const hi = bounds.value.max;
    a = Math.min(hi, Math.max(lo, a));
    b = Math.min(hi, Math.max(lo, b));
    emit("update:modelValue", { min: a, max: b });
};

const onMinInput = (event) => {
    const next = Number(event.target.value);
    emitRange(Math.min(next, current.value.max), current.value.max);
};

const onMaxInput = (event) => {
    const next = Number(event.target.value);
    emitRange(current.value.min, Math.max(next, current.value.min));
};
</script>

<template>
    <div class="flex w-full min-w-44 max-w-xs flex-col gap-1">
        <div
            class="flex justify-between text-xs tabular-nums"
            :class="accent ? '' : 'opacity-80'"
            :style="valueLabelStyle"
        >
            <span>{{ current.min }}</span>
            <span>{{ current.max }}</span>
        </div>
        <div class="range-dual relative h-6" :class="colorClass" :style="accentStyle">
            <div class="range-dual-track" aria-hidden="true" />
            <div class="range-dual-fill" :style="fillStyle" aria-hidden="true" />
            <input
                type="range"
                class="range-dual-input"
                :style="{ zIndex: minOnTop ? 5 : 3 }"
                :value="current.min"
                :min="bounds.min"
                :max="bounds.max"
                :step="step"
                :disabled="disabled"
                aria-label="Minimum"
                @input="onMinInput"
            />
            <input
                type="range"
                class="range-dual-input"
                :style="{ zIndex: minOnTop ? 3 : 5 }"
                :value="current.max"
                :min="bounds.min"
                :max="bounds.max"
                :step="step"
                :disabled="disabled"
                aria-label="Maximum"
                @input="onMaxInput"
            />
        </div>
    </div>
</template>

<style scoped lang="scss">
.range-dual {
    --range-color: var(--color-primary-500);
}

.color-primary { --range-color: var(--color-primary-500); }
.color-secondary { --range-color: var(--color-secondary-500); }
.color-accent { --range-color: var(--color-accent-500); }
.color-info { --range-color: var(--color-info-500); }
.color-success { --range-color: var(--color-success-500); }
.color-warning { --range-color: var(--color-warning-500); }
.color-error { --range-color: var(--color-error-500); }
.color-neutral { --range-color: var(--color-neutral-500); }

.range-dual-track,
.range-dual-fill {
    position: absolute;
    top: 50%;
    height: 6px;
    border-radius: 999px;
    transform: translateY(-50%);
    pointer-events: none;
}

.range-dual-track {
    inset-inline: 0;
    background: color-mix(in srgb, var(--range-color) 16%, transparent);
}

.range-dual-fill {
    background: var(--range-color);
}

.range-dual-input {
    position: absolute;
    inset: 0;
    margin: 0;
    width: 100%;
    appearance: none;
    background: transparent;
    pointer-events: none;
    cursor: pointer;

    &:disabled {
        cursor: not-allowed;
        opacity: 0.6;
    }

    &:focus {
        outline: none;
    }

    &::-webkit-slider-runnable-track {
        height: 6px;
        background: transparent;
    }

    &::-moz-range-track {
        height: 6px;
        background: transparent;
        border: none;
    }

    &::-webkit-slider-thumb {
        appearance: none;
        pointer-events: auto;
        width: 18px;
        height: 18px;
        margin-top: -6px;
        border-radius: 50%;
        border: 2px solid white;
        background: var(--range-color);
        box-shadow: 0 1px 3px color-mix(in srgb, var(--range-color) 40%, transparent);
    }

    &::-moz-range-thumb {
        pointer-events: auto;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 2px solid white;
        background: var(--range-color);
        box-shadow: 0 1px 3px color-mix(in srgb, var(--range-color) 40%, transparent);
    }
}
</style>
