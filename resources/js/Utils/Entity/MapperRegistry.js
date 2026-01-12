/**
 * MapperRegistry — Registre centralisé des mappers par entityType
 * 
 * @description
 * Source de vérité unique pour les mappers utilisés dans les opérations bulk.
 * Les mappers doivent implémenter la méthode `fromBulkForm(bulkFormData)`.
 * 
 * @example
 * import { MAPPER_REGISTRY } from '@/Utils/Entity/MapperRegistry';
 * const mapper = MAPPER_REGISTRY['resources'];
 * if (mapper && typeof mapper.fromBulkForm === 'function') {
 *   const mappedData = mapper.fromBulkForm(bulkFormData);
 * }
 */

// Import du mapper Resource (seul mapper existant pour l'instant)
import { ResourceMapper } from '@/Mappers/Entity/ResourceMapper';

/**
 * Registre des mappers par entityType
 * @type {Object<string, {fromBulkForm: function}>}
 */
export const MAPPER_REGISTRY = {
  'resources': ResourceMapper,
  'resource': ResourceMapper,
  // Ajouter d'autres mappers ici au fur et à mesure de leur migration
  // Exemple :
  // 'items': ItemMapper,
  // 'spells': SpellMapper,
};

/**
 * Récupère le mapper pour un type d'entité donné
 * 
 * @param {string} entityType - Type d'entité (ex: 'resources', 'items')
 * @returns {{fromBulkForm: function}|null} Le mapper ou null si non trouvé
 */
export function getMapperForEntityType(entityType) {
  if (!entityType) return null;
  return MAPPER_REGISTRY[entityType] || null;
}
