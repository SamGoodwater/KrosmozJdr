/**
 * Construit l’URL publique DofusDB pour une entité Krosmoz.
 *
 * @description
 * Mappe le type d’entité (singulier ou pluriel) vers le segment
 * `https://dofusdb.fr/fr/database/{segment}/{id}`.
 *
 * @example
 * buildDofusDbEntityUrl('spells', 201)
 * // → 'https://dofusdb.fr/fr/database/spells/201'
 */

/** @type {Readonly<Record<string, string>>} */
export const DOFUSDB_DATABASE_SEGMENTS = Object.freeze({
    spell: 'spells',
    spells: 'spells',
    monster: 'monsters',
    monsters: 'monsters',
    item: 'items',
    items: 'items',
    consumable: 'items',
    consumables: 'items',
    resource: 'items',
    resources: 'items',
    breed: 'breeds',
    breeds: 'breeds',
    classe: 'breeds',
    class: 'breeds',
    panoply: 'item-sets',
    panoplies: 'item-sets',
    condition: 'states',
    conditions: 'states',
});

export const DOFUSDB_SITE_BASE = 'https://dofusdb.fr/fr/database';

/**
 * Normalise un identifiant DofusDB (string/number).
 *
 * @param {unknown} dofusdbId
 * @returns {string|null}
 */
export function normalizeDofusDbId(dofusdbId) {
    if (dofusdbId == null || dofusdbId === '') return null;
    const value = String(dofusdbId).trim();
    return value === '' ? null : value;
}

/**
 * Extrait `dofusdb_id` depuis une entité modèle JS ou objet brut.
 *
 * @param {Object|null|undefined} entity
 * @returns {string|null}
 */
export function getEntityDofusDbId(entity) {
    if (!entity) return null;
    const raw = entity._data ?? entity;
    return normalizeDofusDbId(
        raw?.dofusdb_id
        ?? entity?.dofusdbId
        ?? entity?.dofusdb_id
        ?? null,
    );
}

/**
 * Segment URL DofusDB pour un type d’entité Krosmoz.
 *
 * @param {string} entityType
 * @returns {string|null}
 */
export function resolveDofusDbDatabaseSegment(entityType) {
    const key = String(entityType || '').trim();
    return DOFUSDB_DATABASE_SEGMENTS[key] || null;
}

/**
 * URL fiche DofusDB, ou null si type/id invalides.
 *
 * @param {string} entityType - Type Krosmoz (ex. `spells`, `spell`, `monsters`)
 * @param {string|number|null|undefined} dofusdbId
 * @returns {string|null}
 */
export function buildDofusDbEntityUrl(entityType, dofusdbId) {
    const id = normalizeDofusDbId(dofusdbId);
    const segment = resolveDofusDbDatabaseSegment(entityType);
    if (!id || !segment) return null;
    return `${DOFUSDB_SITE_BASE}/${segment}/${encodeURIComponent(id)}`;
}
