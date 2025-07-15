import { ref, computed, unref } from 'vue';

/**
 * usePasswordToggle — Composable pour la gestion du toggle password
 *
 * Fournit :
 *   - showPassword : état réactif (ref)
 *   - hasNativeToggle : détection du navigateur natif (computed)
 *   - shouldShowToggle : si on doit afficher notre toggle (computed)
 *   - togglePassword() : fonction pour basculer l'état
 *   - effectiveType : type d'input effectif (computed)
 *   - iconClass : classe CSS de l'icône (computed)
 *   - ariaLabel : label d'accessibilité (computed)
 *
 * @param {Object} options - Options de configuration
 * @param {String|ComputedRef} options.type - Type d'input ('password', 'text', etc.)
 * @param {Boolean|ComputedRef} options.showToggle - Si on doit afficher le toggle (défaut true)
 * @returns {Object} API du toggle password
 */
export default function usePasswordToggle(options = {}) {
    const {
        type = 'text',
        showToggle = true
    } = options;

    const showPassword = ref(false);

    // Détecte si le navigateur a déjà un toggle password natif
    const hasNativeToggle = computed(() => {
        if (typeof window === 'undefined') return false;
        
        try {
            const input = document.createElement('input');
            input.type = 'password';
            
            // Vérifie les attributs et propriétés supportés par le navigateur
            return input.hasAttribute('show-password') || 
                   input.hasAttribute('showpassword') ||
                   'showPassword' in input ||
                   input.type === 'password' && 'showPassword' in HTMLInputElement.prototype;
        } catch (e) {
            return false;
        }
    });

    // Détermine si on doit afficher notre toggle
    const shouldShowToggle = computed(() => {
        const currentType = unref(type);
        const currentShowToggle = unref(showToggle);
        return currentType === 'password' && 
               currentShowToggle && 
               !hasNativeToggle.value;
    });

    // Type d'input effectif (password/text selon l'état)
    const effectiveType = computed(() => {
        const currentType = unref(type);
        if (currentType === 'password' && showPassword.value) {
            return 'text';
        }
        return currentType;
    });

    // Classe CSS de l'icône
    const iconClass = computed(() => {
        return showPassword.value 
            ? 'fa-solid fa-eye-slash' 
            : 'fa-solid fa-eye';
    });

    // Label d'accessibilité
    const ariaLabel = computed(() => {
        return showPassword.value 
            ? 'Masquer le mot de passe' 
            : 'Afficher le mot de passe';
    });

    // Fonction pour basculer l'état
    const togglePassword = () => {
        showPassword.value = !showPassword.value;
    };

    // Reset de l'état
    const resetPassword = () => {
        showPassword.value = false;
    };

    return {
        // État
        showPassword,
        
        // Computed
        hasNativeToggle,
        shouldShowToggle,
        effectiveType,
        iconClass,
        ariaLabel,
        
        // Méthodes
        togglePassword,
        resetPassword
    };
} 