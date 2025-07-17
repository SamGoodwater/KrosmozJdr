<script setup>
/**
 * TextareaCore Atom (DaisyUI, Atomic Design)
 *
 * @description
 * Atom de base pour les champs textarea, stylé DaisyUI, sans gestion de label top/bottom/floating.
 * - Props : v-model, placeholder, color, size, variant, disabled, readonly, etc.
 * - Accessibilité : id, ariaLabel, role, tabindex, aria-invalid, etc.
 * - Utilise les classes DaisyUI (textarea, textarea-primary, etc.)
 * - Slots : labelInStart, labelInEnd, default (pour le contenu du textarea)
 * - Support des utilitaires custom (shadow, backdrop, opacity, rounded)
 *
 * @see https://daisyui.com/components/textarea/
 * @version DaisyUI v5.x
 *
 * @example
 * <TextareaCore v-model="bio" placeholder="Bio" labelInStart="✍️" />
 * <TextareaCore v-model="desc">
 *   <template #labelInStart>✍️</template>
 *   <template #labelInEnd>🔒</template>
 * </TextareaCore>
 *
 * @props {String|Number} modelValue - v-model
 * @props {String} placeholder
 * @props {Boolean} disabled, readonly, required
 * @props {String} color, size, variant
 * @props {String} id, ariaLabel, role, tabindex
 * @props {Boolean|String} aria-invalid
 * @props {String} labelInStart - Label inline à gauche (dans la balise label)
 * @props {String} labelInEnd - Label inline à droite (dans la balise label)
 * @slot labelInStart - Slot pour label inline à gauche
 * @slot labelInEnd - Slot pour label inline à droite
 * @slot default - Contenu du textarea (optionnel)
 */
import { ref, computed, useAttrs } from 'vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';
import { getInputProps, getInputAttrs } from '@/Utils/atomic-design/inputHelper';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    ...getInputProps('textarea', 'core'),
});

const emit = defineEmits(['update:modelValue']);
const textareaRef = ref(null);
const $attrs = useAttrs();

const atomClasses = computed(() => {
    return mergeClasses(
        [
            'textarea',
            props.color === 'neutral' && 'textarea-neutral',
            props.color === 'primary' && 'textarea-primary',
            props.color === 'secondary' && 'textarea-secondary',
            props.color === 'accent' && 'textarea-accent',
            props.color === 'info' && 'textarea-info',
            props.color === 'success' && 'textarea-success',
            props.color === 'warning' && 'textarea-warning',
            props.color === 'error' && 'textarea-error',
            props.size === 'xs' && 'textarea-xs',
            props.size === 'sm' && 'textarea-sm',
            props.size === 'md' && 'textarea-md',
            props.size === 'lg' && 'textarea-lg',
            props.size === 'xl' && 'textarea-xl',
            hasValidationState.value && 'textarea-error',
        ].filter(Boolean),
        getCustomUtilityClasses(props)
    );
});

const attrs = computed(() => ({
        ...getCommonAttrs(props),
    ...getInputAttrs('textarea', 'core'),
        'aria-label': typeof props.ariaLabel === 'string' ? props.ariaLabel : undefined,
        'aria-invalid': props['aria-invalid'] !== undefined ? props['aria-invalid'] : undefined,
        readonly: props.readonly,
        ref: textareaRef,
        value: props.modelValue,
}));

function onInput(e) {
    emit('update:modelValue', e.target.value);
}
</script>

<template>
    <label :class="atomClasses">
        <span v-if="labelInStart || $slots.labelInStart" class="label-text">
            <slot name="labelInStart">{{ labelInStart }}</slot>
        </span>
        <textarea
            v-bind="attrs"
            v-on="$attrs"
            @input="onInput"
        >
            <slot />
        </textarea>
        <span v-if="labelInEnd || $slots.labelInEnd" class="label-text">
            <slot name="labelInEnd">{{ labelInEnd }}</slot>
        </span>
    </label>
</template>

<style scoped></style>
