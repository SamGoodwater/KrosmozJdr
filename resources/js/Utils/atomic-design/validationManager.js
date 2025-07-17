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
export const notificationTypes = ['error', 'success', 'warning', 'info', 'primary', 'secondary'];

/**
 * Crée un objet de validation avec une API claire
 * @param {Object|String|Boolean} config - Configuration de validation
 * @returns {Object} - Objet de validation normalisé
 */
export function createValidation(config) {
    // Si c'est déjà un objet de validation
    if (typeof config === 'object' && config !== null) {
        return {
            state: config.state || 'error',
            message: config.message || '',
            showNotification: config.showNotification || false,
            notificationType: config.notificationType || config.state || 'error',
            notificationDuration: config.notificationDuration || 5000,
            notificationPlacement: config.notificationPlacement || 'top-right'
        };
    }
    
    // Si c'est une string (message d'erreur)
    if (typeof config === 'string') {
        return {
            state: 'error',
            message: config,
            showNotification: false,
            notificationType: 'error',
            notificationDuration: 5000,
            notificationPlacement: 'top-right'
        };
    }
    
    // Si c'est un booléen
    if (typeof config === 'boolean') {
        return {
            state: config ? 'success' : 'error',
            message: config ? 'Champ valide' : 'Champ invalide',
            showNotification: false,
            notificationType: config ? 'success' : 'error',
            notificationDuration: 5000,
            notificationPlacement: 'top-right'
        };
    }
    
    // Par défaut
    return {
        state: 'error',
        message: '',
        showNotification: false,
        notificationType: 'error',
        notificationDuration: 5000,
        notificationPlacement: 'top-right'
    };
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
 * @param {Function} notificationStore - Store de notifications (useNotificationStore)
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
                placement: normalizedValidation.notificationPlacement
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
 * Crée des validations rapides pour les cas courants
 */
export const quickValidation = {
    // Validation locale uniquement
    local: {
        error: (message) => createValidation({ state: 'error', message, showNotification: false }),
        success: (message) => createValidation({ state: 'success', message, showNotification: false }),
        warning: (message) => createValidation({ state: 'warning', message, showNotification: false }),
        info: (message) => createValidation({ state: 'info', message, showNotification: false })
    },
    
    // Validation avec notification
    withNotification: {
        error: (message, options = {}) => createValidation({ 
            state: 'error', 
            message, 
            showNotification: true,
            notificationType: 'error',
            ...options 
        }),
        success: (message, options = {}) => createValidation({ 
            state: 'success', 
            message, 
            showNotification: true,
            notificationType: 'success',
            ...options 
        }),
        warning: (message, options = {}) => createValidation({ 
            state: 'warning', 
            message, 
            showNotification: true,
            notificationType: 'warning',
            ...options 
        }),
        info: (message, options = {}) => createValidation({ 
            state: 'info', 
            message, 
            showNotification: true,
            notificationType: 'info',
            ...options 
        })
    }
}; 