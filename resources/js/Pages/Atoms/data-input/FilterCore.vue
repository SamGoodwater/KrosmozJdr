<script setup>
/**
 * Filter Atom (DaisyUI)
 *
 * @description
 * Composant atomique Filter conforme DaisyUI (v5.x) et Atomic Design.
 * - Rend un <form class="filter"> ou <div class="filter"> stylé DaisyUI selon la prop 'form'
 * - Slot par défaut : radios (groupe de boutons radio DaisyUI)
 * - Props utilitaires custom : shadow, backdrop, opacity (via getCustomUtilityProps)
 * - Props d'accessibilité et HTML natif héritées de commonProps
 * - Toutes les classes DaisyUI sont explicites, pas de concaténation dynamique non couverte par Tailwind
 *
 * @see https://daisyui.com/components/filter/
 * @version DaisyUI v5.x (5.0.43)
 *
 * @example
 * <Filter :form="true">
 *   <input class="btn btn-square" type="reset" value="×" />
 *   <input class="btn" type="radio" name="frameworks" aria-label="Svelte" />
 *   <input class="btn" type="radio" name="frameworks" aria-label="Vue" />
 *   <input class="btn" type="radio" name="frameworks" aria-label="React" />
 * </Filter>
 *
 * <Filter>
 *   <input class="btn filter-reset" type="radio" name="metaframeworks" aria-label="All" />
 *   <input class="btn" type="radio" name="metaframeworks" aria-label="Sveltekit" />
 *   <input class="btn" type="radio" name="metaframeworks" aria-label="Nuxt" />
 *   <input class="btn" type="radio" name="metaframeworks" aria-label="Next.js" />
 * </Filter>
 *
 * @props {Boolean} form - Utilise <form> si true, sinon <div> (défaut false)
 * @props {String} shadow, backdrop, opacity - utilitaires custom
 * @props {String|Object} id, ariaLabel, role, tabindex, class - hérités de commonProps
 * @slot default - Radios DaisyUI (input type="radio" ou reset)
 *
 * @note Toutes les classes DaisyUI et utilitaires custom sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 * @note Accessibilité : aria-label, role, tabindex, etc. transmis.
 */
 import { computed, ref, watch, onMounted, useSlots } from 'vue';
import Validator from '@/Pages/Atoms/data-input/Validator.vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';
import { getInputProps, getInputAttrs } from '@/Utils/atomic-design/inputHelper';

const props = defineProps({
    ...getCommonProps(),
    ...getInputProps('filter', 'core'),
    ...getCustomUtilityProps(),
});

const emit = defineEmits(['update:modelValue']);
const filterRef = ref(null);

const hasValidationState = computed(() => hasValidation(props, useSlots()));

const atomClasses = computed(() =>
    mergeClasses(
        [
            'filter',
            props.color === 'neutral' && 'filter-neutral',
            props.color === 'primary' && 'filter-primary',
            props.color === 'secondary' && 'filter-secondary',
            props.color === 'accent' && 'filter-accent',
            props.color === 'info' && 'filter-info',
            props.color === 'success' && 'filter-success',
            props.color === 'warning' && 'filter-warning',
            props.color === 'error' && 'filter-error',
            props.size === 'xs' && 'filter-xs',
            props.size === 'sm' && 'filter-sm',
            props.size === 'md' && 'filter-md',
            props.size === 'lg' && 'filter-lg',
            props.size === 'xl' && 'filter-xl',
            hasValidationState.value && 'filter-error',
        ].filter(Boolean),
        getCustomUtilityClasses(props),
        props.class
    )
);

const filterId = computed(() => props.id || `filter-${Math.random().toString(36).substr(2, 9)}`);

const attrs = computed(() => ({
    ...getCommonAttrs(props),
    ...getInputAttrs('filter', 'core'),
}));

function onInput(e) {
    emit('update:modelValue', e.target.value);
}

onMounted(() => {
    if (filterRef.value && props.autofocus) {
        filterRef.value.focus();
    }
});

defineExpose({ focus: () => filterRef.value && filterRef.value.focus() });
</script>

<template>
    <div class="form-control w-full">
        <InputLabel v-if="props.label || $slots.labelTop" :for="filterId" :value="props.label">
            <template v-if="$slots.labelTop" #default>
                <slot name="labelTop" />
            </template>
        </InputLabel>
        <div class="flex items-center gap-2">
            <component :is="props.as" :class="atomClasses" v-bind="attrs" v-on="$attrs">
                <slot />
            </component>
        </div>
        <InputLabel v-if="props.labelBottom || $slots.labelBottom" :for="filterId" :value="props.labelBottom" class="mt-1">
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
