/**
 * resolveEntityViewComponent — Résout le composant Vue pour une vue d'entité
 *
 * @description
 * Retourne le composant Vue approprié pour une vue d'entité spécifique.
 * Utilise des imports dynamiques pour charger les composants selon le type d'entité et la vue.
 *
 * @param {string} entityType - Type d'entité (ex: 'resource', 'item', 'spell')
 * @param {string} view - Vue demandée ('full', 'minimal', 'text', 'line', …)
 * @returns {Promise<Component>} Composant Vue chargé dynamiquement
 *
 * @example
 * const component = await resolveEntityViewComponent('resource', 'full');
 * // Retourne ResourceViewFull.vue
 */
import { normalizeEntityType } from '@/Entities/entity-registry';

/**
 * Mappe les types d'entités vers leurs noms de composants
 */
const ENTITY_COMPONENT_MAP = {
  'resources': 'Resource',
  'items': 'Item',
  'consumables': 'Consumable',
  'spells': 'Spell',
  'monsters': 'Monster',
  'npcs': 'Npc',
  'breeds': 'Breed',
  'campaigns': 'Campaign',
  'scenarios': 'Scenario',
  'conditions': 'Condition',
  'panoplies': 'Panoply',
  'capabilities': 'Capability',
  'creature-traits': 'CreatureTrait',
  'specializations': 'Specialization',
  'resource-types': 'ResourceType',
  'shops': 'Shop',
};

/**
 * Mappe les vues vers leurs noms de composants
 */
const VIEW_COMPONENT_MAP = {
  'full': 'ViewFull',
  'minimal': 'ViewMinimal',
  'text': 'ViewText',
  /** Vue liste dense (SpellLineRow, ResourceLineRow, …) */
  'line': 'LineRow',
};

/**
 * Résout et charge dynamiquement le composant Vue pour une vue d'entité
 *
 * @param {string} entityType - Type d'entité (normalisé)
 * @param {string} view - Vue demandée ('full', 'minimal', 'text', …)
 * @returns {Promise<Component>} Composant Vue chargé dynamiquement
 */
export async function resolveEntityViewComponent(entityType, view = 'full') {
  const normalizedType = normalizeEntityType(entityType);
  const entityName = ENTITY_COMPONENT_MAP[normalizedType];
  const viewName = VIEW_COMPONENT_MAP[view] || VIEW_COMPONENT_MAP['full'];

  if (!entityName) {
    console.warn(`[resolveEntityViewComponent] Type d'entité non reconnu: ${entityType} (normalisé: ${normalizedType})`);
    return null;
  }

  const componentName = `${entityName}${viewName}`;
  const folderName = entityName
    .replace(/([a-z0-9])([A-Z])/g, "$1-$2")
    .toLowerCase();
  const componentPath = `@/Pages/Molecules/entity/${folderName}/${componentName}.vue`;

  const components = import.meta.glob('@/Pages/Molecules/entity/**/*{View,Edit}*.vue');
  const lineRowComponents = import.meta.glob('@/Pages/Molecules/entity/**/*LineRow.vue');
  const mergedGlobs = { ...components, ...lineRowComponents };

  for (const [path, importFn] of Object.entries(mergedGlobs)) {
    if (path.includes(`/${folderName}/`) && path.includes(componentName)) {
      try {
        const module = await importFn();
        return module.default || module[componentName] || module;
      } catch (error) {
        console.error(`[resolveEntityViewComponent] Erreur lors du chargement du composant ${path}:`, error);
        return null;
      }
    }
  }

  console.warn(`[resolveEntityViewComponent] Composant non trouvé: ${componentPath}`);
  return null;
}

/**
 * Résout et charge de manière synchrone (eager) le composant Vue pour une vue d'entité
 *
 * @param {string} entityType - Type d'entité (normalisé)
 * @param {string} view - Vue demandée ('full', 'minimal', 'text', …)
 * @returns {Component|null} Composant Vue ou null si non trouvé
 */
export function resolveEntityViewComponentSync(entityType, view = 'full') {
  const normalizedType = normalizeEntityType(entityType);
  const entityName = ENTITY_COMPONENT_MAP[normalizedType];
  const viewName = VIEW_COMPONENT_MAP[view] || VIEW_COMPONENT_MAP['full'];

  if (!entityName) {
    console.warn(`[resolveEntityViewComponentSync] Type d'entité non reconnu: ${entityType} (normalisé: ${normalizedType})`);
    return null;
  }

  const componentName = `${entityName}${viewName}`;
  const folderName = entityName
    .replace(/([a-z0-9])([A-Z])/g, "$1-$2")
    .toLowerCase();
  const componentPath = `@/Pages/Molecules/entity/${folderName}/${componentName}.vue`;

  const components = import.meta.glob('@/Pages/Molecules/entity/**/*{View,Edit}*.vue', { eager: true });
  const lineRowComponents = import.meta.glob('@/Pages/Molecules/entity/**/*LineRow.vue', { eager: true });
  const genericComponents = import.meta.glob('@/Pages/Molecules/entity/Entity{View,Edit}*.vue', { eager: true });

  const allComponents = { ...components, ...lineRowComponents, ...genericComponents };

  for (const [path, module] of Object.entries(allComponents)) {
    if (path.includes(`/${folderName}/`) && path.includes(componentName)) {
      return module.default || module[componentName] || module;
    }
  }

  console.warn(`[resolveEntityViewComponentSync] Composant non trouvé: ${componentPath}`);
  return null;
}
