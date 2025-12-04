<script setup>
/**
 * SelectField Molecule (DaisyUI, Atomic Design)
 * 
 * @description
 * Molecule pour champ de sélection complet, utilisant le système unifié useInputField.
 * 
 * @example
 * // Label simple (floating par défaut)
 * <SelectField label="Pays" v-model="country" :options="countries" />
 * 
 * // Avec validation
 * <SelectField 
 *   label="Rôle" 
 *   v-model="role"
 *   :validation="{ state: 'error', message: 'Rôle requis' }"
 * />
 * 
 * // Avec options
 * <SelectField 
 *   label="Catégorie" 
 *   v-model="category" 
 *   :options="[
 *     { value: 'tech', label: 'Technologie' },
 *     { value: 'design', label: 'Design' }
 *   ]"
 * />
 */
import { useSlots, useAttrs } from 'vue'
import SelectCore from '@/Pages/Atoms/data-input/SelectCore.vue'
import FieldTemplate from '@/Pages/Molecules/data-input/FieldTemplate.vue'
import useInputField from '@/Composables/form/useInputField'
import { getInputPropsDefinition } from '@/Utils/atomic-design/inputHelper'

// ------------------------------------------
// 🔧 Définition des props et des events
// ------------------------------------------
const props = defineProps(getInputPropsDefinition('select', 'field'))
const emit = defineEmits(['update:modelValue'])
const $attrs = useAttrs()

// ------------------------------------------
// 🎯 Utilisation du composable unifié
// ------------------------------------------
const {
  // V-model et actions
  currentValue,
  actionsToDisplay,
  inputRef,
  focus,
  isModified,
  isReadonly,
  showPassword,
  
  // Attributs et événements
  inputAttrs,
  listeners,
  
  // Labels
  labelConfig,
  
  // Validation
  validationState,
  validationMessage,
  hasInteracted,
  validate,
  setInteracted,
  resetValidation,
  isValid,
  hasError,
  hasWarning,
  hasSuccess,
  
  // Méthodes de contrôle de validation
  enableValidation,
  disableValidation,
  
  // Style
  styleProperties,
  containerClasses,
  
  // Helpers
  handleAction
} = useInputField({
  modelValue: props.modelValue,
  type: 'select',
  mode: 'field',
  props,
  attrs: $attrs,
  emit
})

// Exposer les méthodes pour contrôle externe
defineExpose({
  enableValidation,
  disableValidation,
  resetValidation,
  focus,
  validate
})
</script>

<template>
  <FieldTemplate
    :container-classes="containerClasses"
    :label-config="labelConfig"
    :input-attrs="inputAttrs"
    :listeners="listeners"
    :input-ref="inputRef"
    :actions-to-display="actionsToDisplay"
    :style-properties="styleProperties"
    :validation-state="validationState"
    :validation-message="validationMessage"
    :helper="props.helper"
  >
    <!-- Slot core spécifique pour SelectCore -->
    <template #core="{ inputAttrs, listeners, inputRef }">
      <SelectCore
        v-bind="inputAttrs"
        v-on="listeners"
        :options="props.options"
        ref="inputRef"
      >
        <!-- Options par défaut -->
        <slot>
          <option v-if="props.placeholder" value="" disabled selected>
            {{ props.placeholder }}
          </option>
          <option
            v-for="option in props.options"
            :key="option.value || option"
            :value="option.value || option"
            :disabled="option.disabled"
          >
            {{ option.label || option }}
          </option>
        </slot>
      </SelectCore>
    </template>
    
    <!-- Slots personnalisés -->
    <template #helper>
      <slot name="helper" />
    </template>
  </FieldTemplate>
</template>