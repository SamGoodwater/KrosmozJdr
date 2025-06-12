<script setup>
defineOptions({ inheritAttrs: false }); // Pour que les évéments natifs soient transmis à l'atom

/**
 * Select Atom (DaisyUI + Custom Utility + Edition réactive)
 *
 * @description
 * Composant atomique Select conforme DaisyUI (v5.x) et Atomic Design.
 * - Gère tous les cas d'usage select (simple, multiple, aide, validation, etc.)
 * - Props DaisyUI : color, size, variant
 * - Props communes input via getInputProps()
 * - Props utilitaires custom : shadow, backdrop, opacity
 * - Slots : #labelTop, #labelBottom, #validator, #help, default (pour options custom)
 * - v-model natif
 * - Edition réactive avancée via useFieldComposable/field/debounceTime (voir ci-dessous)
 * - Toutes les classes DaisyUI sont explicites
 * - Accessibilité renforcée (role, aria, etc.)
 * - Tooltip intégré
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
 * ]" color="primary" size="md" :validator="form.errors.color" help="Sélectionnez une couleur" useFieldComposable :debounceTime="300" />
 *
 * @props {Array} options - Liste des options [{ value, label, disabled? }], sinon slot par défaut
 * @props {Boolean} multiple - Mode sélection multiple
 * @props {String} color - Couleur DaisyUI ('', 'neutral', 'primary', ...)
 * @props {String} size - Taille DaisyUI ('', 'xs', 'sm', 'md', 'lg', 'xl')
 * @props {String} variant - Style DaisyUI ('', 'ghost', 'bordered')
 * @props {String} label - Label du champ (optionnel, sinon slot #labelTop)
 * @props {String|Object} validator - Message de validation ou slot #validator
 * @props {String} errorMessage - Message d'erreur (optionnel)
 * @props {String} help - Message d'aide (optionnel ou slot #help)
 * @props {String} shadow, backdrop, opacity - utilitaires custom
 * @props {String|Object} tooltip, tooltip_placement, id, ariaLabel, role, tabindex - hérités de commonProps
 * @props {Boolean} useFieldComposable - Active l'édition réactive (reset, debounce, etc.)
 * @props {Object} field - Objet field externe (optionnel, sinon composable interne)
 * @props {Number} debounceTime - Délai de debounce (ms, défaut 500)
 * @slot labelTop - Label custom au-dessus
 * @slot labelBottom - Label custom en-dessous
 * @slot validator - Message de validation custom
 * @slot help - Message d'aide custom
 * @slot default - Slot pour options custom (remplace la prop options)
 *
 * @note Si useFieldComposable=true, la logique d'édition réactive (valeur, debounce, reset, bouton reset, update) est entièrement gérée par le composable useEditableField. Le bouton reset s'affiche automatiquement si la valeur a été modifiée.
 */
import { computed, ref, onMounted, onUnmounted, defineExpose } from 'vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import Validator from '@/Pages/Atoms/data-input/Validator.vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';
import { getInputProps, getInputAttrs } from '@/Utils/atomic-design/atomManager';
import InputLabel from '@/Pages/Atoms/data-input/InputLabel.vue';
import useEditableField from '@/Composables/form/useEditableField';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import { colorList, sizeXlList } from '@/Pages/Atoms/atomMap';

const props = defineProps({
    ...getCommonProps(),
    ...getInputProps(),
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
});

const emit = defineEmits(['update:modelValue']);
const selectRef = ref(null);

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

const isFieldModified = computed(() => props.useFieldComposable && editableField.value ? editableField.value.isModified.value : false);
const displayValue = computed(() => {
    if (props.useFieldComposable && editableField.value) {
        return editableField.value.value.value;
    }
    return props.modelValue;
});

function onInput(e) {
    const value = props.multiple ? Array.from(e.target.selectedOptions).map(o => o.value) : e.target.value;
    if (props.useFieldComposable && editableField.value) {
        editableField.value.onInput({ target: { value } });
    } else {
        emit('update:modelValue', value);
    }
}
function onBlur() {
    if (props.useFieldComposable && editableField.value) {
        editableField.value.onBlur();
    }
}
function handleReset() {
    if (props.useFieldComposable && editableField.value && typeof editableField.value.reset === 'function') {
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
defineExpose({ focus: () => selectRef.value && selectRef.value.focus() });

function getAtomClasses(props) {
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
            (props.errorMessage || props.validator) && 'validator select-error',
        ].filter(Boolean),
        getCustomUtilityClasses(props),
        props.class
    );
}

const atomClasses = computed(() => getAtomClasses(props));
const attrs = computed(() => ({
    ...getCommonAttrs(props),
    ...getInputAttrs(props),
    multiple: props.multiple || undefined,
}));

const selectId = computed(() => props.id || `select-${Math.random().toString(36).substr(2, 9)}`);
</script>

<template>
    <Tooltip :content="props.tooltip" :placement="props.tooltip_placement">
        <div class="form-control w-full">
            <!-- Label top -->
            <InputLabel v-if="label || $slots.labelTop" :for="selectId" :value="label">
                <template v-if="$slots.labelTop" #default>
                    <slot name="labelTop" />
                </template>
            </InputLabel>
            <div class="relative w-full">
                <select ref="selectRef" v-bind="attrs" v-on="$attrs" :id="selectId" :class="atomClasses"
                    :value="displayValue" @input="onInput" @blur="onBlur" :aria-invalid="!!errorMessage || validator">
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
            <InputLabel v-if="labelBottom || $slots.labelBottom" :for="selectId" :value="labelBottom" class="mt-1">
                <template v-if="$slots.labelBottom" #default>
                    <slot name="labelBottom" />
                </template>
            </InputLabel>
            <!-- Validator -->
            <div v-if="validator || $slots.validator" class="mt-1">
                <slot name="validator">
                    <Validator v-if="validator"
                        :state="validator === true ? 'success' : validator === 'error' ? 'error' : validator"
                        :message="errorMessage" />
                </slot>
            </div>
            <!-- Help -->
            <div v-if="help || $slots.help" class="mt-1 text-xs text-base-400">
                <slot name="help">{{ help }}</slot>
            </div>
        </div>
        <template v-if="typeof props.tooltip === 'object'" #tooltip>
            <slot name="tooltip" />
        </template>
    </Tooltip>
</template>

<style scoped></style>
