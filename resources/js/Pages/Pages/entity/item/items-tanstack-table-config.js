/**
 * Items TanStackTable config (Table v2)
 *
 * @description
 * Config front (Approche A) : colonnes + features.
 * Le backend fournit les cellules typées `Cell{type,value,params}`.
 */

export function createItemsTanStackTableConfig({ selectionEnabled = true } = {}) {
    return {
        id: "items.index",
        ui: { skeletonRows: 10 },
        features: {
            search: { enabled: true, placeholder: "Rechercher un objet…", debounceMs: 200 },
            filters: { enabled: true },
            pagination: { enabled: true, perPage: { default: 25, options: [10, 25, 50, 100] } },
            selection: { enabled: Boolean(selectionEnabled), checkboxMode: "auto", clickToSelect: true },
            columnVisibility: { enabled: true, persist: true },
            export: { csv: true, filename: "items.csv" },
        },
        columns: [
            { id: "id", label: "ID", hideable: true, defaultHidden: true, sort: { enabled: true }, cell: { type: "text" }, permissions: { ability: "createAny" } },
            { id: "created_at", label: "Créé le", hideable: true, defaultHidden: true, sort: { enabled: true }, cell: { type: "text" }, permissions: { ability: "createAny" } },
            { id: "updated_at", label: "Modifié le", hideable: true, defaultHidden: true, sort: { enabled: true }, cell: { type: "text" }, permissions: { ability: "createAny" } },
            { id: "name", label: "Nom", isMain: true, hideable: false, sort: { enabled: true }, search: { enabled: true }, cell: { type: "route" } },
            { id: "level", label: "Niveau", sort: { enabled: true }, filter: { id: "level", type: "select" }, cell: { type: "text" } },
            { id: "rarity", label: "Rareté", sort: { enabled: true }, filter: { id: "rarity", type: "select" }, cell: { type: "badge" } },
            { id: "item_type", label: "Type", sort: { enabled: true }, filter: { id: "item_type_id", type: "select" }, cell: { type: "text" } },
            { id: "dofusdb_id", label: "DofusDB", hideable: true, defaultHidden: true, sort: { enabled: true }, cell: { type: "route" }, permissions: { ability: "updateAny" } },
            { id: "created_by", label: "Créé par", hideable: true, defaultHidden: true, sort: { enabled: true }, cell: { type: "text" }, permissions: { ability: "createAny" } },
        ],
    };
}

export default createItemsTanStackTableConfig;


