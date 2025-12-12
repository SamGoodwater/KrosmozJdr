/**
 * Configuration du template Text
 */
export default {
  name: 'Texte',
  description: 'Section de texte riche avec éditeur WYSIWYG. Permet d\'ajouter du contenu formaté, des listes, des liens, etc.',
  icon: 'fa-solid fa-file-lines',
  value: 'text',
  supportsAutoSave: true,
  // Valeurs par défaut pour les settings (paramètres d'affichage)
  defaultSettings: {},
  // Paramètres configurables dans le modal
  parameters: [
    {
      key: 'align',
      type: 'select',
      label: 'Alignement',
      description: 'Alignement du texte dans la section',
      default: 'left',
      options: [
        { value: 'left', label: 'Gauche' },
        { value: 'center', label: 'Centre' },
        { value: 'right', label: 'Droite' },
      ],
    },
    {
      key: 'size',
      type: 'select',
      label: 'Taille du texte',
      description: 'Taille d\'affichage du texte',
      default: 'md',
      options: [
        { value: 'sm', label: 'Petit' },
        { value: 'md', label: 'Moyen' },
        { value: 'lg', label: 'Grand' },
        { value: 'xl', label: 'Très grand' },
      ],
    },
  ],
  // Valeurs par défaut pour les data (contenu)
  defaultData: {
    content: null, // null au lieu de '' pour éviter les problèmes de validation
  },
};

