<script setup>
/**
 * Select Atom (DaisyUI + Custom Utility + Edition réactive)
 *
 * @description
 * Composant atomique Select conforme DaisyUI (v5.x) et Atomic Design.
 * - Gère tous les cas d'usage select (simple, multiple, aide, validation, etc.)
 * - Props DaisyUI : color, size, variant
 * - Props communes input via getInputProps()
 * - Props utilitaires custom : shadow, backdrop, opacity
 * - Slots : #labelTop, #labelBottom, #validator, #helper, default (pour options custom)
 * - v-model natif
 * - Edition réactive avancée via useFieldComposable/field/debounceTime (voir ci-dessous)
 * - Toutes les classes DaisyUI sont explicites
 * - Accessibilité renforcée (role, aria, etc.)
 *
 * @see https://daisyui.com/components/select/
 * @version DaisyUI v5.x
 *
 * @note Toutes les classes DaisyUI sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 *
 * @example
 * <Select label="Couleur" v-model="color" :options="[
 *   { value: '', label: 'Choisir une couleur', disabled: true },
 *   { value: 'red', label: 'Rouge' },
 *   { value: 'blue', label: 'Bleu' }
 * ]" color="primary" size="md" :validator="form.errors.color" helper="Sélectionnez une couleur" useFieldComposable :debounceTime="300" />
 *
 * @props {Array} options - Liste des options [{ value, label, disabled? }], sinon slot par défaut
 * @props {Boolean} multiple - Mode sélection multiple
 * @props {String} color - Couleur DaisyUI ('', 'neutral', 'primary', ...)
 * @props {String} size - Taille DaisyUI ('', 'xs', 'sm', 'md', 'lg', 'xl')
 * @props {String} variant - Style DaisyUI ('', 'ghost', 'bordered')
 * @props {String} label - Label du champ (optionnel, sinon slot #labelTop)
 * @props {String|Object} validator - Message de validation ou slot #validator
 * @props {String} errorMessage - Message d'erreur (optionnel)
 * @props {String} helper - Message d'aide (optionnel ou slot #helper)
 * @props {String} shadow, backdrop, opacity - utilitaires custom
 * @props {String|Object} id, ariaLabel, role, tabindex - hérités de commonProps
 * @props {Boolean} useFieldComposable - Active l'édition réactive (reset, debounce, etc.)
 * @props {Object} field - Objet field externe (optionnel, sinon composable interne)
 * @props {Number} debounceTime - Délai de debounce (ms, défaut 500)
 * @slot labelTop - Label custom au-dessus
 * @slot labelBottom - Label custom en-dessous
 * @slot validator - Message de validation custom
 * @slot helper - Message d'aide custom
 * @slot default - Slot pour options custom (remplace la prop options)
 *
 * @note Si useFieldComposable=true, la logique d'édition réactive (valeur, debounce, reset, bouton reset, update) est entièrement gérée par le composable useEditableField. Le bouton reset s'affiche automatiquement si la valeur a été modifiée.
 */
import { computed, ref, watch, onMounted, onUnmounted, useSlots } from 'vue';
import Validator from '@/Pages/Atoms/data-input/Validator.vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';
import { getInputAttrs, getInputProps, hasValidation } from '@/Utils/atomic-design/atomManager';
import InputLabel from '@/Pages/Atoms/data-input/InputLabel.vue';
import useEditableField from '@/Composables/form/useEditableField';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import { colorList, sizeXlList } from '@/Pages/Atoms/atomMap';

const props = defineProps({
    ...getCommonProps(),
    ...getInputProps({ exclude: ['type', 'placeholder', 'autocomplete', 'min', 'max', 'step', 'inputmode', 'pattern', 'maxlength', 'minlength'] }),
    ...getCustomUtilityProps(),
    options: { type: Array, default: null },
    multiple: { type: Boolean, default: false },
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
    variant: {
        type: String,
        default: '',
        validator: v => ['', 'ghost', 'bordered'].includes(v),
    },
    useFieldComposable: { type: Boolean, default: false },
    field: { type: Object, default: null },
    debounceTime: { type: Number, default: 500 },
    labelBottom: { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue']);
const selectRef = ref(null);

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
    if (selectRef.value && props.autofocus) {
        selectRef.value.focus();
    }
});
onUnmounted(() => {
    if (editableField.value && editableField.value.debounceTimeout) {
        clearTimeout(editableField.value.debounceTimeout);
    }
});
defineExpose({ 
    focus: () => selectRef.value && selectRef.value.focus(),
    isFieldModified,
    handleReset,
});

const atomClasses = computed(() =>
    mergeClasses(
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
            hasValidationState.value && 'select-error',
        ].filter(Boolean),
        getCustomUtilityClasses(props),
        props.class
    )
);

const selectId = computed(() => props.id || `select-${Math.random().toString(36).substr(2, 9)}`);

const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <div class="form-control w-full">
        <!-- Label top -->
        <InputLabel v-if="props.label || $slots.labelTop" :for="selectId" :value="props.label">
            <template v-if="$slots.labelTop" #default>
                <slot name="labelTop" />
            </template>
        </InputLabel>
        <div class="relative w-full">
            <select ref="selectRef" v-bind="attrs" :id="selectId" :class="atomClasses"
                :value="displayValue" @input="onInput" @blur="onBlur"
                :aria-invalid="hasValidationState" :multiple="multiple" v-on="$attrs">
                <template v-if="options && !$slots.default">
                    <option v-for="opt in options" :key="opt.value" :value="opt.value" :disabled="opt.disabled">{{
                        opt.label }}</option>
                </template>
                <slot v-else />
            </select>
            <!-- Bouton reset -->
            <Btn v-if="props.useFieldComposable && isFieldModified" class="absolute right-2 top-2 z-20" size="xs"
                variant="ghost" circle @click="handleReset" :aria-label="'Réinitialiser'">
                <i class="fa-solid fa-arrow-rotate-left"></i>
            </Btn>
        </div>
        <!-- Label bottom -->
        <InputLabel v-if="props.labelBottom || $slots.labelBottom" :for="selectId" :value="props.labelBottom" class="mt-1">
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
