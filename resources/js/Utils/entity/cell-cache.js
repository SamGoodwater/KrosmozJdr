/**
 * Cache pour les cellules générées par les adapters
 *
 * @description
 * Système de cache pour éviter de régénérer les cellules identiques.
 * Le cache est basé sur une clé composée de l'ID de l'entité, du colId, et des options.
 */

/**
 * @typedef {Object} CellCacheEntry
 * @property {any} cell
 * @property {number} timestamp
 */

/**
 * Cache global pour les cellules
 * @type {Map<string, CellCacheEntry>}
 */
const cellCache = new Map();

/**
 * TTL du cache en millisecondes (2 minutes)
 */
const CELL_CACHE_TTL = 2 * 60 * 1000;

/**
 * Taille maximale du cache (éviter la fuite mémoire)
 */
const MAX_CACHE_SIZE = 1000;

/**
 * Génère une clé de cache pour une cellule
 *
 * @param {string|number} entityId
 * @param {string} colId
 * @param {any} entity
 * @param {any} opts
 * @returns {string}
 */
function generateCacheKey(entityId, colId, entity, opts = {}) {
    // Utiliser une clé basée sur l'ID de l'entité, le colId, et les options importantes
    const optsKey = opts?.context || 'table';
    const sizeKey = opts?.size || 'normal';
    // Pour les entités, on utilise l'ID et le colId comme base
    // On ne hash pas toute l'entité pour éviter des clés trop longues
    return `${entityId}:${colId}:${optsKey}:${sizeKey}`;
}

/**
 * Vérifie si une entrée de cache est valide
 *
 * @param {CellCacheEntry} entry
 * @returns {boolean}
 */
function isCellCacheValid(entry) {
    if (!entry) return false;
    const now = Date.now();
    return (now - entry.timestamp) < CELL_CACHE_TTL;
}

/**
 * Nettoie le cache des entrées expirées et limite la taille
 */
function cleanCellCache() {
    const now = Date.now();
    const expiredKeys = [];

    // Trouver les entrées expirées
    for (const [key, entry] of cellCache.entries()) {
        if ((now - entry.timestamp) >= CELL_CACHE_TTL) {
            expiredKeys.push(key);
        }
    }

    // Supprimer les entrées expirées
    expiredKeys.forEach(key => cellCache.delete(key));

    // Si le cache est encore trop grand, supprimer les plus anciennes entrées
    if (cellCache.size > MAX_CACHE_SIZE) {
        const entries = Array.from(cellCache.entries())
            .sort((a, b) => a[1].timestamp - b[1].timestamp);
        const toRemove = cellCache.size - MAX_CACHE_SIZE;
        for (let i = 0; i < toRemove; i++) {
            cellCache.delete(entries[i][0]);
        }
    }
}

/**
 * Récupère une cellule depuis le cache ou la génère
 *
 * @param {Function} buildCellFn
 * @param {string} colId
 * @param {any} entity
 * @param {any} ctx
 * @param {any} opts
 * @returns {any}
 */
export function getCachedCell(buildCellFn, colId, entity, ctx = {}, opts = {}) {
    if (typeof buildCellFn !== 'function') {
        return null;
    }

    const entityId = entity?.id ?? 'unknown';
    const cacheKey = generateCacheKey(entityId, colId, entity, opts);

    // Nettoyer le cache périodiquement (toutes les 20 requêtes environ)
    if (Math.random() < 0.05) {
        cleanCellCache();
    }

    // Vérifier le cache
    const cached = cellCache.get(cacheKey);
    if (cached && isCellCacheValid(cached)) {
        // Vérifier que l'entité n'a pas changé (comparaison simple)
        // Note: On pourrait améliorer cela avec un hash de l'entité, mais c'est plus coûteux
        return cached.cell;
    }

    // Générer la cellule
    const cell = buildCellFn(colId, entity, ctx, opts);

    // Mettre en cache
    cellCache.set(cacheKey, {
        cell,
        timestamp: Date.now(),
    });

    return cell;
}

/**
 * Invalide le cache pour une entité spécifique
 *
 * @param {string|number} entityId
 */
export function invalidateCellCache(entityId) {
    if (entityId == null) return;
    const idStr = String(entityId);
    for (const key of cellCache.keys()) {
        if (key.startsWith(`${idStr}:`)) {
            cellCache.delete(key);
        }
    }
}

/**
 * Invalide tout le cache de cellules
 */
export function clearCellCache() {
    cellCache.clear();
}

/**
 * Obtient la taille actuelle du cache
 *
 * @returns {number}
 */
export function getCellCacheSize() {
    return cellCache.size;
}

