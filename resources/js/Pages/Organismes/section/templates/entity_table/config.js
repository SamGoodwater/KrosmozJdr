/**
 * Configuration du template EntityTable
 */
export default {
  name: 'Tableau d\'entités',
  description: 'Affiche un tableau d\'entités avec filtres et options de tri. Permet de lister et filtrer des entités du jeu.',
  icon: 'fa-solid fa-table',
  value: 'entity_table',
  supportsAutoSave: true, // Auto-save activé pour les modifications de configuration
  // Valeurs par défaut pour les settings (paramètres d'affichage)
  defaultSettings: {},
  // Valeurs par défaut pour les data (contenu)
  defaultData: {
    entity: null,
    filters: {},
    columns: [],
  },
};

