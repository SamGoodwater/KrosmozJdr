/**
 * Attributes TanStackTable config (Table v2)
 *
 * @description
 * Config front (Approche A) : colonnes + features.
 * Le backend fournit les cellules typées `Cell{type,value,params}`.
 */

export function createAttributesTanStackTableConfig() {
    return {
        id: "attributes.index",
        ui: { skeletonRows: 10 },
        features: {
            search: { enabled: true, placeholder: "Rechercher un attribut…", debounceMs: 200 },
            filters: { enabled: true },
            pagination: { enabled: true, perPage: { default: 25, options: [10, 25, 50, 100] } },
            selection: { enabled: true, checkboxMode: "auto", clickToSelect: true },
            columnVisibility: { enabled: true, persist: true },
            export: { csv: true, filename: "attributes.csv" },
        },
        columns: [
            { id: "id", label: "ID", hideable: true, defaultHidden: true, sort: { enabled: true }, cell: { type: "text" }, permissions: { ability: "createAny" } },
            { id: "created_at", label: "Créé le", hideable: true, defaultHidden: true, sort: { enabled: true }, cell: { type: "text" }, permissions: { ability: "createAny" } },
            { id: "updated_at", label: "Modifié le", hideable: true, defaultHidden: true, sort: { enabled: true }, cell: { type: "text" }, permissions: { ability: "createAny" } },
            { id: "name", label: "Nom", isMain: true, hideable: false, sort: { enabled: true }, search: { enabled: true }, cell: { type: "route" } },
            { id: "description", label: "Description", hideable: true, sort: { enabled: true }, search: { enabled: true }, cell: { type: "text" } },
            { id: "usable", label: "Utilisable", hideable: true, defaultHidden: true, sort: { enabled: true }, filter: { id: "usable", type: "boolean" }, cell: { type: "badge" } },
            { id: "is_visible", label: "Visible", sort: { enabled: true }, filter: { id: "is_visible", type: "select" }, cell: { type: "badge" } },
            { id: "created_by", label: "Créé par", hideable: true, defaultHidden: true, sort: { enabled: true }, search: { enabled: true }, cell: { type: "text" }, permissions: { ability: "createAny" } },
        ],
    };
}

export default createAttributesTanStackTableConfig;


