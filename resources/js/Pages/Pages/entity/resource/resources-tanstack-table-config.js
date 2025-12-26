/**
 * Resources TanStackTable config (Table v2)
 *
 * @description
 * Config front (Approche A) : colonnes + features.
 * Le backend fournit les cellules typées `Cell{type,value,params}`.
 *
 * NOTE:
 * - Les `id` des colonnes doivent correspondre aux `cells` renvoyées par l'API table.
 */

export function createResourcesTanStackTableConfig() {
    return {
        id: "resources.index",
        ui: {
            skeletonRows: 10,
        },
        features: {
            search: {
                enabled: true,
                placeholder: "Rechercher une ressource…",
                debounceMs: 200,
            },
            filters: {
                enabled: true,
            },
            pagination: {
                enabled: true,
                perPage: { default: 25, options: [10, 25, 50, 100] },
            },
            selection: {
                enabled: true, // sera ajusté par le wrapper selon permissions (updateAny)
                checkboxMode: "auto",
                clickToSelect: true,
            },
            columnVisibility: {
                enabled: true,
                persist: true,
            },
            export: {
                csv: true,
                filename: "resources.csv",
            },
        },
        // Options de filtres peuvent venir de l'API (`meta.filterOptions`) via wrapper
        columns: [
            { id: "id", label: "ID", hideable: true, defaultHidden: true, sort: { enabled: true }, cell: { type: "text" }, permissions: { ability: "createAny" } },
            { id: "created_at", label: "Créé le", hideable: true, defaultHidden: true, sort: { enabled: true }, cell: { type: "text" }, permissions: { ability: "createAny" } },
            { id: "updated_at", label: "Modifié le", hideable: true, defaultHidden: true, sort: { enabled: true }, cell: { type: "text" }, permissions: { ability: "createAny" } },
            {
                id: "image",
                label: "Image",
                hideable: true,
                cell: { type: "image" },
            },
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
                id: "level",
                label: "Niveau",
                sort: { enabled: true },
                filter: { id: "level", type: "select" },
                cell: { type: "text" },
            },
            {
                id: "resource_type",
                label: "Type",
                sort: { enabled: true },
                filter: { id: "resource_type_id", type: "select" },
                cell: { type: "text" },
            },
            {
                id: "rarity",
                label: "Rareté",
                sort: { enabled: true },
                filter: { id: "rarity", type: "select" },
                cell: { type: "badge" },
            },
            {
                id: "price",
                label: "Prix",
                sort: { enabled: true },
                cell: { type: "text" },
            },
            {
                id: "weight",
                label: "Poids",
                sort: { enabled: true },
                cell: { type: "text" },
            },
            {
                id: "usable",
                label: "Utilisable",
                hideable: true,
                defaultHidden: true,
                sort: { enabled: true },
                filter: { id: "usable", type: "select" },
                cell: { type: "badge" },
            },
            {
                id: "auto_update",
                label: "Auto-update",
                hideable: true,
                defaultHidden: true,
                sort: { enabled: true },
                filter: { id: "auto_update", type: "select" },
                cell: { type: "badge" },
                permissions: { ability: "updateAny" },
            },
            {
                id: "dofusdb_id",
                label: "DofusDB",
                hideable: true,
                defaultHidden: true,
                sort: { enabled: true },
                cell: { type: "route" },
                permissions: { ability: "updateAny" },
            },
            {
                id: "created_by",
                label: "Créé par",
                hideable: true,
                defaultHidden: true,
                sort: { enabled: true },
                cell: { type: "text" },
                // Colonne technique: visible seulement pour les users pouvant créer l'entité.
                permissions: { ability: "createAny" },
            },
        ],
    };
}

export default createResourcesTanStackTableConfig;


