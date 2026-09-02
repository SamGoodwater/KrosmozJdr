/**
 * Template « Catalogue de téléchargements » : fichiers du config/game_downloads.php.
 *
 * @see SectionDownloadCatalogRead.vue
 */
export default {
    name: "Catalogue de téléchargements",
    description:
        "Liste les fichiers téléchargeables (livre de règles, fiches, logo) d’après le catalogue.",
    icon: "fa-solid fa-download",
    value: "download_catalog",
    supportsAutoSave: true,
    defaultSettings: {
        groups: [],
    },
    defaultData: {},
    parameters: [
        {
            key: "groups",
            type: "text",
            label: "Groupes (vide = tous)",
            default: "",
        },
    ],
};
