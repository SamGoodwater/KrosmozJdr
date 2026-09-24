/**
 * resolveEntityViewComponent — Résout le composant Vue pour une vue d'entité
 *
 * @description
 * Imports dynamiques (lazy) uniquement : ne pas utiliser `eager: true` ici,
 * sinon Vite tire TipTap / toutes les vues dans le chunk layout (`app.js`).
 *
 * @param {string} entityType - Type d'entité (ex: 'resource', 'item', 'spell')
 * @param {string} view - Vue demandée ('full', 'minimal', 'text', 'line', …)
 * @returns {Promise<Component>} Composant Vue chargé dynamiquement
 *
 * @example
 * const component = await resolveEntityViewComponent('resource', 'full');
 */
import { defineAsyncComponent } from 'vue';
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

/** Globs lazy (évalués à la demande, pas au parse du layout). */
const viewEditGlobs = import.meta.glob('@/Pages/Molecules/entity/**/*{View,Edit}*.vue');
const lineRowGlobs = import.meta.glob('@/Pages/Molecules/entity/**/*LineRow.vue');
const genericGlobs = import.meta.glob('@/Pages/Molecules/entity/Entity{View,Edit}*.vue');

/**
 * @param {string} entityType
 * @param {string} [view]
 * @returns {{ folderName: string, componentName: string }|null}
 */
function resolveNames(entityType, view = 'full') {
  const normalizedType = normalizeEntityType(entityType);
  const entityName = ENTITY_COMPONENT_MAP[normalizedType];
  const viewName = VIEW_COMPONENT_MAP[view] || VIEW_COMPONENT_MAP['full'];

  if (!entityName) {
    return null;
  }

  return {
    componentName: `${entityName}${viewName}`,
    folderName: entityName
      .replace(/([a-z0-9])([A-Z])/g, '$1-$2')
      .toLowerCase(),
  };
}

/**
 * Résout et charge dynamiquement le composant Vue pour une vue d'entité
 *
 * @param {string} entityType - Type d'entité (normalisé)
 * @param {string} view - Vue demandée ('full', 'minimal', 'text', …)
 * @returns {Promise<Component|null>}
 */
export async function resolveEntityViewComponent(entityType, view = 'full') {
  const names = resolveNames(entityType, view);
  if (!names) {
    console.warn(`[resolveEntityViewComponent] Type d'entité non reconnu: ${entityType}`);
    return null;
  }

  const { folderName, componentName } = names;
  const mergedGlobs = { ...viewEditGlobs, ...lineRowGlobs, ...genericGlobs };

  for (const [path, importFn] of Object.entries(mergedGlobs)) {
    if (path.includes(`/${folderName}/`) && path.includes(componentName)) {
      try {
        const module = await importFn();
        return module.default || module[componentName] || module;
      } catch (error) {
        console.error(`[resolveEntityViewComponent] Erreur chargement ${path}:`, error);
        return null;
      }
    }
  }

  console.warn(`[resolveEntityViewComponent] Composant non trouvé: ${folderName}/${componentName}`);
  return null;
}

/**
 * Wrapper synchrone pour les tables : AsyncComponent Vue (chunk chargé à l'affichage).
 * Ne tire plus les SFC entity/TipTap dans le bundle layout.
 *
 * @param {string} entityType
 * @param {string} [view]
 * @returns {Component|null}
 */
export function resolveEntityViewComponentSync(entityType, view = 'full') {
  const names = resolveNames(entityType, view);
  if (!names) {
    console.warn(`[resolveEntityViewComponentSync] Type d'entité non reconnu: ${entityType}`);
    return null;
  }

  return defineAsyncComponent(() =>
    resolveEntityViewComponent(entityType, view).then((c) => {
      if (!c) {
        return { name: 'EntityViewMissing', render: () => null };
      }
      return c;
    }),
  );
}
