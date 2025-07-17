<script setup>
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
 * - Slots : #labelTop, #labelBottom, #validator, #helper, default
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
 * @props {String|Object} id, ariaLabel, role, tabindex - hérités de commonProps
 * @props {String|Object} validator - Message de validation ou slot #validator
 * @props {String} errorMessage - Message d'erreur (optionnel)
 * @props {String} helper - Message d'aide (optionnel ou slot #helper)
 * @slot labelTop - Label custom au-dessus
 * @slot labelBottom - Label custom en-dessous
 * @slot validator - Message de validation custom
 * @slot helper - Message d'aide custom
 * @slot default - Slot pour contenu custom à droite du rating
 *
 * @note Si useFieldComposable=true, la logique d'édition réactive (valeur, debounce, reset, bouton reset, update) est entièrement gérée par le composable useEditableField. Le bouton reset s'affiche automatiquement si la valeur a été modifiée.
 */
import { computed, ref, watch, onMounted, onUnmounted, useSlots } from 'vue';
import Validator from '@/Pages/Atoms/data-input/Validator.vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';
import { getInputProps, getInputAttrs } from '@/Utils/atomic-design/inputHelper';
import InputLabel from '@/Pages/Atoms/data-input/InputLabel.vue';
import useEditableField from '@/Composables/form/useEditableField';
import Btn from '@/Pages/Atoms/action/Btn.vue';

const props = defineProps({
    ...getCommonProps(),
    ...getInputProps('rating', 'core'),
    ...getCustomUtilityProps(),
});

const emit = defineEmits(['update:modelValue']);
const ratingRef = ref(null);

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
    if (ratingRef.value && props.autofocus) {
        ratingRef.value.focus();
    }
});
onUnmounted(() => {
    if (editableField.value && editableField.value.debounceTimeout) {
        clearTimeout(editableField.value.debounceTimeout);
    }
});
defineExpose({ 
    focus: () => ratingRef.value && ratingRef.value.focus(),
    isFieldModified,
    handleReset,
});

const atomClasses = computed(() =>
    mergeClasses(
        [
            'rating',
            props.color === 'neutral' && 'rating-neutral',
            props.color === 'primary' && 'rating-primary',
            props.color === 'secondary' && 'rating-secondary',
            props.color === 'accent' && 'rating-accent',
            props.color === 'info' && 'rating-info',
            props.color === 'success' && 'rating-success',
            props.color === 'warning' && 'rating-warning',
            props.color === 'error' && 'rating-error',
            props.size === 'xs' && 'rating-xs',
            props.size === 'sm' && 'rating-sm',
            props.size === 'md' && 'rating-md',
            props.size === 'lg' && 'rating-lg',
            props.size === 'xl' && 'rating-xl',
            hasValidationState.value && 'rating-error',
        ].filter(Boolean),
        getCustomUtilityClasses(props),
        props.class
    )
);

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
const ratingId = computed(() => props.id || `rating-${Math.random().toString(36).substr(2, 9)}`);

const attrs = computed(() => ({
    ...getCommonAttrs(props),
    ...getInputAttrs('rating', 'core'),
}));

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
    <div class="form-control w-full">
        <!-- Label top -->
        <InputLabel v-if="props.label || $slots.labelTop" :for="ratingId" :value="props.label">
            <template v-if="$slots.labelTop" #default>
                <slot name="labelTop" />
            </template>
        </InputLabel>
        <div class="relative flex items-center gap-2 w-full">
            <div :class="atomClasses" ref="ratingRef">
                <template v-for="(item, idx) in ratingItems" :key="idx">
                    <input type="radio" v-bind="attrs" :name="ratingId" :value="item.value"
                        :class="getMaskClasses(item, idx)" :checked="displayValue == item.value || item.checked"
                        :aria-label="item.ariaLabel" :aria-checked="displayValue == item.value" :aria-invalid="hasValidationState"
                        @input="onInput(item.value)" @blur="onBlur" v-on="$attrs" />
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
        <InputLabel v-if="props.labelBottom || $slots.labelBottom" :for="ratingId" :value="props.labelBottom" class="mt-1">
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
