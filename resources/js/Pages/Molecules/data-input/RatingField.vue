<script setup>
/**
 * RatingField Molecule (DaisyUI, Atomic Design)
 * 
 * @description
 * Molecule pour système de notation/étoiles complet, utilisant le système unifié useInputField.
 * 
 * @example
 * // Label simple
 * <RatingField label="Note" v-model="rating" :max="5" />
 * 
 * // Avec validation
 * <RatingField 
 *   label="Satisfaction" 
 *   v-model="satisfaction"
 *   :max="5"
 *   :validation="{ state: 'error', message: 'Veuillez donner une note' }"
 * />
 * 
 * // Avec helper et icônes personnalisées
 * <RatingField 
 *   label="Qualité" 
 *   v-model="quality"
 *   :max="10"
 *   :default-mask="'mask-heart'"
 *   helper="Évaluez la qualité du service"
 * />
 */
import { useSlots, useAttrs } from 'vue'
import RatingCore from '@/Pages/Atoms/data-input/RatingCore.vue'
import FieldTemplate from '@/Pages/Molecules/data-input/FieldTemplate.vue'
import useInputField from '@/Composables/form/useInputField'
import { getInputPropsDefinition } from '@/Utils/atomic-design/inputHelper'

// ------------------------------------------
// 🔧 Définition des props et des events
// ------------------------------------------
const props = defineProps(getInputPropsDefinition('rating', 'field'))
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
  
  // Style
  styleProperties,
  containerClasses,
  
  // Helpers
  handleAction
} = useInputField({
  modelValue: props.modelValue,
  type: 'rating',
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
    input-type="rating"
  >
    <!-- Slot core spécifique pour RatingCore -->
    <template #core="{ inputAttrs, listeners, inputRef }">
      <RatingCore
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
// Styles spécifiques pour RatingField
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
    // Boutons d'action dans les ratings
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

// Styles pour l'affichage de la valeur
.text-sm {
    // Affichage de la valeur actuelle
    transition: all 0.2s ease-in-out;
    
    .font-medium {
        font-weight: 500;
    }
    
    .text-error {
        color: var(--color-error, #ef4444);
    }
    
    .text-warning {
        color: var(--color-warning, #f59e0b);
    }
    
    .text-info {
        color: var(--color-info, #06b6d4);
    }
    
    .text-success {
        color: var(--color-success, #10b981);
    }
    
    .font-bold {
        font-weight: 700;
    }
}

// Styles pour les badges de statut
.badge {
    // Badge de statut
    transition: all 0.2s ease-in-out;
    
    &.badge-error {
        background-color: var(--color-error, #ef4444);
        color: white;
    }
    
    &.badge-warning {
        background-color: var(--color-warning, #f59e0b);
        color: white;
    }
    
    &.badge-info {
        background-color: var(--color-info, #06b6d4);
        color: white;
    }
    
    &.badge-success {
        background-color: var(--color-success, #10b981);
        color: white;
    }
}

// Styles pour la barre de progression
.bg-gray-200 {
    // Barre de progression de base
    transition: all 0.2s ease-in-out;
    
    .bg-error {
        background-color: var(--color-error, #ef4444);
    }
    
    .bg-warning {
        background-color: var(--color-warning, #f59e0b);
    }
    
    .bg-info {
        background-color: var(--color-info, #06b6d4);
    }
    
    .bg-success {
        background-color: var(--color-success, #10b981);
    }
}

// Styles pour les icônes de valeur
.fa-star {
    color: var(--color-primary, #3b82f6);
    
    &.text-error {
        color: var(--color-error, #ef4444);
    }
    
    &.text-warning {
        color: var(--color-warning, #f59e0b);
    }
    
    &.text-info {
        color: var(--color-info, #06b6d4);
    }
    
    &.text-success {
        color: var(--color-success, #10b981);
    }
}
</style> 