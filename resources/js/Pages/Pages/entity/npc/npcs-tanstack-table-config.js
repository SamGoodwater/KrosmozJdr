/**
 * NPCs TanStackTable config (Table v2)
 *
 * @description
 * Config front (Approche A) : colonnes + features.
 * Le backend fournit les cellules typées `Cell{type,value,params}`.
 */

export function createNpcsTanStackTableConfig() {
    return {
        id: "npcs.index",
        ui: { skeletonRows: 10 },
        features: {
            search: { enabled: true, placeholder: "Rechercher un NPC…", debounceMs: 200 },
            filters: { enabled: true },
            pagination: { enabled: true, perPage: { default: 25, options: [10, 25, 50, 100] } },
            selection: { enabled: true, checkboxMode: "auto", clickToSelect: true },
            columnVisibility: { enabled: true, persist: true },
            export: { csv: true, filename: "npcs.csv" },
        },
        columns: [
            { id: "id", label: "ID", hideable: true, defaultHidden: true, sort: { enabled: true }, cell: { type: "text" }, permissions: { ability: "createAny" } },
            { id: "created_at", label: "Créé le", hideable: true, defaultHidden: true, sort: { enabled: true }, cell: { type: "text" }, permissions: { ability: "createAny" } },
            { id: "updated_at", label: "Modifié le", hideable: true, defaultHidden: true, sort: { enabled: true }, cell: { type: "text" }, permissions: { ability: "createAny" } },
            { id: "creature_name", label: "Nom", isMain: true, hideable: false, sort: { enabled: true }, search: { enabled: true }, cell: { type: "route" } },
            { id: "classe", label: "Classe", sort: { enabled: true }, search: { enabled: true }, cell: { type: "text" } },
            { id: "specialization", label: "Spécialisation", sort: { enabled: true }, search: { enabled: true }, cell: { type: "text" } },
        ],
    };
}

export default createNpcsTanStackTableConfig;


