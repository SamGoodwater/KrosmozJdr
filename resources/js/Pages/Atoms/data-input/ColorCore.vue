<script setup>
defineOptions({ inheritAttrs: false });

/**
 * ColorCore Atom (DaisyUI, Atomic Design)
 *
 * @description
 * Sélecteur de couleur : input HTML type="color" (hex), ou avec `limitTo="tailwind"` un menu de palettes Tailwind
 * (pastille par teinte + option explicite « sans couleur »).
 *
 * @example
 * <ColorCore v-model="color" />
 * <ColorCore v-model="paletteName" limit-to="tailwind" />
 */
import { computed, ref, useAttrs } from 'vue';
import Dropdown from '@/Pages/Atoms/action/Dropdown.vue';
import { getInputStyle } from '@/Composables/form/useInputStyle';
import useInputProps from '@/Composables/form/useInputProps';
import { getInputPropsDefinition } from '@/Utils/atomic-design/inputHelper';
import { mergeClasses } from '@/Utils/atomic-design/uiHelper';
import { TAILWIND_CHARACTERISTIC_PALETTES, formatPaletteLabel } from '@/Constants/tailwindCharacteristicPalettes';
import { resolveCharacteristicUiColor } from '@/Utils/color/Color';

const props = defineProps({
    ...getInputPropsDefinition('color', 'core'),
    /**
     * Libellé pour la valeur vide (aucune palette), affiché dans le déclencheur et en tête de liste.
     */
    noneLabel: {
        type: String,
        default: 'Aucune',
    },
});
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

const paletteDropdownRef = ref(null);

const tailwindTriggerLabel = computed(() => {
    const v = tailwindSelectValue.value;
    if (v === '') {
        return props.noneLabel;
    }
    return formatPaletteLabel(v);
});

const tailwindDisabled = computed(() => Boolean(tailwindInputAttrs.value.disabled));

/**
 * Pastille CSS pour une palette Tailwind (même résolution que l’affichage des caractéristiques).
 *
 * @param {string} paletteName
 * @returns {Record<string, string>|undefined}
 */
function tailwindPaletteSwatchStyle(paletteName) {
    const css = resolveCharacteristicUiColor(paletteName);
    if (!css) return undefined;
    return { background: css };
}

function pickTailwindPalette(val) {
    emit('update:modelValue', val === '' || val == null ? '' : val);
    paletteDropdownRef.value?.close?.();
}

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
</script>

<template>
    <template v-if="isTailwindMode">
        <Dropdown
            ref="paletteDropdownRef"
            placement="bottom-start"
            variant="glass"
            color="neutral"
            size="sm"
            :class="mergeClasses('block w-full max-w-xs', $attrs.class ?? '')"
            :disabled="tailwindDisabled"
            :close-on-content-click="true"
            :aria-label="$attrs['aria-label'] || 'Choisir une couleur de palette'"
        >
            <template #trigger>
                <div
                    class="select select-bordered flex w-full cursor-pointer items-center gap-2 text-left min-h-10"
                    :class="[atomClasses, { 'pointer-events-none opacity-50': tailwindDisabled }]"
                    v-bind="tailwindInputAttrs.id ? { id: tailwindInputAttrs.id } : {}"
                >
                    <span
                        v-if="tailwindSelectValue"
                        class="h-5 w-5 shrink-0 rounded-full border border-base-300 shadow-sm"
                        :style="tailwindPaletteSwatchStyle(tailwindSelectValue)"
                        aria-hidden="true"
                    />
                    <span
                        v-else
                        class="h-5 w-5 shrink-0 rounded-full border-2 border-dashed border-base-300 bg-base-200/80"
                        aria-hidden="true"
                    />
                    <span class="min-w-0 flex-1 truncate">{{ tailwindTriggerLabel }}</span>
                    <i class="fa-solid fa-chevron-down shrink-0 text-xs opacity-60" aria-hidden="true" />
                </div>
            </template>
            <template #content>
                <ul
                    class="menu menu-sm rounded-box max-h-64 w-64 overflow-y-auto border border-base-300 bg-base-100 p-1 shadow-lg"
                    role="listbox"
                    :aria-label="$attrs['aria-label'] || 'Palettes'"
                >
                    <li role="none">
                        <button
                            type="button"
                            role="option"
                            class="flex w-full items-center gap-2 rounded-lg px-2 py-2 text-left hover:bg-base-200"
                            :aria-selected="tailwindSelectValue === ''"
                            @click="pickTailwindPalette('')"
                        >
                            <span
                                class="h-5 w-5 shrink-0 rounded-full border-2 border-dashed border-base-300 bg-base-200/80"
                                aria-hidden="true"
                            />
                            <span class="min-w-0 flex-1">{{ noneLabel }}</span>
                        </button>
                    </li>
                    <li v-for="opt in tailwindOptions" :key="opt.value" role="none">
                        <button
                            type="button"
                            role="option"
                            class="flex w-full items-center gap-2 rounded-lg px-2 py-2 text-left hover:bg-base-200"
                            :aria-selected="tailwindSelectValue === opt.value"
                            @click="pickTailwindPalette(opt.value)"
                        >
                            <span
                                class="h-5 w-5 shrink-0 rounded-full border border-base-300 shadow-sm"
                                :style="tailwindPaletteSwatchStyle(opt.value)"
                                aria-hidden="true"
                            />
                            <span class="min-w-0 flex-1">{{ opt.label }}</span>
                        </button>
                    </li>
                </ul>
            </template>
        </Dropdown>
        <input
            v-if="tailwindInputAttrs.name"
            type="hidden"
            :name="tailwindInputAttrs.name"
            :value="tailwindSelectValue"
        />
    </template>
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
