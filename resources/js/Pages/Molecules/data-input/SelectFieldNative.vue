<script setup>
/**
 * SelectFieldNative Molecule (DaisyUI, Atomic Design)
 *
 * @description
 * Implémentation historique basée sur un `<select>` natif.
 * Conservée pour les cas `multiple` (et compat) car le rendu OS est parfois préférable.
 */
import { useAttrs } from 'vue'
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
  actionsToDisplay,
  inputRef,
  focus,
  
  // Attributs et événements
  inputAttrs,
  listeners,
  
  // Labels
  labelConfig,
  
  // Validation
  validationState,
  validationMessage,
  validate,
  resetValidation,
  
  // Méthodes de contrôle de validation
  enableValidation,
  disableValidation,
  
  // Style
  styleProperties,
  containerClasses,
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
    <template #core="{ inputAttrs: coreInputAttrs, listeners: coreListeners }">
      <SelectCore
        v-bind="coreInputAttrs"
        v-on="coreListeners"
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
            :key="(option?.value ?? option)"
            :value="(option?.value ?? option)"
            :disabled="option?.disabled"
          >
            {{ option?.label ?? option }}
          </option>
        </slot>
      </SelectCore>
    </template>
    
    <!-- Slots personnalisés -->
    <template v-if="$slots.overStart" #overStart>
      <slot name="overStart" />
    </template>
    <template v-if="$slots.overEnd" #overEnd>
      <slot name="overEnd" />
    </template>
    <template #helper>
      <slot name="helper" />
    </template>
  </FieldTemplate>
</template>

