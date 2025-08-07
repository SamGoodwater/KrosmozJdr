<script setup>
/**
 * ToggleField Molecule (DaisyUI, Atomic Design)
 * 
 * @description
 * Molecule pour toggle/switch complet, utilisant le système unifié useInputField.
 * 
 * @example
 * // Label simple
 * <ToggleField label="Notifications" v-model="notifications" />
 * 
 * // Avec validation
 * <ToggleField 
 *   label="Notifications" 
 *   v-model="notifications"
 *   :validation="{ state: 'error', message: 'Veuillez activer les notifications' }"
 * />
 * 
 * // Avec helper
 * <ToggleField 
 *   label="Mode sombre" 
 *   v-model="darkMode"
 *   helper="Activez le thème sombre pour une meilleure expérience"
 * />
 */
import { useSlots, useAttrs } from 'vue'
import ToggleCore from '@/Pages/Atoms/data-input/ToggleCore.vue'
import FieldTemplate from '@/Pages/Molecules/data-input/FieldTemplate.vue'
import useInputField from '@/Composables/form/useInputField'
import { getInputPropsDefinition } from '@/Utils/atomic-design/inputHelper'

// ------------------------------------------
// 🔧 Définition des props et des events
// ------------------------------------------
const props = defineProps(getInputPropsDefinition('toggle', 'field'))
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
  type: 'toggle',
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
    input-type="toggle"
  >
    <!-- Slot core spécifique pour ToggleCore -->
    <template #core="{ inputAttrs, listeners, inputRef }">
      <ToggleCore
        v-bind="inputAttrs"
        v-on="listeners"
        ref="inputRef"
      />
    </template>
    
    <!-- Slots personnalisés -->
    <template #helper>
      <slot name="helper" />
    </template>
  </FieldTemplate>
</template>

<style scoped lang="scss">
// Styles spécifiques pour ToggleField
// Utilisation maximale de Tailwind/DaisyUI, CSS custom minimal

// Styles pour les labels
.label {
    transition: all 0.2s ease-in-out;
    font-weight: 500;
    
    // Tailles
    &.label-xs { font-size: 0.75rem; }
    &.label-sm { font-size: 0.875rem; }
    &.label-md { font-size: 1rem; }
    &.label-lg { font-size: 1.125rem; }
    &.label-xl { font-size: 1.25rem; }
    
    // Couleurs
    &.label-primary { color: var(--color-primary, #3b82f6); }
    &.label-secondary { color: var(--color-secondary, #8b5cf6); }
    &.label-accent { color: var(--color-accent, #f59e0b); }
    &.label-info { color: var(--color-info, #06b6d4); }
    &.label-success { color: var(--color-success, #10b981); }
    &.label-warning { color: var(--color-warning, #f59e0b); }
    &.label-error { color: var(--color-error, #ef4444); }
    &.label-neutral { color: var(--color-neutral, #6b7280); }
    
    // Effet hover subtil
    &:hover {
        opacity: 0.8;
    }
}

// Styles pour les helpers
.helper {
    transition: all 0.2s ease-in-out;
    font-size: 0.875rem;
    opacity: 0.8;
    
    // Tailles
    &.helper-xs { font-size: 0.75rem; }
    &.helper-sm { font-size: 0.875rem; }
    &.helper-md { font-size: 1rem; }
    &.helper-lg { font-size: 1.125rem; }
    &.helper-xl { font-size: 1.25rem; }
    
    // Couleurs
    &.helper-primary { color: var(--color-primary, #3b82f6); }
    &.helper-secondary { color: var(--color-secondary, #8b5cf6); }
    &.helper-accent { color: var(--color-accent, #f59e0b); }
    &.helper-info { color: var(--color-info, #06b6d4); }
    &.helper-success { color: var(--color-success, #10b981); }
    &.helper-warning { color: var(--color-warning, #f59e0b); }
    &.helper-error { color: var(--color-error, #ef4444); }
    &.helper-neutral { color: var(--color-neutral, #6b7280); }
}

// Styles pour les actions contextuelles
.btn {
    // Boutons d'action dans les toggles
    &.btn-link {
        transition: all 0.2s ease-in-out;
        
        &:hover {
            transform: scale(1.1);
        }
    }
}

// Styles pour les slots overStart/overEnd
.absolute {
    // Positionnement des éléments absolus
    z-index: 10;
    
    .btn {
        // Boutons dans les slots over
        transition: all 0.2s ease-in-out;
        
        &:hover {
            transform: scale(1.05);
        }
    }
}

// Styles pour les validations
.validator {
    // Messages de validation
    transition: all 0.2s ease-in-out;
    
    &.error {
        color: var(--color-error, #ef4444);
    }
    
    &.success {
        color: var(--color-success, #10b981);
    }
    
    &.warning {
        color: var(--color-warning, #f59e0b);
    }
    
    &.info {
        color: var(--color-info, #06b6d4);
    }
}

// Styles pour l'affichage de l'état
.text-sm {
    // Affichage de l'état actuel
    transition: all 0.2s ease-in-out;
    
    .font-medium {
        font-weight: 500;
    }
    
    .text-success {
        color: var(--color-success, #10b981);
    }
    
    .text-neutral {
        color: var(--color-neutral, #6b7280);
    }
    
    .badge {
        // Badge de statut
        transition: all 0.2s ease-in-out;
        
        &.badge-success {
            background-color: var(--color-success, #10b981);
            color: white;
        }
        
        &.badge-neutral {
            background-color: var(--color-neutral, #6b7280);
            color: white;
        }
    }
}

// Styles pour les icônes d'état
.fa-toggle-on {
    color: var(--color-success, #10b981);
}

.fa-toggle-off {
    color: var(--color-neutral, #6b7280);
}
</style> 