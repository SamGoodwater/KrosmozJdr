import { ref, computed, watch } from 'vue'
import { inject } from 'vue'

/**
 * useValidation — Système de validation granulaire et flexible
 * 
 * Ce composable gère la validation avec support de règles multiples,
 * états différents (error, warning, info, success), et contrôle flexible
 * du déclenchement.
 * 
 * @param {Object} options
 * @param {any} options.value - Valeur à valider (lecture seule)
 * @param {Array} options.rules - Règles de validation granulaire
 * @param {Object} options.externalState - État externe (serveur)
 * @param {boolean} options.autoValidate - Auto-validation au blur/change
 * @param {boolean} options.parentControl - Le parent peut-il surcharger ?
 * @returns {Object} API de validation simplifiée
 */
export function useValidation({
  value,
  rules = [],
  externalState = null,
  autoValidate = true,
  parentControl = false
} = {}) {
  const notificationStore = inject('notificationStore', null)
  
  // État de validation
  const validationState = ref(null)
  const validationMessage = ref(null)
  const validationResults = ref([]) // Tous les résultats de validation
  const hasInteracted = ref(false)
  // Validation activée automatiquement si des règles sont présentes
  const isEnabled = computed(() => rules.length > 0)

  // Évaluation d'une règle
  const evaluateRule = (rule, val) => {
    if (typeof rule.rule === 'function') {
      return rule.rule(val)
    } else if (rule.rule instanceof RegExp) {
      return rule.rule.test(val)
    } else if (typeof rule.rule === 'string') {
      // Patterns prédéfinis
      switch (rule.rule) {
        case 'required':
          return val && val.toString().trim().length > 0
        case 'email':
          return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)
        case 'minLength':
          return val && val.length >= (rule.minLength || 0)
        case 'maxLength':
          return val && val.length <= (rule.maxLength || Infinity)
        case 'password':
          const hasUpperCase = /[A-Z]/.test(val)
          const hasLowerCase = /[a-z]/.test(val)
          const hasNumbers = /\d/.test(val)
          const hasMinLength = val && val.length >= 8
          
          if (hasUpperCase && hasLowerCase && hasNumbers && hasMinLength) {
            return true
          } else {
            return false
          }
        default:
          return true
      }
    }
    return true
  }

  // Validation complète
  const validate = (trigger = 'auto') => {
    if (!isEnabled.value) {
      return null;
    }

    // Ne valider que si l'utilisateur a interagi avec le champ
    // (évite l'affichage d'erreurs au chargement avec autofocus)
    if (!hasInteracted.value && trigger !== 'manual') {
      return null;
    }

    // Si état externe et parent a le contrôle, l'utiliser
    if (externalState && parentControl) {
      validationState.value = externalState.state
      validationMessage.value = externalState.message
      validationResults.value = [externalState]
      return externalState
    }

    const results = []
    
    // Évaluer toutes les règles applicables
    for (const rule of rules) {
      if (rule.trigger === trigger || rule.trigger === 'auto') {
        const isValid = evaluateRule(rule, value.value)
        if (!isValid) {
          results.push({
            ...rule,
            isValid: false,
            priority: rule.priority || 0
          })
        }
      }
    }

    // Trier par priorité et prendre le plus critique
    if (results.length > 0) {
      results.sort((a, b) => b.priority - a.priority)
      const topResult = results[0]
      
      validationState.value = topResult.state
      validationMessage.value = topResult.message
      validationResults.value = results
      
      // Notification si demandée
      if (topResult.showNotification && notificationStore) {
        const config = topResult.notificationConfig || {}
        if (topResult.state === 'error') {
          notificationStore.error(topResult.message, config)
        } else if (topResult.state === 'warning') {
          notificationStore.warning(topResult.message, config)
        } else if (topResult.state === 'info') {
          notificationStore.info(topResult.message, config)
        } else if (topResult.state === 'success') {
          notificationStore.success(topResult.message, config)
        }
      }
      
      return {
        state: topResult.state,
        message: topResult.message,
        allResults: results
      }
    }

    // Aucune erreur
    validationState.value = null
    validationMessage.value = null
    validationResults.value = []
    return null
  }

  // Validation au changement si activée
  if (autoValidate) {
    watch(value, (newVal) => {
      if (hasInteracted.value) {
        validate('change')
      }
    })
  }

  // API publique
  return {
    // État (lecture seule)
    state: computed(() => validationState.value),
    message: computed(() => validationMessage.value),
    allResults: computed(() => validationResults.value),
    
    // Contrôle
    setState: (newState, newMessage) => {
      validationState.value = newState
      validationMessage.value = newMessage
    },
    
    // Validation
    validate,
    validateOnBlur: () => validate('blur'),
    validateOnChange: () => validate('change'),
    
    // Gestion de l'interaction
    setInteracted: () => { hasInteracted.value = true },
    hasInteracted: computed(() => hasInteracted.value),
    
    // Reset
    reset: () => {
      validationState.value = null
      validationMessage.value = null
      validationResults.value = []
      hasInteracted.value = false
    },
    
    // Status
    isEnabled: computed(() => isEnabled.value),
    
    // Helpers de lecture
    isValid: computed(() => validationState.value !== 'error'),
    hasError: computed(() => validationState.value === 'error'),
    hasWarning: computed(() => validationState.value === 'warning'),
    hasSuccess: computed(() => validationState.value === 'success'),
    hasInfo: computed(() => validationState.value === 'info')
  }
} 