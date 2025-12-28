/**
 * Monsters TanStackTable config (Table v2)
 *
 * @description
 * Config front (Approche A) : colonnes + features.
 * Le backend fournit les cellules typées `Cell{type,value,params}`.
 */

export function createMonstersTanStackTableConfig() {
    return {
        id: "monsters.index",
        ui: { skeletonRows: 10 },
        features: {
            search: { enabled: true, placeholder: "Rechercher un monstre…", debounceMs: 200 },
            filters: { enabled: true },
            pagination: { enabled: true, perPage: { default: 25, options: [10, 25, 50, 100] } },
            selection: { enabled: true, checkboxMode: "auto", clickToSelect: true },
            columnVisibility: { enabled: true, persist: true },
            export: { csv: true, filename: "monsters.csv" },
        },
        columns: [
            { id: "id", label: "ID", hideable: true, defaultHidden: true, sort: { enabled: true }, cell: { type: "text" }, permissions: { ability: "createAny" } },
            { id: "created_at", label: "Créé le", hideable: true, defaultHidden: true, sort: { enabled: true }, cell: { type: "text" }, permissions: { ability: "createAny" } },
            { id: "updated_at", label: "Modifié le", hideable: true, defaultHidden: true, sort: { enabled: true }, cell: { type: "text" }, permissions: { ability: "createAny" } },
            { id: "creature_name", label: "Nom", isMain: true, hideable: false, sort: { enabled: true }, search: { enabled: true }, cell: { type: "route" } },
            { id: "monster_race", label: "Race", sort: { enabled: true }, search: { enabled: true }, cell: { type: "text" } },
            { id: "size", label: "Taille", sort: { enabled: true }, filter: { id: "size", type: "multi" }, cell: { type: "text" } },
            { id: "is_boss", label: "Boss", sort: { enabled: true }, filter: { id: "is_boss", type: "boolean" }, cell: { type: "badge" } },
            { id: "dofusdb_id", label: "DofusDB ID", hideable: true, defaultHidden: true, sort: { enabled: true }, search: { enabled: true }, cell: { type: "text" }, permissions: { ability: "updateAny" } },
        ],
    };
}

export default createMonstersTanStackTableConfig;


