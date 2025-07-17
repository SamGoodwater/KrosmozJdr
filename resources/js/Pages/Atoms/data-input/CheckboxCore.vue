<script setup>
/**
 * CheckboxCore Atom (DaisyUI, Atomic Design)
 *
 * @description
 * Atom de base pour les cases à cocher, stylé DaisyUI, sans gestion de label inStart/inEnd.
 * - Props : v-model (checked), indeterminate, bgOn, bgOff, color, size, disabled, readonly, required, etc.
 * - Utilisation des classes DaisyUI (checkbox, checkbox-primary, etc.)
 * - Émission de update:modelValue sur changement
 * - Gestion de l'état indeterminate via ref et watch
 * - Slot par défaut pour le label externe (texte ou HTML)
 *
 * @see https://daisyui.com/components/checkbox/
 * @version DaisyUI v5.x
 *
 * @example
 * <CheckboxCore v-model="checked" label="Se souvenir de moi" color="primary" />
 * <CheckboxCore v-model="checked" :indeterminate="true" bgOn="bg-green-500" bgOff="bg-gray-200">Option</CheckboxCore>
 *
 * @props {Boolean} modelValue - v-model (checked)
 * @props {Boolean} indeterminate - État indéterminé
 * @props {String} bgOn - Classe bg-* à appliquer quand coché
 * @props {String} bgOff - Classe bg-* à appliquer quand décoché
 * @props {String} color, size
 * @props {Boolean} disabled, readonly, required
 * @props {String} id, ariaLabel, role, tabindex
 * @slot default - Label externe (texte ou HTML)
 */
import { ref, computed, watch, onMounted, useAttrs } from 'vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';
import { getInputProps, getInputAttrs } from '@/Utils/atomic-design/inputHelper';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    ...getInputProps('checkbox', 'core'),
});

const emit = defineEmits(['update:modelValue']);
const checkboxRef = ref(null);
const $attrs = useAttrs();

const atomClasses = computed(() => {
    return mergeClasses(
        [
            'checkbox',
            props.color === 'neutral' && 'checkbox-neutral',
            props.color === 'primary' && 'checkbox-primary',
            props.color === 'secondary' && 'checkbox-secondary',
            props.color === 'accent' && 'checkbox-accent',
            props.color === 'info' && 'checkbox-info',
            props.color === 'success' && 'checkbox-success',
            props.color === 'warning' && 'checkbox-warning',
            props.color === 'error' && 'checkbox-error',
            props.size === 'xs' && 'checkbox-xs',
            props.size === 'sm' && 'checkbox-sm',
            props.size === 'md' && 'checkbox-md',
            props.size === 'lg' && 'checkbox-lg',
            props.size === 'xl' && 'checkbox-xl',
            hasValidationState.value && 'checkbox-error',
            props.modelValue && props.bgOn,
            !props.modelValue && props.bgOff,
        ].filter(Boolean),
        getCustomUtilityClasses(props)
    );
});

const attrs = computed(() => ({
        ...getCommonAttrs(props),
    ...getInputAttrs('checkbox', 'core'),
        'aria-label': typeof props.ariaLabel === 'string' ? props.ariaLabel : undefined,
        'aria-invalid': props['aria-invalid'] !== undefined ? props['aria-invalid'] : undefined,
        readonly: props.readonly,
        ref: checkboxRef,
        checked: props.modelValue,
}));

function onChange(e) {
    emit('update:modelValue', e.target.checked);
}

// Gérer l'état indeterminate
watch(() => props.indeterminate, (val) => {
    if (checkboxRef.value) {
        checkboxRef.value.indeterminate = val;
    }
}, { immediate: true });

onMounted(() => {
    if (checkboxRef.value) {
        checkboxRef.value.indeterminate = props.indeterminate;
    }
});
</script>

<template>
    <label class="inline-flex items-center gap-2 cursor-pointer select-none">
        <input
            type="checkbox"
            v-bind="attrs"
            v-on="$attrs"
            :class="atomClasses"
            @change="onChange"
        />
        <span><slot /></span>
    </label>
</template>

<style scoped></style>
