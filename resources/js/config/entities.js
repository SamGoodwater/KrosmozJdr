/**
 * Config entités — URL icône, couleur et label par type d'entité
 *
 * Aligné sur resources/scss/themes/_theme-entities.scss (clés et couleurs).
 * Icônes : storage/app/public/images/icons/entities/ (servi via /storage/images/icons/entities/).
 * Format : WebP (.webp).
 *
 * @example
 * import { getEntityIconUrl, getEntityConfig, getEntityColor } from '@/config/entities';
 * const url = getEntityIconUrl('npc');
 * const { iconUrl, color, label } = getEntityConfig('spell');
 */

const ICON_BASE = '/storage/images/icons/entities';
const ICON_EXT = '.webp';

/**
 * Config par clé d'entité : couleur (token DaisyUI) et label (affichage).
 * Couleurs synchronisées avec $theme-entities.
 */
const ENTITY_CONFIG = {
  section: { color: 'zinc', label: 'Section' },
  page: { color: 'zinc', label: 'Page' },
  npc: { color: 'green', label: 'PNJ' },
  item: { color: 'indigo', label: 'Équipement' },
  creature: { color: 'emerald', label: 'Créature' },
  shop: { color: 'amber', label: 'Commerce' },
  campaign: { color: 'stone', label: 'Campagne' },
  resource: { color: 'sky', label: 'Ressource' },
  monster: { color: 'pink', label: 'Monstre' },
  specialization: { color: 'lime', label: 'Spécialisation' },
  spell: { color: 'violet', label: 'Sort' },
  user: { color: 'blue', label: 'Utilisateur' },
  attribute: { color: 'yellow', label: 'Attribut' },
  capitalize: { color: 'slate', label: 'Capital' },
  breed: { color: 'cyan', label: 'Race' },
  consumable: { color: 'orange', label: 'Consommable' },
  scenario: { color: 'neutral', label: 'Scénario' },
  condition: { color: 'red', label: 'Condition' },
};

/** Clés d'entité reconnues (alignées sur $theme-entities). */
export const ENTITY_KEYS = Object.keys(ENTITY_CONFIG);

/**
 * Retourne la config complète pour une entité (iconUrl, color, label).
 * @param {string} entityKey - Clé d'entité (ex. 'npc', 'spell')
 * @returns {{ iconUrl: string, color: string, label: string }}
 */
export function getEntityConfig(entityKey) {
  const key = typeof entityKey === 'string' ? entityKey.trim().toLowerCase() : '';
  const config = ENTITY_CONFIG[key];
  if (config) {
    return {
      iconUrl: `${ICON_BASE}/${key}${ICON_EXT}`,
      color: config.color,
      label: config.label,
    };
  }
  return {
    iconUrl: '',
    color: 'neutral',
    label: entityKey || 'Entité',
  };
}

/**
 * Retourne l'URL de l'icône pour une entité.
 * @param {string} entityKey - Clé d'entité
 * @returns {string}
 */
export function getEntityIconUrl(entityKey) {
  return getEntityConfig(entityKey).iconUrl;
}

/**
 * Retourne le nom de couleur DaisyUI pour une entité.
 * @param {string} entityKey - Clé d'entité
 * @returns {string}
 */
export function getEntityColor(entityKey) {
  return getEntityConfig(entityKey).color;
}
