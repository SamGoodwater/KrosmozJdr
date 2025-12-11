/**
 * Configuration du template Video
 */
export default {
  name: 'Vidéo',
  description: 'Affiche une vidéo (YouTube, Vimeo ou fichier direct). Permet d\'intégrer des vidéos avec contrôles personnalisables.',
  icon: 'fa-solid fa-video',
  value: 'video',
  supportsAutoSave: true, // Auto-save activé pour les modifications de données
  // Valeurs par défaut pour les settings (paramètres d'affichage)
  defaultSettings: {
    autoplay: false,
    controls: true,
  },
  // Valeurs par défaut pour les data (contenu)
  defaultData: {
    src: null,
    type: 'youtube',
  },
};

