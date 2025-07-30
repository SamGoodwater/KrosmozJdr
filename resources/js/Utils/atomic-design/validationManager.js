/**
 * validationManager.js — Gestionnaire de validation pour les composants UI
 *
 * @description
 * Module spécialisé pour la gestion de la validation des composants UI.
 * Fournit des helpers pour créer, valider et traiter les objets de validation
 * avec intégration automatique des notifications.
 *
 * @example
 * import { createValidation, processValidation, quickValidation } from '@/Utils/atomic-design/validationManager';
 * 
 * // Créer une validation
 * const validation = createValidation({ state: 'error', message: 'Erreur' });
 * 
 * // Traiter avec notifications
 * const processed = processValidation(validation, notificationStore);
 * 
 * // Helpers rapides
 * const error = quickValidation.local.error('Message');
 * const success = quickValidation.withNotification.success('Succès !');
 */

/**
 * États de validation supportés
 */
export const validationStates = ['error', 'success', 'warning', 'info'];

/**
 * Types de notifications supportés
 */
export const notificationTypes = ['error', 'success', 'warning', 'info', 'primary', 'secondary', 'auto'];

/**
 * Configuration par défaut pour les validations
 */
export const defaultValidationConfig = {
    state: 'error',
    message: '',
    showNotification: false,
    notificationType: 'auto',
    notificationDuration: 5000,
    notificationPlacement: null
};

/**
 * Crée un objet de validation avec une API claire
 * @param {Object|String|Boolean} config - Configuration de validation
 * @returns {Object} - Objet de validation normalisé
 */
export function createValidation(config) {
    // Si c'est déjà un objet de validation
    if (typeof config === 'object' && config !== null) {
        const normalized = {
            ...defaultValidationConfig,
            ...config,
        };
        
        // Logique pour notificationType : auto → state, null/undefined → auto
        if (!normalized.notificationType || normalized.notificationType === 'auto') {
            normalized.notificationType = normalized.state || 'error';
        }
        
        return normalized;
    }
    
    // Si c'est une string (message d'erreur)
    if (typeof config === 'string') {
        return {
            ...defaultValidationConfig,
            message: config,
        };
    }
    
    // Si c'est un booléen
    if (typeof config === 'boolean') {
        return {
            ...defaultValidationConfig,
            state: config ? 'success' : 'error',
            message: config ? 'Champ valide' : 'Champ invalide',
            notificationType: config ? 'success' : 'error',
        };
    }
    
    // Par défaut
    return { ...defaultValidationConfig };
}

/**
 * Valide un objet de validation
 * @param {Object|String|Boolean} validation - Objet de validation à valider
 * @returns {Boolean} - True si valide
 */
export function validateValidationObject(validation) {
    // Accepter les valeurs par défaut valides
    if (validation === '' || validation === null || validation === undefined) {
        return true;
    }
    
    // Si c'est une string, c'est valide (sera traité comme message d'erreur)
    if (typeof validation === 'string') {
        return true;
    }
    
    // Si c'est un booléen, c'est valide
    if (typeof validation === 'boolean') {
        return true;
    }
    
    // Si c'est un objet, vérifier sa structure
    if (typeof validation === 'object' && validation !== null) {
        // Vérifier que state est valide
        if (validation.state && !validationStates.includes(validation.state)) {
            return false;
        }
        
        // Vérifier que notificationType est valide
        if (validation.notificationType && !notificationTypes.includes(validation.notificationType)) {
            return false;
        }
        
        return true;
    }
    
    return false;
}

/**
 * Traite une validation et déclenche les notifications si nécessaire
 * @param {Object} validation - Objet de validation
 * @param {Object} notificationStore - Store de notifications (useNotificationStore)
 * @returns {Object} - Validation traitée pour affichage local
 */
export function processValidation(validation, notificationStore = null) {
    const normalizedValidation = createValidation(validation);
    
    // Si on doit afficher une notification et qu'on a un store
    if (normalizedValidation.showNotification && notificationStore && normalizedValidation.message) {
        const { success, error, info, warning, primary, secondary } = notificationStore;
        
        const notificationMethod = normalizedValidation.notificationType;
        const methodMap = { success, error, info, warning, primary, secondary };
        
        if (methodMap[notificationMethod]) {
            methodMap[notificationMethod](normalizedValidation.message, {
                duration: normalizedValidation.notificationDuration,
                placement: normalizedValidation.notificationPlacement || undefined // Laisse le système gérer la position par défaut
            });
        }
    }
    
    // Retourner la validation pour affichage local
    return {
        state: normalizedValidation.state,
        message: normalizedValidation.message
    };
}

/**
 * Mappe les erreurs serveur (Laravel/Inertia) vers des objets de validation
 * @param {Object} errors - Erreurs du serveur
 * @param {Object} fieldMapping - Mapping des champs (optionnel)
 * @param {Object} options - Options de validation
 * @returns {Object} - Mapping des erreurs par champ
 */
export function mapServerErrors(errors, fieldMapping = {}, options = {}) {
    const {
        showNotifications = true,
        notificationDuration = 5000,
        notificationPlacement = 'top-right'
    } = options;
    
    return Object.entries(errors).reduce((acc, [field, messages]) => {
        const mappedField = fieldMapping[field] || field;
        const message = Array.isArray(messages) ? messages[0] : messages;
        
        acc[mappedField] = {
            state: 'error',
            message: message,
            showNotification: showNotifications,
            notificationType: 'error',
            notificationDuration: notificationDuration,
            notificationPlacement: notificationPlacement
        };
        
        return acc;
    }, {});
}

/**
 * Valide un champ avec une règle spécifique
 * @param {any} value - Valeur à valider
 * @param {Function} rule - Fonction de validation
 * @param {String} errorMessage - Message d'erreur
 * @returns {Object|null} - Validation ou null si valide
 */
export function validateField(value, rule, errorMessage) {
    if (rule(value)) {
        return null;
    }
    
    return createValidation({
        state: 'error',
        message: errorMessage,
        showNotification: false
    });
}

/**
 * Valide un champ avec plusieurs règles
 * @param {any} value - Valeur à valider
 * @param {Array} rules - Array de règles [{ rule: Function, message: String }]
 * @returns {Object|null} - Première validation qui échoue ou null si toutes valides
 */
export function validateFieldWithRules(value, rules) {
    for (const { rule, message } of rules) {
        const validation = validateField(value, rule, message);
        if (validation) {
            return validation;
        }
    }
    
    return null;
}

/**
 * Crée des validations rapides pour les cas courants
 */
export const quickValidation = {
    // Validation locale uniquement
    local: {
        error: (message) => createValidation({ 
            state: 'error', 
            message, 
            showNotification: false 
        }),
        success: (message) => createValidation({ 
            state: 'success', 
            message, 
            showNotification: false 
        }),
        warning: (message) => createValidation({ 
            state: 'warning', 
            message, 
            showNotification: false 
        }),
        info: (message) => createValidation({ 
            state: 'info', 
            message, 
            showNotification: false 
        })
    },
    
    // Validation avec notification
    withNotification: {
        error: (message, options = {}) => createValidation({ 
            state: 'error', 
            message, 
            showNotification: true,
            notificationType: 'auto',
            ...options 
        }),
        success: (message, options = {}) => createValidation({ 
            state: 'success', 
            message, 
            showNotification: true,
            notificationType: 'auto',
            ...options 
        }),
        warning: (message, options = {}) => createValidation({ 
            state: 'warning', 
            message, 
            showNotification: true,
            notificationType: 'auto',
            ...options 
        }),
        info: (message, options = {}) => createValidation({ 
            state: 'info', 
            message, 
            showNotification: true,
            notificationType: 'auto',
            ...options 
        })
    },
    
    // Règles de validation communes
    rules: {
        required: (value) => value !== null && value !== undefined && value !== '',
        email: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
        minLength: (min) => (value) => value && value.length >= min,
        maxLength: (max) => (value) => value && value.length <= max,
        pattern: (regex) => (value) => regex.test(value),
        numeric: (value) => !isNaN(value) && !isNaN(parseFloat(value)),
        integer: (value) => Number.isInteger(Number(value)),
        positive: (value) => Number(value) > 0,
        between: (min, max) => (value) => value && value >= min && value <= max,
        min: (min) => (value) => value && value >= min,
        max: (max) => (value) => value && value <= max,
        sameAs: (otherValue) => (value) => value === otherValue,
        sameAsField: (otherFieldName) => (value, formData) => {
            if (!formData || typeof formData !== 'object') return false;
            return value === formData[otherFieldName];
        },
        includeLetter: (value) => /[a-zA-Z]/.test(value),
        includeNumber: (value) => /[0-9]/.test(value),
        includeSpecialChar: (value) => /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(value),
        includeUppercase: (value) => /[A-Z]/.test(value),
        includeLowercase: (value) => /[a-z]/.test(value),
        includeSpace: (value) => /\s/.test(value),
        includeNonPrintable: (value) => /[\x00-\x1F\x7F]/.test(value),
        url: (value) => {
            try {
                new URL(value);
                return true;
            } catch {
                return false;
            }
        }
    }
};

// --- HELPERS RAPIDES (Alias pour compatibilité) ---

/**
 * Crée une validation d'erreur simple
 * @param {String} message - Message d'erreur
 * @returns {Object} - Validation d'erreur
 * @deprecated Utilisez quickValidation.local.error() à la place
 */
export function createErrorValidation(message) {
    return quickValidation.local.error(message);
}

/**
 * Crée une validation de succès simple
 * @param {String} message - Message de succès
 * @returns {Object} - Validation de succès
 * @deprecated Utilisez quickValidation.local.success() à la place
 */
export function createSuccessValidation(message) {
    return quickValidation.local.success(message);
}

/**
 * Crée une validation d'avertissement simple
 * @param {String} message - Message d'avertissement
 * @returns {Object} - Validation d'avertissement
 * @deprecated Utilisez quickValidation.local.warning() à la place
 */
export function createWarningValidation(message) {
    return quickValidation.local.warning(message);
}

/**
 * Crée une validation d'information simple
 * @param {String} message - Message d'information
 * @returns {Object} - Validation d'information
 * @deprecated Utilisez quickValidation.local.info() à la place
 */
export function createInfoValidation(message) {
    return quickValidation.local.info(message);
}

/**
 * Crée une validation pour vérifier que deux champs sont identiques
 * @param {String} fieldName - Nom du champ à comparer
 * @param {String} message - Message d'erreur
 * @returns {Object} - Validation sameAs
 * 
 * @example
 * // Dans un formulaire avec password et passwordConfirm
 * const validation = createSameAsValidation('password', 'Les mots de passe doivent être identiques');
 * 
 * // Utilisation avec validateFieldWithRules
 * const result = validateFieldWithRules(passwordConfirm, [validation]);
 */
export function createSameAsValidation(fieldName, message = 'Les champs doivent être identiques') {
    return {
        rule: quickValidation.rules.sameAsField(fieldName),
        message: message,
        state: 'error',
        showNotification: false
    };
}

/**
 * Valide que deux champs sont identiques
 * @param {any} value - Valeur du champ actuel
 * @param {any} otherValue - Valeur du champ à comparer
 * @param {String} message - Message d'erreur
 * @returns {Object|null} - Validation ou null si identiques
 * 
 * @example
 * // Validation directe
 * const validation = validateSameAs(password, passwordConfirm, 'Mots de passe différents');
 * if (validation) {
 *   // Afficher l'erreur
 * }
 */
export function validateSameAs(value, otherValue, message = 'Les champs doivent être identiques') {
    if (quickValidation.rules.sameAs(otherValue)(value)) {
        return null;
    }
    
    return createValidation({
        state: 'error',
        message: message,
        showNotification: false
    });
} 