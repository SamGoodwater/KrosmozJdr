import { ref, computed, watch } from 'vue'
import { inject } from 'vue'

/**
 * useValidation — Système de validation transparent et simplifié
 * 
 * Ce composable gère la validation sans interférer avec le v-model.
 * Il accepte une condition et un objet de messages, et retourne uniquement l'état.
 * 
 * @param {Object} options
 * @param {any} options.value - Valeur à valider (lecture seule)
 * @param {Function|RegExp|String} options.condition - Condition de validation
 * @param {Object} options.messages - Messages par état avec contrôle des notifications
 * @param {boolean} options.validateOnChange - Valider à chaque changement
 * @param {boolean} options.validateOnBlur - Valider au blur
 * @returns {Object} API de validation simplifiée
 */
export function useValidation({
  value,
  condition = null,
  messages = {},
  validateOnChange = false,
  validateOnBlur = true
} = {}) {
  const notificationStore = inject('notificationStore', null)
  
  // État de validation
  const validationState = ref('')
  const validationMessage = ref('')
  const hasInteracted = ref(false)
  
  // Structure par défaut des messages
  const defaultMessages = {
    success: { text: 'Valide', notified: false },
    error: { text: 'Invalide', notified: false },
    warning: { text: 'Attention', notified: false },
    info: { text: 'Information', notified: false }
  }
  
  // Fusion des messages avec les valeurs par défaut
  const mergedMessages = computed(() => ({
    ...defaultMessages,
    ...messages
  }))
  
  // Fonction de validation simplifiée
  const validate = (val = value) => {
    if (!condition) {
      validationState.value = ''
      validationMessage.value = ''
      return ''
    }
    
    let isValid = true
    let state = 'success'
    
    // Validation selon le type de condition
    if (typeof condition === 'function') {
      const result = condition(val)
      if (typeof result === 'boolean') {
        isValid = result
        state = result ? 'success' : 'error'
      } else if (typeof result === 'object' && result.state) {
        state = result.state
        isValid = state !== 'error'
      } else if (typeof result === 'string') {
        state = result
        isValid = result !== 'error'
      }
    } else if (condition instanceof RegExp) {
      isValid = condition.test(val)
      state = isValid ? 'success' : 'error'
    } else if (typeof condition === 'string') {
      // Validation par pattern (email, required, etc.)
      switch (condition) {
        case 'required':
          isValid = val && val.toString().trim().length > 0
          state = isValid ? 'success' : 'error'
          break
        case 'email':
          const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
          isValid = emailRegex.test(val)
          state = isValid ? 'success' : 'error'
          break
        case 'password':
          const hasUpperCase = /[A-Z]/.test(val)
          const hasLowerCase = /[a-z]/.test(val)
          const hasNumbers = /\d/.test(val)
          const hasMinLength = val && val.length >= 8
          
          if (hasUpperCase && hasLowerCase && hasNumbers && hasMinLength) {
            state = 'success'
          } else {
            state = 'warning'
          }
          break
        default:
          state = 'success'
      }
    }
    
    // Mise à jour de l'état
    validationState.value = state
    validationMessage.value = mergedMessages.value[state]?.text || ''
    
    // Notification si demandée
    const messageConfig = mergedMessages.value[state]
    if (messageConfig?.notified && notificationStore && messageConfig.text) {
      if (state === 'error') {
        notificationStore.error(messageConfig.text)
      } else if (state === 'success') {
        notificationStore.success(messageConfig.text)
      } else if (state === 'warning') {
        notificationStore.warning(messageConfig.text)
      } else if (state === 'info') {
        notificationStore.info(messageConfig.text)
      }
    }
    
    return state
  }
  
  // Validation au changement si activée
  if (validateOnChange) {
    watch(value, (newVal) => {
      if (hasInteracted.value) {
        validate(newVal)
      }
    })
  }
  
  // API simplifiée
  return {
    // État de validation (lecture seule)
    state: computed(() => validationState.value),
    message: computed(() => validationMessage.value),
    hasInteracted: computed(() => hasInteracted.value),
    
    // Méthodes
    validate,
    setInteracted: () => { hasInteracted.value = true },
    reset: () => {
      validationState.value = ''
      validationMessage.value = ''
      hasInteracted.value = false
    },
    
    // Helpers de lecture
    isValid: computed(() => validationState.value !== 'error'),
    hasError: computed(() => validationState.value === 'error'),
    hasWarning: computed(() => validationState.value === 'warning'),
    hasSuccess: computed(() => validationState.value === 'success'),
    hasInfo: computed(() => validationState.value === 'info')
  }
} 