<script setup>
defineOptions({ inheritAttrs: false }); // Pour que les évéments natifs soient transmis à l'atom

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
 * - Slots : #labelTop, #labelBottom, #validator, #help, default
 * - v-model natif (modelValue)
 * - Toutes les classes DaisyUI sont explicites
 * - Accessibilité renforcée (role, aria, etc.)
 * - Tooltip intégré
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
 * @props {String} help - Message d'aide (optionnel ou slot #help)
 * @props {String} shadow, backdrop, opacity - utilitaires custom
 * @props {Boolean} useFieldComposable - Active l'édition réactive (reset, debounce, etc.)
 * @props {Object} field - Objet field externe (optionnel, sinon composable interne)
 * @props {Number} debounceTime - Délai de debounce (ms, défaut 500)
 * @props {String|Object} tooltip, tooltip_placement, id, ariaLabel, role, tabindex - hérités de commonProps
 * @slot labelTop - Label custom au-dessus
 * @slot labelBottom - Label custom en-dessous
 * @slot validator - Message de validation custom
 * @slot help - Message d'aide custom
 * @slot default - Slot pour contenu custom à droite du slider
 *
 * @note Si useFieldComposable=true, la logique d'édition réactive (valeur, debounce, reset, bouton reset, update) est entièrement gérée par le composable useEditableField. Le bouton reset s'affiche automatiquement si la valeur a été modifiée.
 */
import { computed, ref, onMounted, onUnmounted } from 'vue';
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
    useFieldComposable: { type: Boolean, default: false },
    field: { type: Object, default: null },
    debounceTime: { type: Number, default: 500 },
});

const emit = defineEmits(['update:modelValue']);
const rangeRef = ref(null);

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
    if (props.useFieldComposable && editableField.value && typeof editableField.value.reset === 'function') {
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
defineExpose({ focus: () => rangeRef.value && rangeRef.value.focus() });

function getAtomClasses(props) {
    return mergeClasses(
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
            (props.errorMessage || props.validator) && 'validator range-error',
        ].filter(Boolean),
        getCustomUtilityClasses(props),
        props.class
    );
}

const atomClasses = computed(() => getAtomClasses(props));
const attrs = computed(() => ({
    ...getCommonAttrs(props),
    ...getInputAttrs(props),
    min: props.min || 0,
    max: props.max || 100,
    step: props.step || 1,
    id: props.id || undefined,
}));

const rangeId = computed(() => props.id || `range-${Math.random().toString(36).substr(2, 9)}`);
</script>

<template>
    <Tooltip :content="props.tooltip" :placement="props.tooltip_placement">
        <div class="form-control w-full">
            <!-- Label top -->
            <InputLabel v-if="label || $slots.labelTop" :for="rangeId" :value="label">
                <template v-if="$slots.labelTop" #default>
                    <slot name="labelTop" />
                </template>
            </InputLabel>
            <div class="relative flex items-center gap-2 w-full">
                <input ref="rangeRef" type="range" v-bind="attrs" v-on="$attrs" :id="rangeId" :class="atomClasses"
                    :value="displayValue" @input="onInput" @blur="onBlur" :aria-invalid="!!errorMessage || validator" />
                <!-- Bouton reset -->
                <Btn v-if="props.useFieldComposable && isFieldModified" class="absolute right-2 top-2 z-20" size="xs"
                    variant="ghost" circle @click="handleReset" :aria-label="'Réinitialiser'">
                    <i class="fa-solid fa-arrow-rotate-left"></i>
                </Btn>
                <slot />
            </div>
            <!-- Label bottom -->
            <InputLabel v-if="labelBottom || $slots.labelBottom" :for="rangeId" :value="labelBottom" class="mt-1">
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
