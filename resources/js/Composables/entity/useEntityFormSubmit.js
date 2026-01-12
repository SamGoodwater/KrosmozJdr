/**
 * useEntityFormSubmit — Composable pour la soumission de formulaires d'entités
 * 
 * @description
 * Fournit la logique commune de soumission pour les formulaires d'édition d'entités.
 * Gère la construction des routes et l'appel API via useForm d'Inertia.
 * Utilise entityRouteRegistry pour gérer correctement les pluriels et les routes.
 * 
 * @param {Object} options
 * @param {Object} options.form - Instance useForm d'Inertia
 * @param {Object} options.resource - Instance de l'entité à éditer
 * @param {Boolean} options.isUpdating - Mode édition (true) ou création (false)
 * @param {String} options.entityType - Type d'entité (ex: 'resource', 'item')
 * @param {Function} [options.onSuccess] - Callback appelé en cas de succès
 * @returns {Object} { handleSubmit }
 * 
 * @example
 * const form = useForm({ name: '', ... });
 * const { handleSubmit } = useEntityFormSubmit({
 *   form,
 *   resource: props.resource,
 *   isUpdating: !!props.resource?.id,
 *   entityType: 'resource',
 *   onSuccess: () => emit('submit')
 * });
 */
export function useEntityFormSubmit({ form, resource, isUpdating, entityType, onSuccess }) {
    /**
     * Gère la soumission du formulaire
     * 
     * Note: entityRouteRegistry ne gère que show/edit/delete, pas update/store.
     * On utilise donc la construction manuelle avec fallback pour les pluriels.
     */
    const handleSubmit = () => {
        const method = isUpdating ? 'put' : 'post';
        
        // Construction du nom de route selon le type d'entité
        // Gestion des pluriels irréguliers
        const entityTypePlural = entityType === 'panoply' ? 'panoplies' : `${entityType}s`;
        const routeName = isUpdating 
            ? `entities.${entityTypePlural}.update`
            : `entities.${entityTypePlural}.store`;
        
        // Paramètres de route selon le type d'entité
        const routeParams = isUpdating 
            ? { [entityType]: resource.id }
            : {};
        
        form[method](route(routeName, routeParams), {
            onSuccess: () => {
                if (typeof onSuccess === 'function') {
                    onSuccess();
                }
            },
        });
    };

    return {
        handleSubmit,
    };
}
