/**
 * Classes TanStackTable config (Table v2)
 *
 * @description
 * Config front (Approche A) : colonnes + features.
 * Le backend fournit les cellules typées `Cell{type,value,params}`.
 */

export function createClassesTanStackTableConfig() {
    return {
        id: "classes.index",
        ui: { skeletonRows: 10 },
        features: {
            search: { enabled: true, placeholder: "Rechercher une classe…", debounceMs: 200 },
            filters: { enabled: true },
            pagination: { enabled: true, perPage: { default: 25, options: [10, 25, 50, 100] } },
            selection: { enabled: true, checkboxMode: "auto", clickToSelect: true },
            columnVisibility: { enabled: true, persist: true },
            export: { csv: true, filename: "classes.csv" },
        },
        columns: [
            { id: "id", label: "ID", hideable: true, defaultHidden: true, sort: { enabled: true }, cell: { type: "text" }, permissions: { ability: "createAny" } },
            { id: "created_at", label: "Créé le", hideable: true, defaultHidden: true, sort: { enabled: true }, cell: { type: "text" }, permissions: { ability: "createAny" } },
            { id: "updated_at", label: "Modifié le", hideable: true, defaultHidden: true, sort: { enabled: true }, cell: { type: "text" }, permissions: { ability: "createAny" } },
            { id: "name", label: "Nom", isMain: true, hideable: false, sort: { enabled: true }, search: { enabled: true }, cell: { type: "route" } },
            { id: "life", label: "Vie", sort: { enabled: true }, cell: { type: "text" } },
            { id: "life_dice", label: "Vie dé", sort: { enabled: true }, cell: { type: "text" } },
            { id: "specificity", label: "Spécificité", hideable: true, sort: { enabled: true }, search: { enabled: true }, cell: { type: "text" } },
            { id: "dofusdb_id", label: "DofusDB ID", hideable: true, defaultHidden: true, sort: { enabled: true }, search: { enabled: true }, cell: { type: "text" }, permissions: { ability: "updateAny" } },
            { id: "created_by", label: "Créé par", hideable: true, defaultHidden: true, sort: { enabled: true }, search: { enabled: true }, cell: { type: "text" }, permissions: { ability: "createAny" } },
        ],
    };
}

export default createClassesTanStackTableConfig;


