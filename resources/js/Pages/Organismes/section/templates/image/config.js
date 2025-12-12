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
    zoom: 100,
    lazyLoad: false,
  },
  // Paramètres configurables dans le modal
  parameters: [
    {
      key: 'align',
      type: 'select',
      label: 'Alignement',
      description: 'Position de l\'image dans la section',
      default: 'center',
      options: [
        { value: 'left', label: 'Gauche' },
        { value: 'center', label: 'Centre' },
        { value: 'right', label: 'Droite' },
      ],
    },
    {
      key: 'size',
      type: 'select',
      label: 'Taille',
      description: 'Taille d\'affichage de l\'image',
      default: 'md',
      options: [
        { value: 'sm', label: 'Petit' },
        { value: 'md', label: 'Moyen' },
        { value: 'lg', label: 'Grand' },
        { value: 'xl', label: 'Très grand' },
        { value: 'full', label: 'Pleine largeur' },
      ],
    },
    {
      key: 'zoom',
      type: 'number',
      label: 'Zoom',
      description: 'Niveau de zoom de l\'image en pourcentage (10% à 500%)',
      default: 100,
      validation: {
        min: 10,
        max: 500,
      },
      suffix: '%',
    },
    {
      key: 'lazyLoad',
      type: 'toggle',
      label: 'Chargement différé',
      description: 'Charger l\'image uniquement quand elle est visible à l\'écran',
      default: false,
    },
  ],
  // Valeurs par défaut pour les data (contenu)
  defaultData: {
    src: null,
    alt: null,
    caption: null,
  },
};

