/**
 * Cache pour les descriptors d'entités
 *
 * @description
 * Système de cache pour éviter de recalculer les descriptors à chaque fois.
 * Le cache est invalidé automatiquement si le contexte change (capabilities, etc.).
 */

/**
 * @typedef {Object} CacheEntry
 * @property {any} descriptors
 * @property {string} contextHash
 * @property {number} timestamp
 */

/**
 * Cache global pour les descriptors
 * @type {Map<string, CacheEntry>}
 */
const descriptorCache = new Map();

/**
 * TTL du cache en millisecondes (5 minutes)
 */
const CACHE_TTL = 5 * 60 * 1000;

/**
 * Génère un hash simple du contexte pour détecter les changements
 *
 * @param {any} ctx
 * @returns {string}
 */
function hashContext(ctx) {
    if (!ctx || typeof ctx !== 'object') return 'default';
    const keys = Object.keys(ctx).sort();
    const parts = keys.map(key => {
        const value = ctx[key];
        if (value && typeof value === 'object') {
            // Pour les objets complexes (comme capabilities), on hash les valeurs importantes
            if ('capabilities' in value) {
                return `capabilities:${JSON.stringify(value.capabilities || {})}`;
            }
            return `${key}:${JSON.stringify(value)}`;
        }
        return `${key}:${String(value)}`;
    });
    return parts.join('|');
}

/**
 * Vérifie si une entrée de cache est valide
 *
 * @param {CacheEntry} entry
 * @returns {boolean}
 */
function isCacheValid(entry) {
    if (!entry) return false;
    const now = Date.now();
    return (now - entry.timestamp) < CACHE_TTL;
}

/**
 * Nettoie le cache des entrées expirées
 */
function cleanExpiredCache() {
    const now = Date.now();
    for (const [key, entry] of descriptorCache.entries()) {
        if ((now - entry.timestamp) >= CACHE_TTL) {
            descriptorCache.delete(key);
        }
    }
}

/**
 * Récupère les descriptors depuis le cache ou les calcule
 *
 * @param {string} entityType
 * @param {Function} getDescriptorsFn
 * @param {any} ctx
 * @returns {any}
 */
export function getCachedDescriptors(entityType, getDescriptorsFn, ctx = {}) {
    if (typeof getDescriptorsFn !== 'function') {
        return {};
    }

    const contextHash = hashContext(ctx);
    const cacheKey = `${entityType}:${contextHash}`;

    // Nettoyer le cache périodiquement (toutes les 10 requêtes environ)
    if (Math.random() < 0.1) {
        cleanExpiredCache();
    }

    // Vérifier le cache
    const cached = descriptorCache.get(cacheKey);
    if (cached && isCacheValid(cached) && cached.contextHash === contextHash) {
        return cached.descriptors;
    }

    // Calculer les descriptors
    const descriptors = getDescriptorsFn(ctx) || {};

    // Mettre en cache
    descriptorCache.set(cacheKey, {
        descriptors,
        contextHash,
        timestamp: Date.now(),
    });

    return descriptors;
}

/**
 * Invalide le cache pour un type d'entité spécifique
 *
 * @param {string} [entityType] - Si non fourni, invalide tout le cache
 */
export function invalidateDescriptorCache(entityType = null) {
    if (entityType) {
        // Invalider uniquement les entrées pour ce type d'entité
        for (const key of descriptorCache.keys()) {
            if (key.startsWith(`${entityType}:`)) {
                descriptorCache.delete(key);
            }
        }
    } else {
        // Invalider tout le cache
        descriptorCache.clear();
    }
}

/**
 * Obtient la taille actuelle du cache
 *
 * @returns {number}
 */
export function getCacheSize() {
    return descriptorCache.size;
}

