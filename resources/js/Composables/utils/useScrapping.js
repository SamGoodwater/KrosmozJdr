/**
 * Composable pour gérer le scrapping d'entités
 * 
 * @description
 * Fournit des fonctions pour déclencher le scrapping d'entités depuis le serveur
 * 
 * @example
 * const { refreshEntity } = useScrapping();
 * await refreshEntity('resource', 123);
 */
import { useNotificationStore } from '@/Composables/store/useNotificationStore';

/**
 * Récupère le token CSRF depuis le DOM
 * @returns {string|null}
 */
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
}

/**
 * Normalise le type d'entité pour l'API de scrapping
 * @param {string} entityType - Type d'entité (ex: 'resource', 'resources', 'item', 'items')
 * @returns {string|null} Type normalisé pour l'API, ou null si non supporté
 */
function normalizeEntityTypeForScrapping(entityType) {
    if (!entityType || typeof entityType !== 'string') return null;

    const raw = entityType.trim().toLowerCase();
    if (!raw) return null;

    // Normaliser pluriels simples
    const singular = raw.endsWith('s') ? raw.slice(0, -1) : raw;

    // Mapping Krosmoz (FR/EN) -> endpoints scrapping
    const map = {
        // DofusDB breeds
        classe: 'class',
        class: 'class',

        // DofusDB monsters
        monster: 'monster',

        // DofusDB items (dont resources/consumables)
        item: 'item',
        resource: 'resource',      // endpoint dédié (strict)
        consumable: 'consumable',  // alias vers item côté backend

        // DofusDB spells
        spell: 'spell',

        // DofusDB item-sets
        panoply: 'panoply',
    };

    return map[singular] || null;
}

export function useScrapping() {
    const notificationStore = useNotificationStore();
    const { success, error: showError } = notificationStore;

    /**
     * Rafraîchit une entité via scrapping
     * 
     * @param {string} entityType - Type d'entité (ex: 'resource', 'item', 'spell')
     * @param {number|string} entityId - ID de l'entité à rafraîchir
     * @param {Object} options - Options de scrapping
     * @param {boolean} options.forceUpdate - Forcer la mise à jour même si déjà à jour
     * @param {boolean} options.skipCache - Ignorer le cache
     * @returns {Promise<boolean>} true si succès, false sinon
     */
    const refreshEntity = async (entityType, entityId, options = {}) => {
        const csrfToken = getCsrfToken();
        if (!csrfToken) {
            showError('Token CSRF introuvable. Veuillez recharger la page.');
            return false;
        }

        const normalizedType = normalizeEntityTypeForScrapping(entityType);
        if (!normalizedType) {
            showError(`Le scrapping n'est pas supporté pour le type "${entityType}".`);
            return false;
        }
        const url = `/api/scrapping/import/${normalizedType}/${entityId}`;
        
        const payload = {
            skip_cache: options.skipCache || false,
            force_update: options.forceUpdate || false,
            dry_run: false,
            validate_only: false,
            include_relations: true,
        };

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload),
            });

            const data = await response.json();

            if (response.ok && data.success !== false) {
                success(data.message || `Entité ${normalizedType} #${entityId} rafraîchie avec succès`);
                return true;
            } else {
                showError(data.message || `Erreur lors du rafraîchissement de l'entité ${normalizedType} #${entityId}`);
                return false;
            }
        } catch (err) {
            showError(`Erreur lors du rafraîchissement : ${err.message}`);
            return false;
        }
    };

    return {
        refreshEntity,
    };
}
