<script setup>
defineOptions({ inheritAttrs: false }); // Pour la transmission des événements natifs

/**
 * Fieldset Molecule (DaisyUI + Custom Utility)
 *
 * @description
 * Molécule Fieldset conforme DaisyUI (v5.x) et Atomic Design.
 * - Rend un <fieldset> stylé DaisyUI
 * - Prop legend (string) ou slot #legend pour le titre
 * - Slot par défaut : contenu du fieldset (inputs, etc.)
 * - Supporte les utilitaires custom (shadow, backdrop, opacity, etc.)
 * - Accessibilité renforcée (role, aria, etc.)
 *
 * @see https://daisyui.com/components/fieldset/
 * @version DaisyUI v5.x
 *
 * @example
 * <Fieldset legend="Informations de base" shadow="md" class="mb-6">
 *   <InputField label="Nom" v-model="form.name" />
 *   <InputField label="Email" v-model="form.email" />
 * </Fieldset>
 *
 * @props {String} legend - Titre du fieldset (optionnel, sinon slot #legend)
 * @props {String} shadow, backdrop, opacity - utilitaires custom
 * @props {String|Object} id, ariaLabel, role, tabindex - hérités de commonProps
 * @slot legend - Slot pour le titre du fieldset (prioritaire sur prop legend)
 * @slot default - Contenu du fieldset (inputs, etc.)
 */
import { computed } from 'vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    legend: { type: String, default: '' },
    class: { type: String, default: '' },
});

const fieldsetClasses = computed(() =>
    mergeClasses(
        [
            'fieldset',
            'bg-base-200',
            'border-base-300',
            'rounded-box',
            'w-full',
            'border',
            'p-4',
        ],
        getCustomUtilityClasses(props),
        props.class
    )
);
const legendClasses = computed(() =>
    mergeClasses([
        'fieldset-legend',
        'font-semibold',
        'mb-2',
    ])
);
const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <fieldset :class="fieldsetClasses" v-bind="attrs" v-on="$attrs">
        <legend v-if="$slots.legend || legend" :class="legendClasses">
            <slot name="legend">{{ legend }}</slot>
        </legend>
        <slot />
    </fieldset>
</template>

<style scoped>
/* Optionnel : styles additionnels si besoin */
</style>
