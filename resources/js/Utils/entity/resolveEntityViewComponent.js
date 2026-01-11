/**
 * resolveEntityViewComponent — Résout le composant Vue pour une vue d'entité
 *
 * @description
 * Retourne le composant Vue approprié pour une vue d'entité spécifique.
 * Utilise des imports dynamiques pour charger les composants selon le type d'entité et la vue.
 *
 * @param {string} entityType - Type d'entité (ex: 'resource', 'item', 'spell')
 * @param {string} view - Vue demandée ('large', 'compact', 'minimal', 'text')
 * @returns {Promise<Component>} Composant Vue chargé dynamiquement
 *
 * @example
 * const component = await resolveEntityViewComponent('resource', 'large');
 * // Retourne ResourceViewLarge.vue
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
  'creatures': 'Creature',
  'npcs': 'Npc',
  'classes': 'Classe',
  'campaigns': 'Campaign',
  'scenarios': 'Scenario',
  'attributes': 'Attribute',
  'panoplies': 'Panoply',
  'capabilities': 'Capability',
  'specializations': 'Specialization',
  'shops': 'Shop',
};

/**
 * Mappe les vues vers leurs noms de composants
 */
const VIEW_COMPONENT_MAP = {
  'large': 'ViewLarge',
  'compact': 'ViewCompact',
  'minimal': 'ViewMinimal',
  'text': 'ViewText',
};

/**
 * Résout et charge dynamiquement le composant Vue pour une vue d'entité
 *
 * @param {string} entityType - Type d'entité (normalisé)
 * @param {string} view - Vue demandée ('large', 'compact', 'minimal', 'text')
 * @returns {Promise<Component>} Composant Vue chargé dynamiquement
 */
export async function resolveEntityViewComponent(entityType, view = 'large') {
  const normalizedType = normalizeEntityType(entityType);
  const entityName = ENTITY_COMPONENT_MAP[normalizedType];
  const viewName = VIEW_COMPONENT_MAP[view] || VIEW_COMPONENT_MAP['large'];

  if (!entityName) {
    console.warn(`[resolveEntityViewComponent] Type d'entité non reconnu: ${entityType} (normalisé: ${normalizedType})`);
    // Fallback vers un composant générique si disponible (mais normalement on ne devrait jamais arriver ici)
    return null;
  }

  const componentName = `${entityName}${viewName}`;
  const componentPath = `@/Pages/Molecules/entity/${normalizedType.replace('-', '/')}/${componentName}.vue`;

  try {
    const module = await import(componentPath);
    return module.default || module[componentName] || module;
  } catch (error) {
    console.error(`[resolveEntityViewComponent] Erreur lors du chargement du composant ${componentPath}:`, error);
    return null;
  }
}

/**
 * Résout et charge de manière synchrone (eager) le composant Vue pour une vue d'entité
 * ⚠️ Utilise import.meta.glob avec eager: true, donc tous les composants sont chargés au build
 *
 * @param {string} entityType - Type d'entité (normalisé)
 * @param {string} view - Vue demandée ('large', 'compact', 'minimal', 'text')
 * @returns {Component|null} Composant Vue ou null si non trouvé
 */
export function resolveEntityViewComponentSync(entityType, view = 'large') {
  const normalizedType = normalizeEntityType(entityType);
  const entityName = ENTITY_COMPONENT_MAP[normalizedType];
  const viewName = VIEW_COMPONENT_MAP[view] || VIEW_COMPONENT_MAP['large'];

  if (!entityName) {
    console.warn(`[resolveEntityViewComponentSync] Type d'entité non reconnu: ${entityType} (normalisé: ${normalizedType})`);
    return null;
  }

  const componentName = `${entityName}${viewName}`;
  const componentPath = `@/Pages/Molecules/entity/${normalizedType.replace('-', '/')}/${componentName}.vue`;

  // Utiliser import.meta.glob avec eager pour charger tous les composants au build
  // Note: Cette approche charge tous les composants au build, mais permet un accès synchrone
  const components = import.meta.glob('@/Pages/Molecules/entity/**/*View*.vue', { eager: true });
  
  // Chercher le composant correspondant
  for (const [path, module] of Object.entries(components)) {
    if (path.includes(`/${normalizedType.replace('-', '/')}/`) && path.includes(componentName)) {
      return module.default || module[componentName] || module;
    }
  }

  console.warn(`[resolveEntityViewComponentSync] Composant non trouvé: ${componentPath}`);
  return null;
}
