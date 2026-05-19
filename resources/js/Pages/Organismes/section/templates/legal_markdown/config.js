/**
 * Configuration du template LegalMarkdown.
 */
export default {
  name: 'Document legal (Markdown)',
  description: 'Charge un fichier Markdown distant (same-origin) et le rend en HTML.',
  icon: 'fa-solid fa-scale-balanced',
  value: 'legal_markdown',
  supportsAutoSave: true,
  defaultSettings: {},
  parameters: [
    {
      key: 'sourceUrl',
      type: 'text',
      label: 'URL du markdown',
      description: 'URL flux Markdown légal (/legal/…) ou changelog (/changelog/feed/X.Y.Z)',
      default: '/legal/cgu',
    },
    {
      key: 'title',
      type: 'text',
      label: 'Titre optionnel',
      description: 'Titre affiche au-dessus du contenu.',
      default: '',
    },
  ],
  defaultData: {
    sourceUrl: '/legal/cgu',
    title: null,
  },
};
