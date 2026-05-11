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
 * @property {Function} [getIcon] - Fonction pour obtenir l'icône selon le contexte
 * @property {Function} [visibleIf] - Fonction pour déterminer si l'action est visible selon le contexte
 */

/**
 * Matrice centrale des actions selon le contexte d'affichage.
 */
export const SCRAPPABLE_ENTITY_TYPES = Object.freeze([
  "monsters",
  "breeds",
  "resources",
  "items",
  "consumables",
  "panoplies",
  "spells",
]);

export const ENTITY_ACTION_CONTEXT_PRESETS = Object.freeze({
  minimalLine: ["state", "pin", "favorite", "copy-link", "quick-view", "quick-edit"],
  modalDetail: ["state", "favorite", "copy-link", "view", "edit", "quick-edit", "refresh", "delete"],
  pageDetail: ["state", "favorite", "copy-link", "view", "edit", "refresh", "delete"],
  tableDropdown: ["state", "pin", "favorite", "copy-link", "quick-view", "quick-edit"],
});

export function normalizeActionEntityType(entityType = "") {
  const raw = String(entityType || "").trim();
  const map = {
    resource: "resources",
    item: "items",
    consumable: "consumables",
    spell: "spells",
    monster: "monsters",
    breed: "breeds",
    classe: "breeds",
    class: "breeds",
    panoply: "panoplies",
    condition: "conditions",
    capability: "capabilities",
    specialization: "specializations",
    "creature-trait": "creature-traits",
    creatureTrait: "creature-traits",
    shop: "shops",
    npc: "npcs",
    campaign: "campaigns",
    scenario: "scenarios",
    "resource-type": "resource-types",
    resourceType: "resource-types",
  };
  return map[raw] || raw;
}

export function isScrappableEntityType(entityType) {
  return SCRAPPABLE_ENTITY_TYPES.includes(normalizeActionEntityType(entityType));
}

/**
 * Actions communes à toutes les entités.
 */
export const ENTITY_ACTIONS_COMMON = Object.freeze({
  view: {
    key: "view",
    label: "Afficher",
    tooltip: "Afficher",
    icon: "fa-solid fa-eye",
    permission: "canView",
    requiresEntity: true,
    group: "navigation",
    getLabel: (context) => (context?.inModal ? "Agrandir" : "Afficher"),
    getTooltip: (context) => (context?.inModal ? "Agrandir" : "Afficher"),
    getIcon: (context) => (context?.inModal ? "fa-solid fa-expand" : "fa-solid fa-eye"),
    visibleIf: (context) => {
      // En minimal, l'ouverture page se fait via actions dédiées seulement (pas d'ouverture implicite).
      if (context?.inMinimal) return false;
      if (context?.inLine || context?.viewMode === "line" || context?.viewMode === "minimal") return false;
      // Sur une page d'édition, on garde l'action "Afficher" en plus du bouton retour.
      if (context?.inPage) return context?.pageMode === "edit";
      return true;
    },
  },
  "quick-view": {
    key: "quick-view",
    label: "Afficher",
    tooltip: "Afficher",
    icon: "fa-solid fa-eye",
    permission: "canView",
    requiresEntity: true,
    group: "navigation",
    getLabel: () => "Afficher",
    getTooltip: () => "Afficher",
    visibleIf: (context) => {
      // En modal, `view` devient Agrandir vers la page.
      if (context?.inModal) return false;
      // Sur la page de l'entité, on n'affiche pas "quick-view" (on est déjà sur la page)
      if (context?.inPage) return false;
      return true;
    },
  },
  edit: {
    key: "edit",
    label: "Éditer",
    tooltip: "Éditer",
    icon: "fa-solid fa-pen-to-square",
    permission: "canUpdate",
    requiresEntity: true,
    group: "edition",
    getLabel: (context) => (context?.inModal && context?.modalMode === "edit" ? "Agrandir" : "Éditer"),
    getTooltip: (context) => (context?.inModal && context?.modalMode === "edit" ? "Agrandir" : "Éditer"),
    getIcon: (context) => (context?.inModal && context?.modalMode === "edit" ? "fa-solid fa-expand" : "fa-solid fa-pen-to-square"),
    visibleIf: (context) => {
      // En vue minimal, on garde l'édition en modal rapide.
      if (context?.inMinimal) return false;
      if (context?.inLine || context?.viewMode === "line" || context?.viewMode === "minimal") return false;
      // En modal d'édition, ce bouton devient "Agrandir" vers la page d'édition.
      if (context?.inModal) return context?.modalMode === "edit";
      // Sur une page d'édition, l'action utile devient "Afficher".
      if (context?.inPage) return context?.pageMode !== "edit";
      // Hors modal/page, on privilégie l'édition rapide.
      return false;
    },
  },
  "quick-edit": {
    key: "quick-edit",
    label: "Éditer",
    tooltip: "Éditer",
    icon: "fa-solid fa-pen-to-square",
    permission: "canUpdate",
    requiresEntity: true,
    group: "edition",
    getLabel: () => "Éditer",
    getTooltip: () => "Éditer",
    visibleIf: (context) => {
      // En page complète, on privilégie l'édition page. En modal, on garde l'édition modale.
      if (context?.inPage) return false;
      if (context?.inModal && context?.modalMode === "edit") return false;
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
    visibleIf: (_context) => {
      // Action historique remplacée par `view`.
      return false;
    },
  },
  "copy-link": {
    key: "copy-link",
    label: "Copier",
    tooltip: "Copier l'URL de l'entité dans le presse-papiers",
    icon: "fa-solid fa-link",
    permission: null, // Toujours disponible
    requiresEntity: true,
    group: "tools",
  },
  favorite: {
    key: "favorite",
    label: "Ajouter aux favoris",
    tooltip: "Ajouter cette fiche aux favoris (local)",
    icon: "fa-regular fa-star",
    permission: null,
    requiresEntity: true,
    group: "tools",
  },
  state: {
    key: "state",
    label: "État",
    tooltip: "Voir ou modifier l'état de l'entité",
    icon: "fa-solid fa-circle",
    permission: null,
    requiresEntity: true,
    group: "status",
    visibleIf: (_context, entity) => {
      const raw = entity?._data ?? entity;
      return Object.prototype.hasOwnProperty.call(raw || {}, "state");
    },
  },
  pin: {
    key: "pin",
    label: "Épingler",
    tooltip: "Épingler cette fiche (mémorisé sur cet appareil)",
    icon: "fa-solid fa-thumbtack",
    permission: null,
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
    visibleIf: () => false,
  },
  refresh: {
    key: "refresh",
    label: "Rafraîchir",
    tooltip: "Rafraîchir les données depuis le serveur (via scrapping)",
    icon: "fa-solid fa-arrow-rotate-right",
    permission: "canManage",
    requiresEntity: true,
    group: "tools",
    visibleIf: (context) => {
      if (!isScrappableEntityType(context?.entityType)) return false;
      return Boolean(context?.inModal || context?.inPage);
    },
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
    visibleIf: (context) => Boolean(context?.inModal || context?.inPage),
  },
});

/**
 * Configuration complète des actions par type d'entité.
 * Les entités héritent des actions communes et peuvent en ajouter/surcharger.
 */
export const ENTITY_ACTIONS_CONFIG = Object.freeze({
  common: ENTITY_ACTIONS_COMMON,

  conditions: {},
  
  // Actions spécifiques par entité (exemple pour resource)
  resource: {
    // Actions spécifiques aux ressources (si nécessaire)
  },

  /** Monstres : Rafraîchir utilise le pipeline scrapping V2 (conversion BDD, validation, intégration). */
  monsters: {
    refresh: {
      key: "refresh",
      label: "Rafraîchir",
      tooltip: "Rafraîchir les données depuis DofusDB (pipeline V2)",
      icon: "fa-solid fa-arrow-rotate-right",
      permission: "canManage",
      requiresEntity: true,
      group: "tools",
      visibleIf: ENTITY_ACTIONS_COMMON.refresh.visibleIf,
    },
  },

  /** Sorts : Rafraîchir utilise le pipeline V2. */
  spells: {
    refresh: {
      key: "refresh",
      label: "Rafraîchir",
      tooltip: "Rafraîchir les données depuis DofusDB (pipeline V2)",
      icon: "fa-solid fa-arrow-rotate-right",
      permission: "canManage",
      requiresEntity: true,
      group: "tools",
      visibleIf: ENTITY_ACTIONS_COMMON.refresh.visibleIf,
    },
  },

  /** Breeds (affichées « Classes ») : Rafraîchir utilise le pipeline V2. */
  breeds: {
    refresh: {
      key: "refresh",
      label: "Rafraîchir",
      tooltip: "Rafraîchir les données depuis DofusDB (pipeline V2)",
      icon: "fa-solid fa-arrow-rotate-right",
      permission: "canManage",
      requiresEntity: true,
      group: "tools",
      visibleIf: ENTITY_ACTIONS_COMMON.refresh.visibleIf,
    },
  },

  /** Panoplies : Rafraîchir utilise le pipeline V2. */
  panoplies: {
    refresh: {
      key: "refresh",
      label: "Rafraîchir",
      tooltip: "Rafraîchir les données depuis DofusDB (pipeline V2)",
      icon: "fa-solid fa-arrow-rotate-right",
      permission: "canManage",
      requiresEntity: true,
      group: "tools",
      visibleIf: ENTITY_ACTIONS_COMMON.refresh.visibleIf,
    },
  },

  /** Items : Rafraîchir utilise le pipeline V2. */
  items: {
    refresh: {
      key: "refresh",
      label: "Rafraîchir",
      tooltip: "Rafraîchir les données depuis DofusDB (pipeline V2)",
      icon: "fa-solid fa-arrow-rotate-right",
      permission: "canManage",
      requiresEntity: true,
      group: "tools",
      visibleIf: ENTITY_ACTIONS_COMMON.refresh.visibleIf,
    },
  },

  /** Ressources : Rafraîchir utilise le pipeline V2. */
  resources: {
    refresh: {
      ...ENTITY_ACTIONS_COMMON.refresh,
      tooltip: "Rafraîchir les données depuis DofusDB (pipeline V2)",
    },
  },

  /** Consommables : Rafraîchir utilise le pipeline V2. */
  consumables: {
    refresh: {
      ...ENTITY_ACTIONS_COMMON.refresh,
      tooltip: "Rafraîchir les données depuis DofusDB (pipeline V2)",
    },
  },
});

/**
 * Ordre d'affichage recommandé des groupes d'actions.
 */
export const ACTION_GROUPS_ORDER = Object.freeze([
  "status",
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

