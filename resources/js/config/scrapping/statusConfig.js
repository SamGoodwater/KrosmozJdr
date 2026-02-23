/**
 * Config des statuts d’affichage par ligne (recherche, conversion, simulation, import).
 * Une seule source de vérité pour les libellés et couleurs des badges.
 * @see docs/50-Fonctionnalités/Scrapping/SPEC_UI_SCRAPPING.md
 */

/** Libellés d’état pour l’affichage (Badge). */
export const STATUS_LABELS = {
    recherché: "Recherché",
    simulé: "Simulé",
    "simulation en cours": "Simulation…",
    "simulation erreur": "Simulation erreur",
    "importation programmée": "Import programmé",
    "importation en cours": "Import en cours",
    importé: "Importé",
    "conversion en cours": "Conversion…",
    converti: "Converti",
    erreur: "Erreur",
};

/** Couleur Badge par statut (Tailwind pour contraste : bg-xxx). */
export const STATUS_COLORS = {
    recherché: "neutral-300",
    simulé: "blue-600",
    "simulation en cours": "amber-500",
    "simulation erreur": "red-600",
    "importation programmée": "neutral-300",
    "importation en cours": "amber-500",
    importé: "green-600",
    "conversion en cours": "amber-500",
    converti: "green-600",
    erreur: "red-600",
};

/** Statuts terminaux : ne pas les écraser par « Recherché » lors d’une nouvelle recherche. */
export const TERMINAL_STATUSES = new Set([
    "simulé",
    "simulation erreur",
    "importé",
    "erreur",
]);
