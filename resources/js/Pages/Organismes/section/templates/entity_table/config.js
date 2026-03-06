/**
 * Configuration du template EntityTable
 *
 * Affiche un tableau d'entités chargé depuis l'API tables, avec filtres optionnels.
 * Les paramètres entity et filters sont stockés dans settings.
 */
export default {
  name: 'Tableau d\'entités',
  description: 'Affiche un tableau d\'entités (sorts, monstres, NPCs, etc.) avec filtres. Les filtres sont passés à l\'API pour afficher une liste filtrée.',
  icon: 'fa-solid fa-table',
  value: 'entity_table',
  supportsAutoSave: true,
  defaultSettings: {
    entity: 'spells',
    filters: {},
  },
  defaultData: {
    entity: null,
    filters: {},
    columns: [],
  },
  parameters: [
    {
      key: 'entity',
      type: 'select',
      label: 'Type d\'entité',
      description: 'Table d\'entités à afficher (sorts, monstres, campagnes, etc.)',
      default: 'spells',
      options: [
        { value: 'spells', label: 'Sorts' },
        { value: 'monsters', label: 'Monstres' },
        { value: 'npcs', label: 'NPCs' },
        { value: 'campaigns', label: 'Campagnes' },
        { value: 'scenarios', label: 'Scénarios' },
        { value: 'shops', label: 'Boutiques' },
        { value: 'breeds', label: 'Classes' },
        { value: 'specializations', label: 'Spécialisations' },
        { value: 'attributes', label: 'Attributs' },
        { value: 'capabilities', label: 'Capacités' },
        { value: 'consumables', label: 'Consommables' },
        { value: 'items', label: 'Objets' },
        { value: 'resources', label: 'Ressources' },
        { value: 'panoplies', label: 'Panoplies' },
      ],
    },
    {
      key: 'filters',
      type: 'textarea',
      label: 'Filtres (JSON)',
      description: 'Objet JSON de filtres passés à l\'API (ex: {"level": "50", "state": "playable"}). Laisser vide ou {} pour aucun filtre.',
      default: '{}',
      placeholder: '{}',
      rows: 4,
    },
    {
      key: 'limit',
      type: 'number',
      label: 'Nombre max d\'entrées',
      description: 'Nombre maximum d\'entités à afficher (1-500)',
      default: 50,
      validation: { min: 1, max: 500 },
    },
  ],
};
