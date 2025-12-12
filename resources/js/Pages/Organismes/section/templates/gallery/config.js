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
  // Paramètres configurables dans le modal
  parameters: [
    {
      key: 'columns',
      type: 'select',
      label: 'Colonnes',
      description: 'Nombre de colonnes dans la grille',
      default: 3,
      options: [
        { value: 2, label: '2 colonnes' },
        { value: 3, label: '3 colonnes' },
        { value: 4, label: '4 colonnes' },
      ],
    },
    {
      key: 'gap',
      type: 'select',
      label: 'Espacement',
      description: 'Espacement entre les images',
      default: 'md',
      options: [
        { value: 'sm', label: 'Petit' },
        { value: 'md', label: 'Moyen' },
        { value: 'lg', label: 'Grand' },
      ],
    },
  ],
  // Valeurs par défaut pour les data (contenu)
  defaultData: {
    images: [],
  },
};

