/**
 * Place les fiches déjà en favoris en tête d’une liste de résultats de recherche.
 *
 * @param {Array<{ entityType?: string, id?: number|string }>} results
 * @param {(entityType: string, id: number|string) => boolean} isFavorite
 * @returns {typeof results}
 *
 * @example
 * rankResultsWithFavoritesFirst(rows, isEntityFavorite);
 */
export function rankResultsWithFavoritesFirst(results, isFavorite) {
    if (!Array.isArray(results) || results.length === 0) return Array.isArray(results) ? results : [];
    if (typeof isFavorite !== "function") return [...results];

    const favorites = [];
    const others = [];
    for (const row of results) {
        const type = row?.entityType || row?.entity_type || "";
        const id = row?.id ?? row?.entity_id;
        if (type && id != null && isFavorite(type, id)) {
            favorites.push(row);
        } else {
            others.push(row);
        }
    }
    return [...favorites, ...others];
}
