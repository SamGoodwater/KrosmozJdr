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
  const mergedInputAttrs = computed(() => ({
    ...inputAttrs.value,
    value: currentValue.value // Ajouter la valeur actuelle
  }))

  // --- FUSION DES LISTENERS (transparente) ---
  const mergedListeners = computed(() => ({
    ...listeners,
    // Ajouter le gestionnaire d'événement input pour le v-model
    input: handleInput,
    // Les événements personnalisés passés via $attrs sont préservés
    // useInputActions gère le v-model en interne
  }))

  // --- GESTION DES LABELS ---
  const labelConfig = computed(() =>
    processLabelConfig(props.label, props.defaultLabelPosition)
  )

  // --- GESTION DE LA VALIDATION ---
  // Extraction de la condition et des messages depuis la prop validation
  const validationConfig = computed(() => {
    if (!props.validation) return { condition: null, messages: {}, directState: null }
    
    // Si c'est une string, regex ou fonction, c'est la condition
    if (typeof props.validation === 'string' || 
        props.validation instanceof RegExp || 
        typeof props.validation === 'function') {
      return {
        condition: props.validation,
        messages: {},
        directState: null
      }
    }
    
    // Si c'est un objet avec condition et messages
    if (typeof props.validation === 'object') {
      const { condition, messages, state, message, ...otherProps } = props.validation
      
      // Support de l'ancienne API pour compatibilité
      if (state && message) {
        return {
          condition: null,
          messages: {},
          directState: { state, message }
        }
      }
      
      // Nouvelle API
      return {
        condition: condition || null,
        messages: messages || {},
        directState: null
      }
    }
    
    return { condition: null, messages: {}, directState: null }
  })

  const validation = useValidation({
    value: currentValue,
    condition: validationConfig.value.condition,
    messages: validationConfig.value.messages,
    validateOnChange: false,
    validateOnBlur: true,
    directState: validationConfig.value.directState,
    enabled: props.validationEnabled || false // Nouvelle prop
  })

  // Watch pour forcer la validation quand la prop validation change
  watch(() => props.validation, (newValidation) => {
    if (newValidation && typeof newValidation === 'object') {
      // Forcer la validation dans tous les cas
      validation.validate();
    }
  }, { immediate: true, deep: true })

  // Watch pour forcer la validation quand validationConfig change
  watch(() => validationConfig.value, (newConfig) => {
    validation.validate();
  }, { deep: true, immediate: true })

  // Watch pour la prop validationEnabled
  watch(() => props.validationEnabled, (enabled) => {
    if (enabled) {
      validation.enableValidation();
    } else {
      validation.disableValidation();
    }
  }, { immediate: true })

  // --- ÉCOUTE DES ACTIONS POUR RÉINITIALISER LA VALIDATION ---
  const handleAction = (action, value) => {
    if (action === 'clear' || action === 'reset') {
      validation.reset()
    }
  }

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
    
    // Méthodes de contrôle de validation
    enableValidation: validation.enableValidation,
    disableValidation: validation.disableValidation,
    
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