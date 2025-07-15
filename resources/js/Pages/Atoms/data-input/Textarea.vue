<script setup>
defineOptions({ inheritAttrs: false }); // Pour que les événements natifs soient transmis à l'atom

/**
 * Textarea Atom (DaisyUI)
 *
 * @description
 * Composant atomique Textarea conforme DaisyUI (v5.x) et Atomic Design.
 * - Gère tous les cas d'usage textarea (simple, aide, validation, etc.)
 * - Props DaisyUI : color, size
 * - Props communes input via getInputProps()
 * - Props utilitaires custom : shadow, backdrop, opacity
 * - Slots : #labelTop, #labelBottom, #validator, #helper, default
 * - v-model natif (modelValue). Si modelValue n'est pas défini, fallback sur la prop value.
 * - Toutes les classes DaisyUI sont explicites
 * - Accessibilité renforcée (role, aria, etc.)
 *
 * @see https://daisyui.com/components/textarea/
 * @version DaisyUI v5.x
 *
 * @example
 * <Textarea label="Message" v-model="message" color="primary" size="md" :validator="form.errors.message" helper="Votre message" />
 *
 * @props {String} color - Couleur DaisyUI ('', 'neutral', 'primary', ...)
 * @props {String} size - Taille DaisyUI ('', 'xs', 'sm', 'md', 'lg', 'xl')
 * @props {String} label - Label du champ (optionnel, sinon slot #labelTop)
 * @props {String|Object} validator - Message de validation ou slot #validator
 * @props {String} errorMessage - Message d'erreur (optionnel)
 * @props {String} helper - Message d'aide (optionnel ou slot #helper)
 * @props {String} shadow, backdrop, opacity - utilitaires custom
 * @props {String|Object} id, ariaLabel, role, tabindex, class, style, disabled - hérités de commonProps
 * @slot labelTop - Label custom au-dessus
 * @slot labelBottom - Label custom en-dessous
 * @slot validator - Message de validation custom
 * @slot helper - Message d'aide custom
 * @slot default - input natif (optionnel)
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
const textareaRef = ref(null);

const hasValidationState = computed(() => hasValidation(props, useSlots()));

const atomClasses = computed(() =>
    mergeClasses(
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
        getCustomUtilityClasses(props),
        props.class
    )
);

const textareaId = computed(() => props.id || `textarea-${Math.random().toString(36).substr(2, 9)}`);

const attrs = computed(() => getCommonAttrs(props));

function onInput(e) {
    emit('update:modelValue', e.target.value);
}

onMounted(() => {
    if (textareaRef.value && props.autofocus) {
        textareaRef.value.focus();
    }
});

defineExpose({ focus: () => textareaRef.value && textareaRef.value.focus() });
</script>

<template>
    <div class="form-control w-full">
        <InputLabel v-if="props.label || $slots.labelTop" :for="textareaId" :value="props.label">
            <template v-if="$slots.labelTop" #default>
                <slot name="labelTop" />
            </template>
        </InputLabel>
        <div class="flex items-center gap-2">
            <textarea ref="textareaRef" v-bind="attrs" :id="textareaId" :class="atomClasses" @input="onInput" v-on="$attrs"></textarea>
        </div>
        <InputLabel v-if="props.labelBottom || $slots.labelBottom" :for="textareaId" :value="props.labelBottom" class="mt-1">
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
