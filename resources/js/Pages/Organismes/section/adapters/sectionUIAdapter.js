/**
 * Données UI synchrones pour une section (sans composant).
 * Réutilise useSectionUI pour une seule source de vérité.
 *
 * @example
 * import { adaptSectionToUI } from '@/Pages/Organismes/section/adapters/sectionUIAdapter';
 * const ui = adaptSectionToUI(rawSection);
 */

import { useSectionUI } from '../composables/useSectionUI';

/**
 * @param {Object|import('@/Models/Section').Section|null} section
 * @returns {Object} Objet uiData (couleurs, badge, url, metadata…)
 */
export function adaptSectionToUI(section) {
  const { uiData } = useSectionUI(section);
  return uiData.value;
}

export default adaptSectionToUI;
