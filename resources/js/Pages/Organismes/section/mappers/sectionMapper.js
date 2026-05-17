/**
 * Point d’entrée Pages pour mapper les sections (délègue au SectionMapper métier).
 *
 * @example
 * import { mapToSectionModel } from '@/Pages/Organismes/section/mappers/sectionMapper';
 * const model = mapToSectionModel(raw);
 */

import { SectionMapper } from '@/Utils/Services/Mappers';

/**
 * @param {Object|null} raw
 * @returns {import('@/Models/Section').Section|null}
 */
export function mapToSectionModel(raw) {
  return SectionMapper.mapToModel(raw);
}

/**
 * @param {Array|null|undefined} list
 * @returns {Array<import('@/Models/Section').Section>}
 */
export function mapToSectionModels(list) {
  return SectionMapper.mapToModels(list);
}

export default { mapToSectionModel, mapToSectionModels };
