/**
 * useInputField — Composable unifié pour tous les composants Field
 * 
 * Ce composable unifie la gestion de :
 * - v-model (via useInputActions)
 * - Attributs HTML (via useInputProps)
 * - Validation
 * - Actions contextuelles
 * - Labels
 * - Helpers
 * 
 * @param {Object} options
 * @param {any} options.modelValue - Valeur du v-model
 * @param {string} options.type - Type d'input ('input', 'textarea', 'select', etc.)
 * @param {string} options.mode - Mode ('core' ou 'field')
 * @param {Object} options.props - Props du composant
 * @param {Object} options.attrs - Attributs HTML ($attrs)
 * @param {Function} options.emit - Fonction emit du composant
 * @returns {Object} API unifiée
 */

import { computed, inject, watch } from 'vue'
import useInputActions from './useInputActions'
import useInputProps from './useInputProps'
import { useValidation } from './useValidation'
import { processLabelConfig } from '@/Utils/atomic-design/labelManager'
import { getInputStyleProperties } from './useInputStyle'
import { getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper'

export default function useInputField({
  modelValue,
  type = 'input',
  mode = 'field',
  props,
  attrs,
  emit
}) {
  // --- GESTION DU V-MODEL ET ACTIONS ---
  const {
    currentValue,
    actionsToDisplay,
    inputRef,
    focus,
    isModified,
    isReadonly,
    showPassword,
    handleInput,
    // Handlers d'actions
    reset,
    back,
    clear,
    togglePassword,
    copy,
    toggleEdit,
    toggleLock
  } = useInputActions({
    modelValue,
    type: props.type || type,
    actions: props.actions,
    readonly: props.readonly,
    debounce: props.debounceTime,
    autofocus: props.autofocus,
    emit // Passer la fonction emit
  })

  // --- GESTION DES ATTRIBUTS HTML ---
  const { inputAttrs, listeners } = useInputProps(props, attrs, emit, type, mode)

  // --- FUSION DES ATTRIBUTS AVEC LA VALEUR ---
  // Note: Pour les inputs de type 'file', on ne peut pas définir la propriété 'value'
  // (sauf pour la chaîne vide) pour des raisons de sécurité du navigateur
  const mergedInputAttrs = computed(() => {
    const attrs = { ...inputAttrs.value };
    const inputType = props.type || type;
    // Ne pas ajouter 'value' pour les inputs de type 'file'
    if (inputType !== 'file') {
      attrs.value = currentValue.value;
    }
    return attrs;
  })

  // --- GESTION DE LA VALIDATION ---
  // Nouveau système de validation granulaire
  const validation = useValidation({
    value: currentValue,
    rules: props.validationRules || [],
    externalState: computed(() => props.validation),
    autoValidate: props.autoValidate !== false,
    parentControl: props.parentControl || false
  })

  // Gestion des événements pour la validation
  const handleFocus = (event) => {
    // Marquer comme interagi dès que l'utilisateur clique sur le champ
    validation.setInteracted()
    
    // Émettre l'événement focus original
    if (listeners.focus) {
      listeners.focus(event)
    }
  }

  const handleBlur = (event) => {
    // Marquer comme interagi AVANT la validation
    validation.setInteracted()
    
    // Validation au blur si activée
    if (props.autoValidate !== false) {
      validation.validateOnBlur()
    }

    // Émettre l'événement blur original
    if (listeners.blur) {
      listeners.blur(event)
    }
  }

  const handleInputEvent = (event) => {
    // Marquer comme interagi AVANT la validation
    validation.setInteracted()
    
    // Validation au changement si activée
    if (props.autoValidate !== false) {
      validation.validateOnChange()
    }

    // Logique v-model existante
    handleInput(event)
  }

  // Fusion des listeners avec validation
  const mergedListeners = computed(() => ({
    ...listeners,
    focus: handleFocus,
    blur: handleBlur,
    input: handleInputEvent
  }))

  // Watch pour forcer la validation quand la prop validation change
  watch(() => props.validation, (newValidation) => {
    if (newValidation && typeof newValidation === 'object') {
      // Forcer la validation dans tous les cas
      validation.validate();
    }
  }, { immediate: true, deep: true })

  // Watch pour l'activation/désactivation de la validation
  // Plus besoin de watch sur validationEnabled car la logique est maintenant dans useValidation

  // --- ÉCOUTE DES ACTIONS POUR RÉINITIALISER LA VALIDATION ---
  const handleAction = (action, value) => {
    if (action === 'clear' || action === 'reset') {
      validation.reset()
    }
  }

  // --- GESTION DES LABELS ---
  const labelConfig = computed(() =>
    processLabelConfig(props.label, props.defaultLabelPosition)
  )

  // --- GESTION DU STYLE ---
  const styleProperties = computed(() =>
    getInputStyleProperties(props.type || type, {
      variant: props.variant,
      color: props.color,
      size: props.size,
      animation: props.animation,
      ...(typeof props.inputStyle === 'object' && props.inputStyle !== null ? props.inputStyle : {}),
      ...(typeof props.inputStyle === 'string' ? { variant: props.inputStyle } : {})
    })
  )

  const containerClasses = computed(() =>
    mergeClasses('form-control w-full', getCustomUtilityClasses(props))
  )

  // --- API UNIFIÉE ---
  return {
    // V-model et actions
    currentValue,
    actionsToDisplay,
    inputRef,
    focus,
    isModified,
    isReadonly,
    showPassword,
    
    // Attributs et événements
    inputAttrs: mergedInputAttrs,
    listeners: mergedListeners,
    
    // Labels
    labelConfig,
    
    // Validation (nouvelle API simplifiée)
    validationState: validation.state,
    validationMessage: validation.message,
    validationResults: validation.allResults,
    hasInteracted: validation.hasInteracted,
    validate: validation.validate,
    setInteracted: validation.setInteracted,
    resetValidation: validation.reset,
    isValid: validation.isValid,
    hasError: validation.hasError,
    hasWarning: validation.hasWarning,
    hasSuccess: validation.hasSuccess,
    hasInfo: validation.hasInfo,
    isValidationEnabled: validation.isEnabled,
    
    // Méthodes de contrôle de validation - Supprimées car non nécessaires avec le nouveau système
    
    // Style
    styleProperties,
    containerClasses,
    
    // Handlers d'actions (exposés pour compatibilité)
    reset,
    back,
    clear,
    togglePassword,
    copy,
    toggleEdit,
    toggleLock,
    
    // Helpers
    handleAction
  }
} 