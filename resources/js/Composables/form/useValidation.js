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
     * Compte le nombre de champs avec des erreurs
     * @returns {Number} - Nombre d'erreurs
     */
    const errorCount = computed(() => {
        return Object.values(fieldValidations.value)
            .filter(validation => validation.state === 'error')
            .length;
    });
    
    /**
     * Vérifie si le formulaire est valide
     * @returns {Boolean} - True si aucune erreur
     */
    const isValid = computed(() => errorCount.value === 0);
    
    /**
     * Récupère toutes les validations
     * @returns {Object} - Toutes les validations
     */
    const allValidations = computed(() => fieldValidations.value);
    
    return {
        // Méthodes principales
        validateField,
        validateForm,
        handleServerErrors,
        clearFieldValidation,
        clearAllValidations,
        getFieldValidation,
        hasFieldValidation,
        
        // Computed
        errorCount,
        isValid,
        allValidations,
        
        // Helpers rapides
        quickValidation,
        
        // Store de notifications (pour usage avancé)
        notificationStore
    };
} 