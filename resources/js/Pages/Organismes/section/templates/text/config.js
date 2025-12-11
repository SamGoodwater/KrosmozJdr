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
  // Valeurs par défaut pour les data (contenu)
  defaultData: {
    content: null, // null au lieu de '' pour éviter les problèmes de validation
  },
};

