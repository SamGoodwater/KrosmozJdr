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
 * @property {string} tooltip - Tooltip détaillé (optionnel, utilise label si non fourni)
 * @property {string} icon - Icône Font Awesome (ex: 'fa-solid fa-eye')
 * @property {string|null} permission - Permission requise (ex: 'canView', 'canUpdate', 'canDelete', 'canManage') ou null si toujours disponible
 * @property {boolean} requiresEntity - Si true, nécessite une entité (ne peut pas être utilisé sans entity)
 * @property {string} [variant] - Variant du bouton (ex: 'error' pour delete)
 * @property {string} [group] - Groupe d'actions (pour séparateurs dans le menu)
 * @property {Function} [getLabel] - Fonction pour obtenir le label selon le contexte
 * @property {Function} [getTooltip] - Fonction pour obtenir le tooltip selon le contexte
 * @property {Function} [visibleIf] - Fonction pour déterminer si l'action est visible selon le contexte
 */

/**
 * Actions communes à toutes les entités.
 */
export const ENTITY_ACTIONS_COMMON = Object.freeze({
  view: {
    key: "view",
    label: "Ouvrir",
    tooltip: "Ouvrir dans une page complète",
    icon: "fa-solid fa-eye",
    permission: "canView",
    requiresEntity: true,
    group: "navigation",
    getLabel: (context) => {
      // Si on est dans un modal, le label change
      if (context?.inModal) return "Agrandir";
      return "Ouvrir";
    },
    getTooltip: (context) => {
      if (context?.inModal) return "Ouvrir dans une page complète";
      return "Ouvrir dans une page complète";
    },
    visibleIf: (context) => {
      // En modal, on n'affiche pas "view" (on utilise expand à la place)
      if (context?.inModal) return false;
      // Sur la page de l'entité, on n'affiche pas "view" (on est déjà sur la page)
      if (context?.inPage) return false;
      return true;
    },
  },
  "quick-view": {
    key: "quick-view",
    label: "Afficher",
    tooltip: "Afficher dans une modal rapide",
    icon: "fa-solid fa-window-maximize",
    permission: "canView",
    requiresEntity: true,
    group: "navigation",
    getLabel: (context) => {
      // Si on est dans une page, on peut vouloir "Afficher" en modal
      if (context?.inPage) return "Afficher";
      return "Afficher";
    },
    getTooltip: (context) => {
      if (context?.inPage) return "Afficher dans une modal rapide";
      return "Afficher dans une modal rapide";
    },
    visibleIf: (context) => {
      // En modal, on n'affiche pas "quick-view" (pas de sens)
      if (context?.inModal) return false;
      // Sur la page de l'entité, on n'affiche pas "quick-view" (on est déjà sur la page)
      if (context?.inPage) return false;
      return true;
    },
  },
  edit: {
    key: "edit",
    label: "Modifier",
    tooltip: "Modifier dans une page complète",
    icon: "fa-solid fa-pen",
    permission: "canUpdate",
    requiresEntity: true,
    group: "edition",
    getLabel: (context) => {
      // Si on est dans un modal de modification, le label change
      if (context?.inModal && context?.modalMode === "edit") return "Agrandir";
      return "Modifier";
    },
    getTooltip: (context) => {
      if (context?.inModal && context?.modalMode === "edit") return "Modifier dans une page complète";
      return "Modifier dans une page complète";
    },
    visibleIf: (context) => {
      // En modal, on n'affiche pas "edit" (on utilise quick-edit à la place)
      if (context?.inModal) return false;
      return true;
    },
  },
  "quick-edit": {
    key: "quick-edit",
    label: "Modifier",
    tooltip: "Modifier dans une modal rapide",
    icon: "fa-solid fa-bolt",
    permission: "canUpdate",
    requiresEntity: true,
    group: "edition",
    getLabel: (context) => {
      // Si on est dans une page, on peut vouloir "Modifier" en modal
      if (context?.inPage) return "Modifier";
      return "Modifier";
    },
    getTooltip: (context) => {
      if (context?.inPage) return "Modifier dans une modal rapide";
      return "Modifier dans une modal rapide";
    },
    visibleIf: (context) => {
      // En modal, on affiche "quick-edit" (modifier dans le modal)
      // En page, on affiche aussi "quick-edit" (modifier dans un modal)
      return true;
    },
  },
  expand: {
    key: "expand",
    label: "Agrandir",
    tooltip: "Ouvrir dans une page complète",
    icon: "fa-solid fa-expand",
    permission: null, // Hérite de la permission de l'action d'origine
    requiresEntity: true,
    group: "navigation",
    getLabel: (context) => {
      if (context?.modalMode === "view") return "Agrandir";
      if (context?.modalMode === "edit") return "Agrandir";
      return "Agrandir";
    },
    getTooltip: (context) => {
      if (context?.modalMode === "view") return "Ouvrir dans une page complète";
      if (context?.modalMode === "edit") return "Modifier dans une page complète";
      return "Ouvrir dans une page complète";
    },
    visibleIf: (context) => {
      // Visible uniquement si on est dans un modal
      return Boolean(context?.inModal);
    },
  },
  "copy-link": {
    key: "copy-link",
    label: "Copier le lien",
    tooltip: "Copier l'URL de l'entité dans le presse-papiers",
    icon: "fa-solid fa-link",
    permission: null, // Toujours disponible
    requiresEntity: true,
    group: "tools",
  },
  "download-pdf": {
    key: "download-pdf",
    label: "Télécharger PDF",
    tooltip: "Télécharger l'entité au format PDF",
    icon: "fa-solid fa-file-pdf",
    permission: null, // Toujours disponible
    requiresEntity: true,
    group: "tools",
  },
  refresh: {
    key: "refresh",
    label: "Rafraîchir",
    tooltip: "Rafraîchir les données depuis le serveur (via scrapping)",
    icon: "fa-solid fa-arrow-rotate-right",
    permission: "canManage", // Admin/maintenance
    requiresEntity: true,
    group: "tools",
  },
  minimize: {
    key: "minimize",
    label: "Minimiser",
    tooltip: "Minimiser le modal (fonctionnalité future)",
    icon: "fa-solid fa-window-minimize",
    permission: null, // Toujours disponible
    requiresEntity: false, // Peut être utilisé sans entité (dans un panel)
    group: "tools",
    note: "Fonctionnalité à implémenter : permet de fermer un modal en gardant l'état, avec raccourci sticky/absolute en bas",
  },
  delete: {
    key: "delete",
    label: "Supprimer",
    tooltip: "Supprimer définitivement l'entité",
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

