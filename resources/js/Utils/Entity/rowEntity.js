/**
 * Récupère l'entité métier portée par une ligne de table.
 *
 * @description
 * Les adapters placent l'instance dans `row.rowParams.entity`. Les fallbacks
 * gardent les anciennes rows plates utilisables.
 *
 * @example
 * const entity = getRowEntity(row);
 *
 * @param {object|null|undefined} row
 * @returns {object|null}
 */
export function getRowEntity(row) {
    if (!row) return null;
    return row?.rowParams?.entity ?? row;
}

export default getRowEntity;
