<script setup>
/**
 * FileInputAtom (DaisyUI)
 *
 * @description
 * Atomique input file stylé DaisyUI, conforme Atomic Design et KrosmozJDR.
 * - Rend un <input type="file"> stylé DaisyUI
 * - Props DaisyUI : color, size, variant (ghost)
 * - Props utilitaires custom : shadow, backdrop, opacity, rounded
 * - Props communes : accessibilité, etc.
 * - mergeClasses pour la composition des classes
 * - getCommonAttrs pour les attributs HTML/accessibilité
 * - AUCUNE logique de drag&drop, preview, progress, label, etc. (géré par la molécule)
 *
 * @see https://daisyui.com/components/file-input/
 * @version DaisyUI v5.x
 *
 * @example
 * <FileInputAtom color="primary" size="md" />
 *
 * @props {String} color - Couleur DaisyUI ('', 'neutral', 'primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error')
 * @props {String} size - Taille DaisyUI ('', 'xs', 'sm', 'md', 'lg', 'xl')
 * @props {String} variant - Style DaisyUI ('', 'ghost')
 * @props {String} shadow, backdrop, opacity, rounded - utilitaires custom
 * @props {String|Object} id, ariaLabel, role, tabindex, class, style, disabled - hérités de commonProps
 */
import { computed, ref, watch, onMounted, useSlots } from 'vue';
import Validator from '@/Pages/Atoms/data-input/Validator.vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';
import { getInputAttrs, getInputProps, hasValidation } from '@/Utils/atomic-design/atomManager';
import InputLabel from '@/Pages/Atoms/data-input/InputLabel.vue';
import { colorList, sizeXlList } from '@/Pages/Atoms/atomMap';

const props = defineProps({
    ...getCommonProps(),
    ...getInputProps({ exclude: ['type', 'placeholder', 'autocomplete', 'min', 'max', 'step', 'inputmode', 'pattern', 'maxlength', 'minlength'] }),
    ...getCustomUtilityProps(),
    color: {
        type: String,
        default: '',
        validator: v => colorList.includes(v),
    },
    size: {
        type: String,
        default: '',
        validator: v => sizeXlList.includes(v),
    },
    labelBottom: { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue']);
const fileInputRef = ref(null);

const hasValidationState = computed(() => hasValidation(props, useSlots()));

const atomClasses = computed(() =>
    mergeClasses(
        [
            'file-input',
            props.color === 'neutral' && 'file-input-neutral',
            props.color === 'primary' && 'file-input-primary',
            props.color === 'secondary' && 'file-input-secondary',
            props.color === 'accent' && 'file-input-accent',
            props.color === 'info' && 'file-input-info',
            props.color === 'success' && 'file-input-success',
            props.color === 'warning' && 'file-input-warning',
            props.color === 'error' && 'file-input-error',
            props.size === 'xs' && 'file-input-xs',
            props.size === 'sm' && 'file-input-sm',
            props.size === 'md' && 'file-input-md',
            props.size === 'lg' && 'file-input-lg',
            props.size === 'xl' && 'file-input-xl',
            hasValidationState.value && 'file-input-error',
        ].filter(Boolean),
        getCustomUtilityClasses(props),
        props.class
    )
);

const fileInputId = computed(() => props.id || `file-input-${Math.random().toString(36).substr(2, 9)}`);

const attrs = computed(() => getCommonAttrs(props));

function onInput(e) {
    emit('update:modelValue', e.target.files);
}

onMounted(() => {
    if (fileInputRef.value && props.autofocus) {
        fileInputRef.value.focus();
    }
});

defineExpose({ focus: () => fileInputRef.value && fileInputRef.value.focus() });
</script>

<template>
    <div class="form-control w-full">
        <InputLabel v-if="props.label || $slots.labelTop" :for="fileInputId" :value="props.label">
            <template v-if="$slots.labelTop" #default>
                <slot name="labelTop" />
            </template>
        </InputLabel>
        <div class="flex items-center gap-2">
            <input ref="fileInputRef" type="file" :class="atomClasses" v-bind="attrs" @input="onInput" v-on="$attrs" />
        </div>
        <InputLabel v-if="props.labelBottom || $slots.labelBottom" :for="fileInputId" :value="props.labelBottom" class="mt-1">
            <template v-if="$slots.labelBottom" #default>
                <slot name="labelBottom" />
            </template>
        </InputLabel>
        <div v-if="hasValidationState" class="mt-1">
            <slot name="validator">
                <Validator v-if="props.validator"
                    :state="typeof props.validator === 'string' ? 'error' : 'error'"
                    :message="typeof props.validator === 'string' ? props.validator : props.errorMessage" />
            </slot>
        </div>
        <div v-if="props.helper || $slots.helper" class="mt-1 text-xs text-base-400">
            <slot name="helper">{{ props.helper }}</slot>
        </div>
    </div>
</template>

<style scoped></style>
