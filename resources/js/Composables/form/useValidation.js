import { ref, computed } from 'vue';
import { useNotificationStore } from '@/Composables/store/useNotificationStore';
import { 
    createValidation, 
    processValidation, 
    mapServerErrors,
    quickValidation 
} from '@/Utils/atomic-design/validationManager';

/**
 * useValidation — Composable pour l'intégration validation + notifications
 *
 * @description
 * Composable qui facilite la gestion des validations avec intégration automatique
 * du système de notifications. Fournit des helpers pour les cas d'usage courants.
 *
 * @example
 * const { validateField, validateForm, handleServerErrors } = useValidation();
 * 
 * // Validation locale
 * validateField('email', { state: 'error', message: 'Email invalide' });
 * 
 * // Validation avec notification
 * validateField('email', { 
 *   state: 'success', 
 *   message: 'Email valide !',
 *   showNotification: true 
 * });
 * 
 * // Gestion des erreurs serveur
 * handleServerErrors(form.errors);
 *
 * @returns {Object} API du composable
 */
export function useValidation() {
    const notificationStore = useNotificationStore();
    const fieldValidations = ref({});
    
    /**
     * Valide un champ spécifique
     * @param {String} fieldName - Nom du champ
     * @param {Object|String|Boolean} validation - Configuration de validation
     * @returns {Object} - Validation traitée
     */
    function validateField(fieldName, validation) {
        const processed = processValidation(validation, notificationStore);
        fieldValidations.value[fieldName] = processed;
        return processed;
    }
    
    /**
     * Valide plusieurs champs d'un coup
     * @param {Object} validations - Objet { fieldName: validation }
     * @returns {Object} - Validations traitées
     */
    function validateForm(validations) {
        const results = {};
        Object.entries(validations).forEach(([field, validation]) => {
            results[field] = validateField(field, validation);
        });
        return results;
    }
    
    /**
     * Gère les erreurs serveur (Laravel/Inertia)
     * @param {Object} errors - Erreurs du serveur
     * @param {Object} options - Options de configuration
     * @returns {Object} - Mapping des erreurs par champ
     */
    function handleServerErrors(errors, options = {}) {
        const mappedErrors = mapServerErrors(errors, {}, options);
        Object.entries(mappedErrors).forEach(([field, validation]) => {
            validateField(field, validation);
        });
        return mappedErrors;
    }
    
    /**
     * Efface la validation d'un champ
     * @param {String} fieldName - Nom du champ
     */
    function clearFieldValidation(fieldName) {
        delete fieldValidations.value[fieldName];
    }
    
    /**
     * Efface toutes les validations
     */
    function clearAllValidations() {
        fieldValidations.value = {};
    }
    
    /**
     * Récupère la validation d'un champ
     * @param {String} fieldName - Nom du champ
     * @returns {Object|null} - Validation du champ
     */
    function getFieldValidation(fieldName) {
        return fieldValidations.value[fieldName] || null;
    }
    
    /**
     * Vérifie si un champ a une validation
     * @param {String} fieldName - Nom du champ
     * @returns {Boolean} - True si le champ a une validation
     */
    function hasFieldValidation(fieldName) {
        return fieldName in fieldValidations.value;
    }
    
    /**
     * Vérifie si un champ est en erreur
     * @param {String} fieldName - Nom du champ
     * @returns {Boolean} - True si le champ est en erreur
     */
    function isFieldInError(fieldName) {
        const validation = fieldValidations.value[fieldName];
        return validation && validation.state === 'error';
    }
    
    /**
     * Vérifie si un champ est valide
     * @param {String} fieldName - Nom du champ
     * @returns {Boolean} - True si le champ est valide
     */
    function isFieldValid(fieldName) {
        const validation = fieldValidations.value[fieldName];
        return validation && validation.state === 'success';
    }
    
    // --- COMPUTED PROPERTIES ---
    
    /**
     * Compte le nombre de champs avec des erreurs
     * @returns {Number} - Nombre d'erreurs
     */
    const errorCount = computed(() => {
        return Object.values(fieldValidations.value)
            .filter(validation => validation.state === 'error')
            .length;
    });
    
    /**
     * Compte le nombre de champs valides
     * @returns {Number} - Nombre de champs valides
     */
    const successCount = computed(() => {
        return Object.values(fieldValidations.value)
            .filter(validation => validation.state === 'success')
            .length;
    });
    
    /**
     * Vérifie si le formulaire est valide (aucune erreur)
     * @returns {Boolean} - True si aucune erreur
     */
    const isValid = computed(() => errorCount.value === 0);
    
    /**
     * Vérifie si le formulaire a des validations
     * @returns {Boolean} - True si au moins une validation
     */
    const hasValidations = computed(() => Object.keys(fieldValidations.value).length > 0);
    
    /**
     * Récupère toutes les validations
     * @returns {Object} - Toutes les validations
     */
    const allValidations = computed(() => fieldValidations.value);
    
    /**
     * Récupère seulement les erreurs
     * @returns {Object} - Erreurs par champ
     */
    const errors = computed(() => {
        const result = {};
        for (const [field, validation] of Object.entries(fieldValidations.value)) {
            if (validation.state === 'error') {
                result[field] = validation;
            }
        }
        return result;
    });
    
    /**
     * Récupère seulement les succès
     * @returns {Object} - Succès par champ
     */
    const successes = computed(() => {
        const result = {};
        for (const [field, validation] of Object.entries(fieldValidations.value)) {
            if (validation.state === 'success') {
                result[field] = validation;
            }
        }
        return result;
    });
    
    // --- HELPERS RAPIDES ---
    
    /**
     * Valide un champ avec une erreur locale
     * @param {String} fieldName - Nom du champ
     * @param {String} message - Message d'erreur
     */
    function setFieldError(fieldName, message) {
        validateField(fieldName, quickValidation.local.error(message));
    }
    
    /**
     * Valide un champ avec un succès local
     * @param {String} fieldName - Nom du champ
     * @param {String} message - Message de succès
     */
    function setFieldSuccess(fieldName, message) {
        validateField(fieldName, quickValidation.local.success(message));
    }
    
    /**
     * Valide un champ avec une erreur et notification
     * @param {String} fieldName - Nom du champ
     * @param {String} message - Message d'erreur
     * @param {Object} options - Options de notification
     */
    function setFieldErrorWithNotification(fieldName, message, options = {}) {
        validateField(fieldName, quickValidation.withNotification.error(message, options));
    }
    
    /**
     * Valide un champ avec un succès et notification
     * @param {String} fieldName - Nom du champ
     * @param {String} message - Message de succès
     * @param {Object} options - Options de notification
     */
    function setFieldSuccessWithNotification(fieldName, message, options = {}) {
        validateField(fieldName, quickValidation.withNotification.success(message, options));
    }
    
    return {
        // Méthodes principales
        validateField,
        validateForm,
        handleServerErrors,
        clearFieldValidation,
        clearAllValidations,
        getFieldValidation,
        hasFieldValidation,
        isFieldInError,
        isFieldValid,
        
        // Helpers rapides
        setFieldError,
        setFieldSuccess,
        setFieldErrorWithNotification,
        setFieldSuccessWithNotification,
        
        // Computed
        errorCount,
        successCount,
        isValid,
        hasValidations,
        allValidations,
        errors,
        successes,
        
        // Store de notifications (pour usage avancé)
        notificationStore,
        
        // Helpers rapides (pour compatibilité)
        quickValidation
    };
} 