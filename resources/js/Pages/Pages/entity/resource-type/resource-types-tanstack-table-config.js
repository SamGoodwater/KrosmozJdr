/**
 * Resource Types TanStackTable config (Table v2)
 *
 * @description
 * Config front (Approche A) : colonnes + features.
 * Le backend fournit les cellules typées `Cell{type,value,params}`.
 */

export function createResourceTypesTanStackTableConfig() {
    return {
        id: "resource-types.index",
        ui: {
            skeletonRows: 10,
        },
        features: {
            search: {
                enabled: true,
                placeholder: "Rechercher un type…",
                debounceMs: 200,
            },
            filters: { enabled: true },
            pagination: {
                enabled: true,
                perPage: { default: 25, options: [10, 25, 50, 100] },
            },
            selection: {
                enabled: true, // activée seulement si updateAny (wrapper)
                checkboxMode: "auto",
                clickToSelect: true,
            },
            columnVisibility: {
                enabled: true,
                persist: true,
            },
            export: {
                csv: true,
                filename: "resource-types.csv",
            },
        },
        columns: [
            { id: "id", label: "ID", hideable: true, defaultHidden: true, sort: { enabled: true }, cell: { type: "text" }, permissions: { ability: "createAny" } },
            { id: "created_at", label: "Créé le", hideable: true, defaultHidden: true, sort: { enabled: true }, cell: { type: "text" }, permissions: { ability: "createAny" } },
            { id: "updated_at", label: "Modifié le", hideable: true, defaultHidden: true, sort: { enabled: true }, cell: { type: "text" }, permissions: { ability: "createAny" } },
            {
                id: "name",
                label: "Nom",
                isMain: true,
                hideable: false,
                sort: { enabled: true },
                search: { enabled: true },
                cell: { type: "route" },
            },
            {
                id: "dofusdb_type_id",
                label: "DofusDB typeId",
                hideable: true,
                defaultHidden: true,
                sort: { enabled: true },
                search: { enabled: true },
                cell: { type: "text" },
                permissions: { ability: "updateAny" },
            },
            {
                id: "decision",
                label: "Statut",
                sort: { enabled: true },
                filter: { id: "decision", type: "multi" },
                cell: { type: "badge" },
            },
            {
                id: "seen_count",
                label: "Détections",
                sort: { enabled: true },
                cell: { type: "text" },
            },
            {
                id: "last_seen_at",
                label: "Dernière détection",
                sort: { enabled: true },
                cell: { type: "text" },
            },
            {
                id: "resources_count",
                label: "Ressources",
                sort: { enabled: true },
                cell: { type: "text" },
            },
        ],
    };
}

export default createResourceTypesTanStackTableConfig;


