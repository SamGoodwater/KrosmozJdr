/**
 * Configuration du template EntityTable
 */
export default {
  name: 'Tableau d\'entités',
  description: 'Affiche un tableau d\'entités avec filtres et options de tri. Permet de lister et filtrer des entités du jeu.',
  icon: 'fa-solid fa-table',
  value: 'entity_table',
  /**
   * @deprecated
   * Template legacy (ancien système de tableau). Conservé uniquement pour ne pas casser
   * d'anciens contenus ; il n'est plus proposé dans les options UI.
   */
  hidden: true,
  supportsAutoSave: true, // Auto-save activé pour les modifications de configuration
  // Valeurs par défaut pour les settings (paramètres d'affichage)
  defaultSettings: {},
  // Paramètres configurables dans le modal
  parameters: [
    // Aucun paramètre spécifique pour l'instant
    // Les paramètres seront ajoutés selon les besoins
  ],
  // Valeurs par défaut pour les data (contenu)
  defaultData: {
    entity: null,
    filters: {},
    columns: [],
  },
};

