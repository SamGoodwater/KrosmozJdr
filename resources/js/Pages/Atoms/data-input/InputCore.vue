<script setup>
defineOptions({ inheritAttrs: false });

/**
 * InputCore Atom (DaisyUI, Atomic Design)
 *
 * @description
 * Atom de base pour les champs input, stylé DaisyUI, sans gestion de label ni de layout.
 * - Props : type, v-model, placeholder, disabled, readonly, color, size, style, etc.
 * - Accessibilité : id, ariaLabel, role, tabindex, aria-invalid, etc.
 * - Utilise getInputClasses pour les classes DaisyUI/Tailwind
 * - Slot par défaut : input natif
 * - Gère tous les types d'input : text, email, password, number, url, tel, search, date, etc.
 * - Utilise le système de labels inline de DaisyUI pour éviter les divs englobantes
 * - Support des utilitaires custom (shadow, backdrop, opacity, rounded)
 *
 * @see https://daisyui.com/components/input/
 * @version DaisyUI v5.x
 *
 * @example
 * <InputCore type="text" v-model="name" placeholder="Nom" />
 * <InputCore type="password" v-model="password" placeholder="Mot de passe" />
 * <InputCore type="email" v-model="email" color="primary" size="lg" />
 * <InputCore type="text" v-model="search" shadow="lg" rounded="full" />
 *
 * @props {String} type - Type d'input (text, email, password, number, url, tel, search, date, etc.)
 * @props {String} modelValue - v-model
 * @props {String} placeholder
 * @props {Boolean} disabled, readonly
 * @props {String} color, size, style, variant
 * @props {String} id, ariaLabel, role, tabindex
 * @props {Boolean|String} aria-invalid - État de validation pour l'accessibilité
 * @props {String} labelStart - Label inline à gauche (dans la balise label)
 * @props {String} labelEnd - Label inline à droite (dans la balise label)
 * @props {Boolean} labelFloating - Active le mode floating label
 * @props {String} shadow, backdrop, opacity, rounded - utilitaires custom
 * @slot labelStart - Slot pour label inline à gauche
 * @slot labelEnd - Slot pour label inline à droite
 * @slot floatingLabel - Slot pour label flottant
 * @slot default - input natif (optionnel)
 */
/**
 * [MIGRATION 2024-06] Ce composant utilise désormais inputHelper.js pour la gestion factorisée des props/attrs input (voir /Utils/atomic-design/inputHelper.js)
 */
import { ref, computed, useAttrs } from 'vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';
import { getInputProps, getInputAttrs } from '@/Utils/atomic-design/inputHelper';
import { getInputClasses } from '@/Composables/form/useInputStyle';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    ...getInputProps('input', 'core'),
});

const emit = defineEmits(['update:modelValue']);
const inputRef = ref(null);
const $attrs = useAttrs();

const atomClasses = computed(() => {
    const styleValue = typeof props.style === 'string' ? props.style : 'glass';
    return mergeClasses(
        getInputClasses({
            style: styleValue,
            color: props.color,
            size: props.size,
            variant: props.variant,
            error: false,
        }),
        getCustomUtilityClasses(props)
    );
});

const shouldShowInlineLabels = computed(() => {
    if (props.labelFloating) {
        return false;
    }
    return props.labelEnd || props.labelStart;
});

const labelClasses = computed(() => {   
    const baseClasses = atomClasses.value;
    if (props.labelFloating) {
        return mergeClasses([baseClasses, 'floating-label']);
    }
    return baseClasses;
});

const attrs = computed(() => ({
    ...getCommonAttrs(props),
    ...getInputAttrs('input', 'core'),
    type: props.type,
    'aria-label': typeof props.ariaLabel === 'string' ? props.ariaLabel : undefined,
    'aria-invalid': props['aria-invalid'] !== undefined ? props['aria-invalid'] : undefined,
    readonly: props.readonly,
    autofocus: props.autofocus,
    ref: inputRef,
    value: props.modelValue,
}));

function onInput(e) {
    emit('update:modelValue', e.target.value);
}

</script>

<template>
    <!-- Structure pour labels inline (labelStart/labelEnd) -->
    <label :class="labelClasses" v-if="shouldShowInlineLabels">
        <span v-if="labelStart || $slots.labelStart" class="label-text">
            <slot name="labelStart">{{ labelStart }}</slot>
        </span>
        <input
            v-bind="attrs"
            v-on="$attrs"
            @input="onInput"
        />
        <span v-if="labelEnd || $slots.labelEnd" class="label-text">
            <slot name="labelEnd">{{ labelEnd }}</slot>
        </span>
    </label>
    
    <!-- Structure pour floating label -->
    <label v-else-if="labelFloating" :class="labelClasses">
        <input
            v-bind="attrs"
            v-on="$attrs"
            @input="onInput"
        />
        <span class="label-text">
            <slot name="floatingLabel">{{ props.placeholder || 'Label' }}</slot>
        </span>
    </label>
    
    <!-- Input simple sans label -->
    <input
        v-else
        v-bind="attrs"
        v-on="$attrs"
        :class="atomClasses"
        @input="onInput"
    />
</template>

<style scoped></style> 