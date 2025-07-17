<script setup>
/**
 * Range Atom (DaisyUI)
 *
 * @description
 * Composant atomique Range (slider) conforme DaisyUI (v5.x) et Atomic Design.
 * - Gère tous les cas d'usage range (slider simple, aide, validation, etc.)
 * - Props DaisyUI : color, size
 * - Props communes input via getInputProps()
 * - Props utilitaires custom : shadow, backdrop, opacity
 * - Edition réactive avancée via useFieldComposable/field/debounceTime (voir ci-dessous)
 * - Slots : #labelTop, #labelBottom, #validator, #helper, default
 * - v-model natif (modelValue)
 * - Toutes les classes DaisyUI sont explicites
 * - Accessibilité renforcée (role, aria, etc.)
 *
 * @see https://daisyui.com/components/range/
 * @version DaisyUI v5.x
 *
 * @note Toutes les classes DaisyUI sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 *
 * @example
 * <Range label="Volume" v-model="volume" min="0" max="100" color="primary" size="md" useFieldComposable :debounceTime="300" />
 *
 * @props {Number|String} modelValue - Valeur du slider (v-model natif)
 * @props {Number|String} min - Valeur minimale
 * @props {Number|String} max - Valeur maximale
 * @props {Number|String} step - Pas
 * @props {String} color - Couleur DaisyUI ('', 'neutral', 'primary', ...)
 * @props {String} size - Taille DaisyUI ('', 'xs', 'sm', 'md', 'lg', 'xl')
 * @props {String} label - Label du champ (optionnel, sinon slot #labelTop)
 * @props {String|Object} validator - Message de validation ou slot #validator
 * @props {String} errorMessage - Message d'erreur (optionnel)
 * @props {String} helper - Message d'aide (optionnel ou slot #helper)
 * @props {String} shadow, backdrop, opacity - utilitaires custom
 * @props {Boolean} useFieldComposable - Active l'édition réactive (reset, debounce, etc.)
 * @props {Object} field - Objet field externe (optionnel, sinon composable interne)
 * @props {Number} debounceTime - Délai de debounce (ms, défaut 500)
 * @props {String|Object} id, ariaLabel, role, tabindex - hérités de commonProps
 * @slot labelTop - Label custom au-dessus
 * @slot labelBottom - Label custom en-dessous
 * @slot validator - Message de validation custom
 * @slot helper - Message d'aide custom
 * @slot default - Slot pour contenu custom à droite du slider
 *
 * @note Si useFieldComposable=true, la logique d'édition réactive (valeur, debounce, reset, bouton reset, update) est entièrement gérée par le composable useEditableField. Le bouton reset s'affiche automatiquement si la valeur a été modifiée.
 */
import { computed, ref, watch, onMounted, onUnmounted, useSlots } from 'vue';
import Validator from '@/Pages/Atoms/data-input/Validator.vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';
import { getInputProps, getInputAttrs } from '@/Utils/atomic-design/inputHelper';

const props = defineProps({
    ...getCommonProps(),
    ...getInputProps('range', 'core'),
    ...getCustomUtilityProps(),
});

const emit = defineEmits(['update:modelValue']);
const rangeRef = ref(null);

// Détermine si le composant doit afficher un état de validation
const hasValidationState = computed(() => hasValidation(props, useSlots()));

// Gestion editableField (optionnel)
const editableField = computed(() => {
    if (props.useFieldComposable) {
        return useEditableField(props.modelValue, {
            field: props.field,
            debounce: props.debounceTime,
            onUpdate: (val) => emit('update:modelValue', val),
        });
    }
    return null;
});

const isFieldModified = computed(() =>
    props.useFieldComposable && editableField.value
        ? editableField.value.isModified.value
        : false,
);

const displayValue = computed(() => {
    if (props.useFieldComposable && editableField.value) {
        return editableField.value.value.value;
    }
    return props.modelValue;
});

function onInput(e) {
    if (props.useFieldComposable && editableField.value) {
        editableField.value.onInput(e);
    } else {
        emit('update:modelValue', e.target.value);
    }
}

function onBlur() {
    if (props.useFieldComposable && editableField.value) {
        editableField.value.onBlur();
    }
}

function handleReset() {
    if (
        props.useFieldComposable &&
        editableField.value &&
        typeof editableField.value.reset === 'function'
    ) {
        editableField.value.reset();
        editableField.value.onBlur();
    }
}

onMounted(() => {
    if (rangeRef.value && props.autofocus) {
        rangeRef.value.focus();
    }
});
onUnmounted(() => {
    if (editableField.value && editableField.value.debounceTimeout) {
        clearTimeout(editableField.value.debounceTimeout);
    }
});
defineExpose({ 
    focus: () => rangeRef.value && rangeRef.value.focus(),
    isFieldModified,
    handleReset,
});

const atomClasses = computed(() =>
    mergeClasses(
        [
            'range',
            props.color === 'neutral' && 'range-neutral',
            props.color === 'primary' && 'range-primary',
            props.color === 'secondary' && 'range-secondary',
            props.color === 'accent' && 'range-accent',
            props.color === 'info' && 'range-info',
            props.color === 'success' && 'range-success',
            props.color === 'warning' && 'range-warning',
            props.color === 'error' && 'range-error',
            props.size === 'xs' && 'range-xs',
            props.size === 'sm' && 'range-sm',
            props.size === 'md' && 'range-md',
            props.size === 'lg' && 'range-lg',
            props.size === 'xl' && 'range-xl',
            hasValidationState.value && 'range-error',
        ].filter(Boolean),
        getCustomUtilityClasses(props),
        props.class
    )
);

const rangeId = computed(() => props.id || `range-${Math.random().toString(36).substr(2, 9)}`);

const attrs = computed(() => ({
    ...getCommonAttrs(props),
    ...getInputAttrs('range', 'core'),
}));
</script>

<template>
    <div class="form-control w-full">
        <!-- Label top -->
        <InputLabel v-if="props.label || $slots.labelTop" :for="rangeId" :value="props.label">
            <template v-if="$slots.labelTop" #default>
                <slot name="labelTop" />
            </template>
        </InputLabel>
        <div class="flex items-center gap-2">
            <input ref="rangeRef" type="range" v-bind="attrs" :id="rangeId" :class="atomClasses" @input="onInput" v-on="$attrs" />
        </div>
        <!-- Label bottom -->
        <InputLabel v-if="props.labelBottom || $slots.labelBottom" :for="rangeId" :value="props.labelBottom" class="mt-1">
            <template v-if="$slots.labelBottom" #default>
                <slot name="labelBottom" />
            </template>
        </InputLabel>
        <!-- Validator -->
        <div v-if="hasValidationState" class="mt-1">
            <slot name="validator">
                <Validator v-if="props.validator"
                    :state="typeof props.validator === 'string' ? 'error' : 'error'"
                    :message="typeof props.validator === 'string' ? props.validator : props.errorMessage" />
            </slot>
        </div>
        <!-- Helper -->
        <div v-if="props.helper || $slots.helper" class="mt-1 text-xs text-base-400">
            <slot name="helper">{{ props.helper }}</slot>
        </div>
    </div>
</template>

<style scoped></style>
