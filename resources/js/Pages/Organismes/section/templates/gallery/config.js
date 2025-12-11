/**
 * Configuration du template Gallery
 */
export default {
  name: 'Galerie',
  description: 'Galerie d\'images avec éditeur intégré. Permet d\'ajouter plusieurs images dans une grille personnalisable.',
  icon: 'fa-solid fa-images',
  value: 'gallery',
  supportsAutoSave: true,
  // Valeurs par défaut pour les settings (paramètres d'affichage)
  defaultSettings: {
    columns: 3,
    gap: 'md',
  },
  // Valeurs par défaut pour les data (contenu)
  defaultData: {
    images: [],
  },
};

