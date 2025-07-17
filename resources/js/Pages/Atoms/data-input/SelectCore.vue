<script setup>
/**
 * SelectCore Atom (DaisyUI, Atomic Design)
 *
 * @description
 * Atom de base pour les champs select, stylé DaisyUI, sans gestion de label top/bottom.
 * - Props : v-model, options, multiple, disabled, readonly, color, size, variant, etc.
 * - Accessibilité : id, ariaLabel, role, tabindex, aria-invalid, etc.
 * - Utilise getInputClasses pour les classes DaisyUI/Tailwind
 * - Slots : labelInStart, labelInEnd, default (pour les <option>)
 * - Support des utilitaires custom (shadow, backdrop, opacity, rounded)
 *
 * @see https://daisyui.com/components/select/
 * @version DaisyUI v5.x
 *
 * @example
 * <SelectCore v-model="status" :options="statusOptions" labelInStart="Type" />
 * <SelectCore v-model="type" :options="typeOptions">
 *   <template #labelInStart>Type</template>
 *   <template #labelInEnd>.com</template>
 * </SelectCore>
 *
 * @props {Array} options - Options du select (obligatoire)
 * @props {String|Number|Array} modelValue - v-model
 * @props {Boolean} multiple, disabled, readonly, required
 * @props {String} color, size, variant
 * @props {String} id, ariaLabel, role, tabindex
 * @props {Boolean|String} aria-invalid
 * @props {String} labelInStart - Label inline à gauche (dans la balise label)
 * @props {String} labelInEnd - Label inline à droite (dans la balise label)
 * @slot labelInStart - Slot pour label inline à gauche
 * @slot labelInEnd - Slot pour label inline à droite
 * @slot default - <option> personnalisées
 */
import { ref, computed, useAttrs } from 'vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';
import { getInputProps, getInputAttrs } from '@/Utils/atomic-design/inputHelper';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    ...getInputProps('select', 'core'),
});

const emit = defineEmits(['update:modelValue']);
const selectRef = ref(null);
const $attrs = useAttrs();
const atomClasses = computed(() => {
    return mergeClasses(
        [
            'select',
            props.color === 'neutral' && 'select-neutral',
            props.color === 'primary' && 'select-primary',
            props.color === 'secondary' && 'select-secondary',
            props.color === 'accent' && 'select-accent',
            props.color === 'info' && 'select-info',
            props.color === 'success' && 'select-success',
            props.color === 'warning' && 'select-warning',
            props.color === 'error' && 'select-error',
            props.size === 'xs' && 'select-xs',
            props.size === 'sm' && 'select-sm',
            props.size === 'md' && 'select-md',
            props.size === 'lg' && 'select-lg',
            props.size === 'xl' && 'select-xl',
            props.variant === 'ghost' && 'select-ghost',
            props.variant === 'bordered' && 'select-bordered',
        ].filter(Boolean),
        getCustomUtilityClasses(props)
    );
});

const attrs = computed(() => ({
        ...getCommonAttrs(props),
    ...getInputAttrs('select', 'core'),
        multiple: props.multiple,
        'aria-label': typeof props.ariaLabel === 'string' ? props.ariaLabel : undefined,
        'aria-invalid': props['aria-invalid'] !== undefined ? props['aria-invalid'] : undefined,
        readonly: props.readonly,
        ref: selectRef,
        value: props.modelValue,
}));

function onChange(e) {
    let value;
    if (props.multiple) {
        value = Array.from(e.target.selectedOptions).map(opt => opt.value);
    } else {
        value = e.target.value;
    }
    emit('update:modelValue', value);
}
</script>

<template>
    <label :class="atomClasses">
        <span v-if="labelInStart || $slots.labelInStart" class="label-text">
            <slot name="labelInStart">{{ labelInStart }}</slot>
        </span>
        <select
            v-bind="attrs"
            v-on="$attrs"
            @change="onChange"
        >
            <slot>
                <option v-for="option in options" :key="option.value ?? option" :value="option.value ?? option">
                    {{ option.label ?? option }}
                </option>
            </slot>
        </select>
        <span v-if="labelInEnd || $slots.labelInEnd" class="label-text">
            <slot name="labelInEnd">{{ labelInEnd }}</slot>
        </span>
    </label>
</template>

<style scoped></style>
