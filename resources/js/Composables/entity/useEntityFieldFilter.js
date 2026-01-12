/**
 * useEntityFieldFilter — Composable pour filtrer les champs d'entité selon les permissions
 * 
 * @description
 * Fournit la logique de filtrage des champs selon les permissions (visibleIf, editableIf).
 * Utilisé pour déterminer quels champs doivent être affichés/éditables dans les formulaires.
 * 
 * @param {ComputedRef<Record<string, any>>} descriptors - Descriptors des champs (computed)
 * @param {ComputedRef<any>|any} ctx - Contexte pour les permissions (computed ou objet)
 * @param {Object} [options] - Options de filtrage
 * @param {Boolean} [options.checkVisibility=true] - Vérifier la visibilité (visibleIf)
 * @param {Boolean} [options.checkEditability=false] - Vérifier l'édition (editableIf)
 * @param {Boolean} [options.isAdmin=false] - L'utilisateur a les droits d'édition
 * 
 * @returns {Object} { isFieldVisible, filterFields }
 * 
 * @example
 * const descriptors = computed(() => getResourceFieldDescriptors(ctx.value));
 * const ctx = computed(() => ({ capabilities: { updateAny: isAdmin } }));
 * const { isFieldVisible, filterFields } = useEntityFieldFilter(descriptors, ctx, {
 *   checkVisibility: true,
 *   checkEditability: true,
 *   isAdmin: true
 * });
 * 
 * const visibleFields = computed(() => filterFields(Object.keys(fieldsConfig.value)));
 */
import { computed } from 'vue';

export function useEntityFieldFilter(descriptors, ctx, options = {}) {
    const {
        checkVisibility = true,
        checkEditability = false,
        isAdmin = false,
    } = options;

    // Normaliser ctx (peut être computed ou objet)
    const ctxValue = computed(() => {
        if (ctx && typeof ctx === 'object' && 'value' in ctx) {
            return ctx.value;
        }
        return ctx;
    });

    /**
     * Vérifie si un champ est visible selon les permissions
     * 
     * @param {string} fieldKey - Clé du champ
     * @returns {boolean} true si le champ est visible
     */
    const isFieldVisible = (fieldKey) => {
        const desc = descriptors.value?.[fieldKey];
        if (!desc) return false;

        // Vérifier la visibilité
        if (checkVisibility) {
            const visibleIf = desc.permissions?.visibleIf;
            if (visibleIf && typeof visibleIf === 'function') {
                try {
                    if (!visibleIf(ctxValue.value)) return false;
                } catch (error) {
                    console.warn(`[useEntityFieldFilter] Erreur dans visibleIf pour ${fieldKey}:`, error);
                    return false;
                }
            }
        }

        // Vérifier l'édition
        if (checkEditability && isAdmin) {
            const editableIf = desc.permissions?.editableIf || desc.edition?.form?.editableIf;
            if (editableIf && typeof editableIf === 'function') {
                try {
                    if (!editableIf(ctxValue.value)) return false;
                } catch (error) {
                    console.warn(`[useEntityFieldFilter] Erreur dans editableIf pour ${fieldKey}:`, error);
                    return false;
                }
            }
        } else if (checkEditability && !isAdmin) {
            // Si on vérifie l'édition mais que l'utilisateur n'est pas admin, exclure le champ
            return false;
        }

        return true;
    };

    /**
     * Filtre une liste de clés de champs selon les permissions
     * 
     * @param {string[]} fieldKeys - Liste des clés de champs à filtrer
     * @returns {string[]} Liste filtrée des clés de champs
     */
    const filterFields = (fieldKeys) => {
        if (!Array.isArray(fieldKeys)) return [];
        return fieldKeys.filter((key) => isFieldVisible(key));
    };

    return {
        isFieldVisible,
        filterFields,
    };
}
