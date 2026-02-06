<script setup>
defineOptions({ inheritAttrs: false });

/**
 * ColorCore Atom (DaisyUI, Atomic Design)
 *
 * @description
 * Atom de base pour le sélecteur de couleur : input HTML natif type="color" avec styles DaisyUI.
 * Aucune dépendance externe (vue-color-kit supprimée). Utilise getInputStyle pour les classes.
 *
 * @example
 * <ColorCore v-model="color" />
 * <ColorCore v-model="color" color="primary" size="lg" />
 *
 * @props {String} modelValue - v-model (couleur hex, ex. #3b82f6)
 * @props {Boolean} disabled, readonly, required
 * @props {String} color, size, variant
 * @props {String|Object} inputStyle - Style d'input (string ou objet avec variant, size, color, animation)
 */
import { computed, useAttrs } from 'vue';
import { getInputStyle } from '@/Composables/form/useInputStyle';
import useInputProps from '@/Composables/form/useInputProps';
import { getInputPropsDefinition } from '@/Utils/atomic-design/inputHelper';
import { mergeClasses } from '@/Utils/atomic-design/uiHelper';

const props = defineProps(getInputPropsDefinition('color', 'core'));
const emit = defineEmits(['update:modelValue']);
const $attrs = useAttrs();

const { inputAttrs, listeners } = useInputProps(props, $attrs, emit, 'color', 'core');

const atomClasses = computed(() =>
  mergeClasses(
    getInputStyle('color', {
      variant: props.variant,
      color: props.color,
      size: props.size,
      animation: props.animation,
      ...(typeof props.inputStyle === 'object' && props.inputStyle !== null ? props.inputStyle : {}),
      ...(typeof props.inputStyle === 'string' ? { variant: props.inputStyle } : {})
    }, false)
  )
);

/** Valeur affichée par l'input (toujours hex valide pour le natif). */
const displayValue = computed(() => {
  const v = props.modelValue;
  if (v && typeof v === 'string' && /^#([0-9A-Fa-f]{3}){1,2}$/.test(v.trim())) {
    return v.trim();
  }
  return '#000000';
});

function onInput(e) {
  emit('update:modelValue', e.target.value);
}
</script>

<template>
  <input
    type="color"
    v-bind="inputAttrs"
    :class="['input color-core-input', atomClasses]"
    :value="displayValue"
    v-on="listeners"
    @input="onInput"
  />
</template>

<style scoped lang="scss">
// Carré compact, sans contour (contour désactivé pour ce type d’input)
input[type="color"].color-core-input {
  width: 2.5rem;
  height: 2.5rem;
  min-width: 2.5rem;
  min-height: 2.5rem;
  padding: 0;
  border: none !important;
  border-radius: 0.375rem;
  cursor: pointer;
  outline: none;
  box-shadow: none;
  transition: opacity 0.2s ease-in-out;
  --color: var(--color-primary-500);

  &:focus {
    outline: none;
    box-shadow: none;
  }

  &:hover {
    opacity: 0.9;
  }

  &:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }
}
</style>
