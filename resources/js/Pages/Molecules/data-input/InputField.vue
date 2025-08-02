<script setup>
/**
 * InputField Molecule (DaisyUI, Atomic Design)
 *
 * @description
 * Molecule pour champ de saisie complet, orchestrant InputCore et InputLabel.
 * - API simplifiée : prop `label` peut être une string (floating par défaut) ou un objet avec positions
 * - 7 positions de labels : top, bottom, start, end, inStart, inEnd, floating
 * - Slots pour chaque position pour du contenu complexe
 * - Gestion automatique des combinaisons interdites (floating vs inStart/inEnd)
 * - Styles DaisyUI, accessibilité, édition réactive, toggle password, etc.
 * - Support des utilitaires custom (shadow, backdrop, opacity, rounded)
 * - Validation intégrée avec états visuels et messages d'erreur
 * - Intégration automatique avec le système de notifications
 * - Support de la prop `style` (objet) et `variant` (string)
 *
 * @see https://daisyui.com/components/input/
 * @version DaisyUI v5.x
 *
 * @example
 * // Label simple (floating par défaut)
 * <InputField label="Nom" v-model="name" />
 * 
 * // Label simple avec position par défaut différente
 * <InputField label="Nom" v-model="name" defaultLabelPosition="top" />
 * 
 * // Label avec positions spécifiques
 * <InputField :label="{ top: 'Nom complet', inStart: 'M.' }" v-model="name" />
 * 
 * // Label complexe avec slots
 * <InputField :label="{ top: 'Email' }" v-model="email">
 *   <template #labelTop>
 *     <span class="flex items-center gap-2">
 *       <i class="fa-solid fa-envelope"></i>
 *       Email professionnel
 *     </span>
 *   </template>
 * </InputField>
 * 
 * // Avec actions automatiques (toggle password dans overStart, reset dans overEnd si useFieldComposable)
 * <InputField label="Mot de passe" v-model="password" type="password" useFieldComposable />
 * 
 * // Avec actions personnalisées dans les slots overStart/overEnd
 * <InputField label="Recherche" v-model="search">
 *   <template #overStart>
 *     <Btn variant="ghost" size="xs">
 *       <i class="fa-solid fa-search"></i>
 *     </Btn>
 *   </template>
 *   <template #overEnd>
 *     <Btn variant="ghost" size="xs" @click="clearSearch">
 *       <i class="fa-solid fa-times"></i>
 *     </Btn>
 *   </template>
 * </InputField>
 *
 * // Validation locale uniquement
 * <InputField 
 *   label="Email" 
 *   v-model="email"
 *   :validation="{ state: 'error', message: 'Email invalide' }"
 * />
 *
 * // Validation avec notification
 * <InputField 
 *   label="Email" 
 *   v-model="email"
 *   :validation="{ 
 *     state: 'success', 
 *     message: 'Email valide !',
 *     showNotification: true 
 *   }"
 * />
 *
 * // Avec objet style
 * <InputField 
 *   label="Nom" 
 *   v-model="name"
 *   :inputStyle="{ variant: 'glass', color: 'primary', size: 'md', animation: 'pulse' }"
 * />
 *
 * @props {String|Object} label - Label simple (string) ou objet avec positions
 * @props {String} defaultLabelPosition - Position par défaut pour les strings ('floating', 'top', 'bottom', 'start', 'end', 'inStart', 'inEnd')
 * @props {Object|String|Boolean} validation - Configuration de validation (nouvelle API)
 * @props {String} helper, errorMessage
 * @props {String} color, size, variant
 * @props {String|Object} inputStyle - Style d'input (string ou objet avec variant, size, color, animation)
 * @props {String|Boolean} animation - Animation Tailwind ou booléen
 * @props {Boolean} useFieldComposable, showPasswordToggle
 * @props {String} shadow, backdrop, opacity, rounded - utilitaires custom
 * @slot labelTop, labelBottom, labelStart, labelEnd, labelInStart, labelInEnd, labelFloating - Slots pour chaque position de label
 * @slot overStart, overEnd - Slots pour éléments positionnés en absolute (toggle, reset, etc.)
 * @slot helper, validator - Slots pour contenu d'aide et validation
 */
/**
 * [MIGRATION 2024-06] Ce composant utilise désormais inputHelper.js pour la gestion factorisée des props/attrs input (voir /Utils/atomic-design/inputHelper.js)
 */

// ------------------------------------------
// 🧩 Importation des dépendances
// ------------------------------------------
import { useSlots, useAttrs } from 'vue'
import InputCore from '@/Pages/Atoms/data-input/InputCore.vue'

import useInputField from '@/Composables/form/useInputField'
import { getInputPropsDefinition } from '@/Utils/atomic-design/inputHelper'
import FieldTemplate from '@/Pages/Molecules/data-input/FieldTemplate.vue'

// ------------------------------------------
// 🔧 Définition des props et des events
// ------------------------------------------
const props = defineProps(getInputPropsDefinition('input', 'field'))
const emit = defineEmits(['update:modelValue'])
const slots = useSlots()
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
  type: 'input',
  mode: 'field',
  props,
  attrs: $attrs,
  emit
})
</script>

<template>
  <FieldTemplate
    :container-classes="containerClasses" :label-config="labelConfig" :input-attrs="inputAttrs" :listeners="listeners" :input-ref="inputRef"
    :actions-to-display="actionsToDisplay" :style-properties="styleProperties"
    :validation-state="validationState" :validation-message="validationMessage"
    :helper="props.helper"
  >
    <!-- Slot core spécifique pour InputCore -->
    <template #core="{ inputAttrs, listeners, inputRef }">
      <InputCore
        v-bind="inputAttrs"
        v-on="listeners"
        ref="inputRef"
      >
        <!-- 🔤 Labels inline start/end -->
        <template v-if="$slots.labelInStart" #labelInStart>
          <slot name="labelInStart" />
        </template>
        <template v-if="$slots.labelInEnd" #labelInEnd>
          <slot name="labelInEnd" />
        </template>
        <!-- 💬 Label flottant -->
        <template v-if="labelConfig.floating || $slots.labelFloating" #floatingLabel>
          <slot name="labelFloating">{{ labelConfig.floating }}</slot>
        </template>
      </InputCore>
    </template>
    
    <!-- Slots personnalisés -->
    <template #helper>
      <slot name="helper" />
    </template>
  </FieldTemplate>
</template>


<style scoped lang="scss">
// Styles spécifiques pour InputField
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
    // Boutons d'action dans les inputs
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
</style> 