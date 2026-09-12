/**
 * Tri initial déclaré dans `_tableConfig.features.sort.initial`.
 *
 * @example
 * tableInitialSortingState({ features: { sort: { initial: { field: "level", dir: "asc" } } } })
 * // [{ id: "level", desc: false }]
 */

/**
 * @param {unknown} config
 * @param {Set<string>|null} [allowedIds]
 * @returns {{ id: string, desc: boolean }[]}
 */
export function tableInitialSortingState(config, allowedIds = null) {
    const initial = config?.features?.sort?.initial;
    if (!initial || typeof initial !== "object") {
        return [];
    }
    const id = String(initial.field || initial.id || "").trim();
    if (!id) {
        return [];
    }
    if (allowedIds instanceof Set && !allowedIds.has(id)) {
        return [];
    }
    const dir = String(initial.dir || initial.order || "asc").toLowerCase();

    return [{ id, desc: dir === "desc" }];
}

/**
 * Params serveur (`sort` / `order` / `sorts`) alignés sur le tri initial.
 *
 * @param {unknown} config
 * @returns {{ sort: string, order: "asc"|"desc", sorts: { field: string, dir: "asc"|"desc" }[] } | Record<string, never>}
 *
 * @example
 * tableInitialServerSort({ features: { sort: { initial: { field: "level", dir: "asc" } } } })
 * // { sort: "level", order: "asc", sorts: [{ field: "level", dir: "asc" }] }
 */
export function tableInitialServerSort(config) {
    const state = tableInitialSortingState(config);
    if (!state.length) {
        return {};
    }
    const { id, desc } = state[0];
    const dir = desc ? "desc" : "asc";

    return {
        sort: id,
        order: dir,
        sorts: [{ field: id, dir }],
    };
}
