/**
 * usePropertyDisplay — config pour PropertyDisplay
 *
 * @description
 * Construit la config { property, value } pour PropertyDisplay à partir
 * de fieldKey, tableMeta, descriptors et entité/cell.
 * Priorise les caractéristiques BDD (icône, couleur, description).
 *
 * @example
 * const { property, value } = usePropertyDisplayConfig({
 *   fieldKey: 'pa',
 *   tableMeta: props.tableMeta,
 *   descriptors: descriptors.value,
 *   entityType: 'spell',
 *   cell: getCell('pa'),
 * });
 * <PropertyDisplay :property="property" :value="value" variant="badge" />
 */
import { computed } from 'vue';
import { resolveEntityFieldUi } from '@/Utils/Entity/entity-view-ui';

/**
 * @param {Object} options
 * @param {string} options.fieldKey
 * @param {Object} options.tableMeta
 * @param {Object} options.descriptors
 * @param {string} options.entityType
 * @param {Object} [options.cell]
 * @returns {{ property: Object, value: any }}
 */
export function usePropertyDisplayConfig(options = {}) {
  const { fieldKey, tableMeta, descriptors, entityType, cell } = options;

  const property = computed(() =>
    resolveEntityFieldUi({
      fieldKey,
      descriptors: descriptors || {},
      tableMeta: tableMeta || {},
      entityType: entityType || '',
    }),
  );

  const value = computed(() => cell?.value ?? null);

  return { property, value };
}
