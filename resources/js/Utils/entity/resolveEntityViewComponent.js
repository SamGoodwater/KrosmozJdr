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
  'resource-types': 'ResourceType',
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
  'quickedit': 'QuickEdit',
  'QuickEdit': 'QuickEdit',
  'editlarge': 'EditLarge',
  'EditLarge': 'EditLarge',
  'editcompact': 'EditCompact',
  'EditCompact': 'EditCompact',
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
  // Le dossier utilise le nom "singulier" de l'entité, pas forcément le type normalisé (pluriel).
  // Exemple: 'capabilities' -> 'capability', 'resource-types' -> 'resource-type'
  // Pour éviter les heuristiques fragiles, on dérive depuis le nom de composant (ENTITY_COMPONENT_MAP).
  const folderName = entityName
    .replace(/([a-z0-9])([A-Z])/g, "$1-$2")
    .toLowerCase();
  const componentPath = `@/Pages/Molecules/entity/${folderName}/${componentName}.vue`;

  // Utiliser import.meta.glob pour que Vite puisse résoudre les imports dynamiques
  // Note: Cette approche charge les composants à la demande (lazy loading)
  // Inclure aussi les composants Edit et QuickEdit
  const components = import.meta.glob('@/Pages/Molecules/entity/**/*{View,Edit,QuickEdit}*.vue');
  
  // Chercher le composant correspondant
  for (const [path, importFn] of Object.entries(components)) {
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
  const folderName = entityName
    .replace(/([a-z0-9])([A-Z])/g, "$1-$2")
    .toLowerCase();
  const componentPath = `@/Pages/Molecules/entity/${folderName}/${componentName}.vue`;

  // Utiliser import.meta.glob avec eager pour charger tous les composants au build
  // Note: Cette approche charge tous les composants au build, mais permet un accès synchrone
  // Inclure aussi les composants Edit et QuickEdit, ainsi que le composant générique EntityQuickEdit
  // Pattern 1: Composants dans les sous-dossiers (ex: resource/ResourceQuickEdit.vue)
  const components = import.meta.glob('@/Pages/Molecules/entity/**/*{View,Edit,QuickEdit}*.vue', { eager: true });
  // Pattern 2: Composants génériques directement dans entity/ (ex: EntityQuickEdit.vue)
  const genericComponents = import.meta.glob('@/Pages/Molecules/entity/Entity{View,Edit,QuickEdit}*.vue', { eager: true });
  
  // Fusionner les deux résultats
  const allComponents = { ...components, ...genericComponents };
  
  // Chercher le composant spécifique d'abord
  for (const [path, module] of Object.entries(allComponents)) {
    if (path.includes(`/${folderName}/`) && path.includes(componentName)) {
      return module.default || module[componentName] || module;
    }
  }

  // Pour quickedit, fallback vers le composant générique EntityQuickEdit
  if (view === 'quickedit' || view === 'QuickEdit') {
    // Chercher EntityQuickEdit.vue dans les composants génériques
    for (const [path, module] of Object.entries(genericComponents)) {
      if (path.includes('EntityQuickEdit.vue')) {
        return module.default || module;
      }
    }
  }

  console.warn(`[resolveEntityViewComponentSync] Composant non trouvé: ${componentPath}`);
  return null;
}
