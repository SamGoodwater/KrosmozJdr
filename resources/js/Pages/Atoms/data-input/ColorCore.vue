<script setup>
defineOptions({ inheritAttrs: false });

/**
 * ColorCore Atom (DaisyUI, Atomic Design)
 *
 * @description
 * Sélecteur de couleur : input HTML type="color" (hex), ou avec `limitTo="tailwind"` un select de palettes Tailwind.
 *
 * @example
 * <ColorCore v-model="color" />
 * <ColorCore v-model="paletteName" limit-to="tailwind" />
 */
import { computed, useAttrs } from 'vue';
import { getInputStyle } from '@/Composables/form/useInputStyle';
import useInputProps from '@/Composables/form/useInputProps';
import { getInputPropsDefinition } from '@/Utils/atomic-design/inputHelper';
import { mergeClasses } from '@/Utils/atomic-design/uiHelper';
import { TAILWIND_CHARACTERISTIC_PALETTES, formatPaletteLabel } from '@/Constants/tailwindCharacteristicPalettes';

const props = defineProps(getInputPropsDefinition('color', 'core'));
const emit = defineEmits(['update:modelValue']);
const $attrs = useAttrs();

const { inputAttrs, listeners } = useInputProps(props, $attrs, emit, 'color', 'core');

const atomClasses = computed(() =>
    mergeClasses(
        getInputStyle(
            'color',
            {
                variant: props.variant,
                color: props.color,
                size: props.size,
                animation: props.animation,
                ...(typeof props.inputStyle === 'object' && props.inputStyle !== null ? props.inputStyle : {}),
                ...(typeof props.inputStyle === 'string' ? { variant: props.inputStyle } : {}),
            },
            false,
        ),
    ),
);

const isTailwindMode = computed(() => props.limitTo === 'tailwind');

/** Le helper associe `type: 'color'` à l’input natif ; sur `<select>` il faut l’omettre. */
const tailwindInputAttrs = computed(() => {
    const raw = inputAttrs.value;
    if (!raw || typeof raw !== 'object') return {};
    const { type: _ignored, ...rest } = raw;
    return rest;
});

const tailwindOptions = computed(() =>
    TAILWIND_CHARACTERISTIC_PALETTES.map((name) => ({
        value: name,
        label: formatPaletteLabel(name),
    })),
);

/** Valeur affichée par l'input color natif (toujours hex valide). */
const displayValue = computed(() => {
    const v = props.modelValue;
    if (v && typeof v === 'string' && /^#([0-9A-Fa-f]{3}){1,2}$/.test(v.trim())) {
        return v.trim();
    }
    return '#000000';
});

const tailwindSelectValue = computed(() => {
    const v = props.modelValue;
    if (v == null || v === '') {
        return '';
    }
    const s = String(v).trim().toLowerCase();
    if (TAILWIND_CHARACTERISTIC_PALETTES.includes(s)) {
        return s;
    }
    return '';
});

function onInput(e) {
    emit('update:modelValue', e.target.value);
}

function onTailwindChange(e) {
    const val = e.target.value;
    emit('update:modelValue', val === '' ? '' : val);
}
</script>

<template>
    <select
        v-if="isTailwindMode"
        v-bind="tailwindInputAttrs"
        class="select select-bordered w-full max-w-xs"
        :class="atomClasses"
        :value="tailwindSelectValue"
        @change="onTailwindChange"
    >
        <option value="">{{ $attrs.placeholder || '— Palette —' }}</option>
        <option v-for="opt in tailwindOptions" :key="opt.value" :value="opt.value">
            {{ opt.label }}
        </option>
    </select>
    <input
        v-else
        type="color"
        v-bind="inputAttrs"
        :class="['input color-core-input', atomClasses]"
        :value="displayValue"
        v-on="listeners"
        @input="onInput"
    />
</template>

<style scoped lang="scss">
input[type='color'].color-core-input {
    width: 2.5rem;
    height: 2.5rem;
    min-width: 2.5rem;
    min-height: 2.5rem;
    padding: 0;
    border: none !important;
    border-radius: 0.375rem;
    cursor: pointer;
    outline: none;
    box-shadow: none;
    transition: opacity 0.2s ease-in-out;
    --color: var(--color-primary-500);

    &:focus {
        outline: none;
        box-shadow: none;
    }

    &:hover {
        opacity: 0.9;
    }

    &:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
}
</style>
