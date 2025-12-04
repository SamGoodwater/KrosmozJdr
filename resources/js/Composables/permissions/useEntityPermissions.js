/**
 * useEntityPermissions Composable
 * 
 * @description
 * Composable pour vérifier les permissions de création d'entités.
 * Utilise les policies Laravel pour déterminer si l'utilisateur peut créer une entité.
 * 
 * @example
 * const { canCreateEntity } = useEntityPermissions();
 * const canCreate = canCreateEntity('item'); // Vérifie si l'utilisateur peut créer un item
 */
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { User } from '@/Models';

/**
 * Vérifie si l'utilisateur peut créer une entité
 * @param {string} entityType - Type d'entité (ex: 'item', 'npc', 'spell', etc.)
 * @returns {boolean}
 */
export function useEntityPermissions() {
    const page = usePage();
    
    /**
     * Récupère l'utilisateur connecté
     */
    const user = computed(() => {
        const authUser = page.props.auth?.user;
        if (!authUser) return null;
        // Si c'est déjà une instance de User, la retourner, sinon en créer une
        return authUser instanceof User ? authUser : new User(authUser);
    });

    /**
     * Vérifie si l'utilisateur peut créer une entité
     * Par défaut, seuls les admins (role >= 4) peuvent créer des entités
     * selon BaseEntityPolicy::create()
     * 
     * @param {string} entityType - Type d'entité
     * @returns {boolean}
     */
    const canCreateEntity = (entityType) => {
        if (!user.value) return false;
        
        // Par défaut, seuls les admins peuvent créer des entités
        // Cela correspond à BaseEntityPolicy::create() qui vérifie $user->isAdmin()
        return user.value.isAdmin;
    };

    /**
     * Vérifie si l'utilisateur est connecté
     * @returns {boolean}
     */
    const isAuthenticated = computed(() => {
        return !!user.value;
    });

    /**
     * Vérifie si l'utilisateur est un admin
     * @returns {boolean}
     */
    const isAdmin = computed(() => {
        return user.value?.isAdmin || false;
    });

    /**
     * Vérifie si l'utilisateur est un super admin
     * @returns {boolean}
     */
    const isSuperAdmin = computed(() => {
        return user.value?.isSuperAdmin || false;
    });

    return {
        user,
        canCreateEntity,
        isAuthenticated,
        isAdmin,
        isSuperAdmin
    };
}

export default useEntityPermissions;

