<script setup>
defineOptions({ inheritAttrs: false }); // Pour que les évéments natifs soient transmis à l'atom

/**
 * Rating Atom (DaisyUI)
 *
 * @description
 * Composant atomique Rating (étoiles, coeurs, etc.) conforme DaisyUI (v5.x) et Atomic Design.
 * - Gère tous les cas d'usage rating (étoiles, coeurs, etc.)
 * - Props DaisyUI : size, color
 * - Prop number : nombre d'étoiles/composants (défaut 5)
 * - Prop mask (string) : nom du mask DaisyUI (défaut 'mask-star')
 * - Prop items (array) : [{ color, mask, checked, ariaLabel }], pour personnaliser chaque composant (prioritaire sur color/mask globaux)
 * - v-model natif (modelValue, number ou string)
 * - Edition réactive avancée via useFieldComposable/field/debounceTime (voir ci-dessous)
 * - Accessibilité renforcée (aria-label, aria-checked)
 * - Slots : #labelTop, #labelBottom, #validator, #help, default
 * - Affiche un bouton reset si modifié (UX moderne)
 *
 * @see https://daisyui.com/components/rating/
 * @version DaisyUI v5.x
 *
 * @note Toutes les classes DaisyUI sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 *
 * @example
 * <Rating v-model="note" :number="5" color="warning" size="lg" />
 * <Rating v-model="note" :items="[
 *   { color: 'warning', mask: 'mask-star', ariaLabel: '1 étoile' },
 *   { color: 'warning', mask: 'mask-star', ariaLabel: '2 étoiles' },
 *   { color: 'error', mask: 'mask-heart', ariaLabel: '3 coeurs', checked: true },
 * ]" />
 *
 * @props {Number|String} modelValue - Valeur sélectionnée (v-model natif)
 * @props {Number} number - Nombre d'étoiles/composants (défaut 5)
 * @props {String} mask - Nom du mask DaisyUI (ex: 'mask-star', 'mask-heart', défaut 'mask-star')
 * @props {String} color - Couleur DaisyUI ('', 'neutral', 'primary', ...)
 * @props {String} size - Taille DaisyUI ('', 'xs', 'sm', 'md', 'lg', 'xl')
 * @props {Array} items - Tableau d'objets pour personnaliser chaque composant : [{ color, mask, checked, ariaLabel }]
 * @props {Boolean} useFieldComposable - Active l'édition réactive (reset, debounce, etc.)
 * @props {Object} field - Objet field externe (optionnel, sinon composable interne)
 * @props {Number} debounceTime - Délai de debounce (ms, défaut 500)
 * @props {String|Object} tooltip, tooltip_placement, id, ariaLabel, role, tabindex - hérités de commonProps
 * @props {String|Object} validator - Message de validation ou slot #validator
 * @props {String} errorMessage - Message d'erreur (optionnel)
 * @props {String} help - Message d'aide (optionnel ou slot #help)
 * @slot labelTop - Label custom au-dessus
 * @slot labelBottom - Label custom en-dessous
 * @slot validator - Message de validation custom
 * @slot help - Message d'aide custom
 * @slot default - Slot pour contenu custom à droite du rating
 *
 * @note Si useFieldComposable=true, la logique d'édition réactive (valeur, debounce, reset, bouton reset, update) est entièrement gérée par le composable useEditableField. Le bouton reset s'affiche automatiquement si la valeur a été modifiée.
 */
import { computed, ref, onMounted, onUnmounted, defineExpose } from 'vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import Validator from '@/Pages/Atoms/data-input/Validator.vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';
import { getInputAttrs } from '@/Utils/atomic-design/atomManager';
import InputLabel from '@/Pages/Atoms/data-input/InputLabel.vue';
import useEditableField from '@/Composables/form/useEditableField';
import Btn from '@/Pages/Atoms/action/Btn.vue';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    modelValue: { type: [Number, String], default: 0 },
    number: { type: Number, default: 5 },
    mask: { type: String, default: 'mask-star' },
    color: {
        type: String,
        default: '',
        validator: v => ['', 'neutral', 'primary', 'secondary', 'accent', 'info', 'success', 'warning', 'error'].includes(v),
    },
    size: {
        type: String,
        default: '',
        validator: v => ['', 'xs', 'sm', 'md', 'lg', 'xl'].includes(v),
    },
    items: { type: Array, default: null },
    useFieldComposable: { type: Boolean, default: false },
    field: { type: Object, default: null },
    debounceTime: { type: Number, default: 500 },
    validator: { type: [Boolean, String, Object], default: true },
    errorMessage: { type: String, default: '' },
    help: { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue']);
const ratingRef = ref(null);

// Edition réactive (optionnel)
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

function onInput(val) {
    if (props.useFieldComposable && editableField.value) {
        editableField.value.onInput({ target: { value: val } });
    } else {
        emit('update:modelValue', val);
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
    if (ratingRef.value && props.autofocus) {
        ratingRef.value.focus();
    }
});
onUnmounted(() => {
    if (editableField.value && editableField.value.debounceTimeout) {
        clearTimeout(editableField.value.debounceTimeout);
    }
});
defineExpose({ focus: () => ratingRef.value && ratingRef.value.focus() });

function getRatingClasses(props) {
    return mergeClasses(
        [
            'rating',
            props.size === 'xs' && 'rating-xs',
            props.size === 'sm' && 'rating-sm',
            props.size === 'md' && 'rating-md',
            props.size === 'lg' && 'rating-lg',
            props.size === 'xl' && 'rating-xl',
        ].filter(Boolean),
        getCustomUtilityClasses(props),
        props.class
    );
}

function getMaskClasses(item, idx) {
    return mergeClasses(
        [
            'mask',
            (item?.mask || props.mask),
            (item?.color || props.color) && `bg-${item?.color || props.color}`,
        ].filter(Boolean)
    );
}

const ratingClasses = computed(() => getRatingClasses(props));
const attrs = computed(() => ({
    ...getCommonAttrs(props),
    ...getInputAttrs(props),
    'aria-checked': isChecked.value,
}));
const ratingId = computed(() => props.id || `rating-${Math.random().toString(36).substr(2, 9)}`);

// Génération dynamique des items (étoiles, coeurs, etc.)
const ratingItems = computed(() => {
    if (props.items && Array.isArray(props.items) && props.items.length > 0) {
        return props.items.map((item, idx) => ({
            ...item,
            value: item.value !== undefined ? item.value : idx + 1,
            checked: item.checked !== undefined ? item.checked : false,
            ariaLabel: item.ariaLabel || `${idx + 1} item`,
        }));
    }
    // Génère un tableau d'items par défaut
    return Array.from({ length: props.number }, (_, idx) => ({
        value: idx + 1,
        mask: props.mask,
        color: props.color,
        checked: false,
        ariaLabel: `${idx + 1} item`,
    }));
});
</script>

<template>
    <Tooltip :content="props.tooltip" :placement="props.tooltip_placement">
        <div class="form-control w-full">
            <!-- Label top -->
            <InputLabel v-if="label || $slots.labelTop" :for="ratingId" :value="label">
                <template v-if="$slots.labelTop" #default>
                    <slot name="labelTop" />
                </template>
            </InputLabel>
            <div class="relative flex items-center gap-2 w-full">
                <div :class="ratingClasses" ref="ratingRef">
                    <template v-for="(item, idx) in ratingItems" :key="idx">
                        <input type="radio" v-bind="attrs" v-on="$attrs" :name="ratingId" :value="item.value"
                            :class="getMaskClasses(item, idx)" :checked="displayValue == item.value || item.checked"
                            :aria-label="item.ariaLabel" :aria-checked="displayValue == item.value"
                            @input="onInput(item.value)" @blur="onBlur" />
                    </template>
                    <!-- Bouton reset -->
                    <Btn v-if="props.useFieldComposable && isFieldModified" class="absolute right-2 top-2 z-20"
                        size="xs" variant="ghost" circle @click="handleReset" :aria-label="'Réinitialiser'">
                        <i class="fa-solid fa-arrow-rotate-left"></i>
                    </Btn>
                    <slot />
                </div>
            </div>
            <!-- Label bottom -->
            <InputLabel v-if="labelBottom || $slots.labelBottom" :for="ratingId" :value="labelBottom" class="mt-1">
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
