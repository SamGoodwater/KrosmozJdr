/**
 * Configuration du template Image
 */
export default {
  name: 'Image',
  description: 'Affiche une image unique avec légende optionnelle. Permet d\'uploader et de configurer l\'affichage d\'une image.',
  icon: 'fa-solid fa-image',
  value: 'image',
  supportsAutoSave: true, // Auto-save activé pour les modifications de données
  // Valeurs par défaut pour les settings (paramètres d'affichage)
  defaultSettings: {
    align: 'center',
    size: 'md',
  },
  // Valeurs par défaut pour les data (contenu)
  defaultData: {
    src: null,
    alt: null,
    caption: null,
  },
};

