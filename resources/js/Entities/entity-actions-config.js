/**
 * Configuration des actions disponibles pour chaque type d'entité.
 *
 * @description
 * Définit les actions possibles, leurs permissions, icônes, labels, etc.
 * Centralise la configuration pour faciliter la maintenance et l'extension.
 *
 * @example
 * import { ENTITY_ACTIONS_CONFIG } from '@/Entities/entity-actions-config';
 * const viewAction = ENTITY_ACTIONS_CONFIG.common.view;
 */

/**
 * @typedef {Object} EntityActionConfig
 * @property {string} key - Identifiant unique de l'action
 * @property {string} label - Label affiché à l'utilisateur
 * @property {string} icon - Icône Font Awesome (ex: 'fa-solid fa-eye')
 * @property {string|null} permission - Permission requise (ex: 'canView', 'canUpdate', 'canDelete', 'canManage') ou null si toujours disponible
 * @property {boolean} requiresEntity - Si true, nécessite une entité (ne peut pas être utilisé sans entity)
 * @property {string} [variant] - Variant du bouton (ex: 'error' pour delete)
 * @property {string} [group] - Groupe d'actions (pour séparateurs dans le menu)
 */

/**
 * Actions communes à toutes les entités.
 */
export const ENTITY_ACTIONS_COMMON = Object.freeze({
  view: {
    key: "view",
    label: "Ouvrir (page)",
    icon: "fa-solid fa-eye",
    permission: "canView",
    requiresEntity: true,
    group: "navigation",
  },
  "quick-view": {
    key: "quick-view",
    label: "Ouvrir rapide",
    icon: "fa-solid fa-window-maximize",
    permission: "canView",
    requiresEntity: true,
    group: "navigation",
  },
  edit: {
    key: "edit",
    label: "Modifier (page)",
    icon: "fa-solid fa-pen",
    permission: "canUpdate",
    requiresEntity: true,
    group: "edition",
  },
  "quick-edit": {
    key: "quick-edit",
    label: "Modifier rapide",
    icon: "fa-solid fa-bolt",
    permission: "canUpdate",
    requiresEntity: true,
    group: "edition",
  },
  "copy-link": {
    key: "copy-link",
    label: "Copier le lien",
    icon: "fa-solid fa-link",
    permission: null, // Toujours disponible
    requiresEntity: true,
    group: "tools",
  },
  "download-pdf": {
    key: "download-pdf",
    label: "Télécharger PDF",
    icon: "fa-solid fa-file-pdf",
    permission: null, // Toujours disponible
    requiresEntity: true,
    group: "tools",
  },
  refresh: {
    key: "refresh",
    label: "Rafraîchir",
    icon: "fa-solid fa-arrow-rotate-right",
    permission: "canManage", // Admin/maintenance
    requiresEntity: true,
    group: "tools",
  },
  minimize: {
    key: "minimize",
    label: "Minimiser",
    icon: "fa-solid fa-window-minimize",
    permission: null, // Toujours disponible
    requiresEntity: false, // Peut être utilisé sans entité (dans un panel)
    group: "tools",
    note: "Fonctionnalité à implémenter : permet de fermer un modal en gardant l'état, avec raccourci sticky/absolute en bas",
  },
  delete: {
    key: "delete",
    label: "Supprimer",
    icon: "fa-solid fa-trash",
    permission: "canDelete",
    requiresEntity: true,
    variant: "error", // Style spécial pour action destructive
    group: "destructive",
  },
});

/**
 * Configuration complète des actions par type d'entité.
 * Les entités héritent des actions communes et peuvent en ajouter/surcharger.
 */
export const ENTITY_ACTIONS_CONFIG = Object.freeze({
  common: ENTITY_ACTIONS_COMMON,
  
  // Actions spécifiques par entité (exemple pour resource)
  resource: {
    // Actions spécifiques aux ressources (si nécessaire)
  },
  
  // Autres entités peuvent avoir des actions spécifiques
  // spell: { ... },
  // item: { ... },
});

/**
 * Ordre d'affichage recommandé des groupes d'actions.
 */
export const ACTION_GROUPS_ORDER = Object.freeze([
  "navigation",
  "edition",
  "tools",
  "destructive",
]);

/**
 * Retourne la configuration d'une action spécifique.
 *
 * @param {string} actionKey - Clé de l'action (ex: 'view', 'edit')
 * @param {string} [entityType] - Type d'entité (pour actions spécifiques)
 * @returns {EntityActionConfig|null}
 */
export function getActionConfig(actionKey, entityType = null) {
  const common = ENTITY_ACTIONS_COMMON[actionKey];
  if (common) return common;
  
  if (entityType && ENTITY_ACTIONS_CONFIG[entityType]?.[actionKey]) {
    return ENTITY_ACTIONS_CONFIG[entityType][actionKey];
  }
  
  return null;
}

/**
 * Retourne toutes les actions disponibles pour un type d'entité.
 *
 * @param {string} [entityType] - Type d'entité
 * @returns {Record<string, EntityActionConfig>}
 */
export function getActionsForEntityType(entityType = null) {
  const common = { ...ENTITY_ACTIONS_COMMON };
  
  if (entityType && ENTITY_ACTIONS_CONFIG[entityType]) {
    return { ...common, ...ENTITY_ACTIONS_CONFIG[entityType] };
  }
  
  return common;
}

