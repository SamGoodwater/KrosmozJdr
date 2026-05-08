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
  minimalLine: ["pin", "favorite", "copy-link", "quick-view", "quick-edit"],
  modalDetail: ["favorite", "copy-link", "view", "quick-edit", "refresh", "delete"],
  pageDetail: ["favorite", "copy-link", "edit", "refresh", "delete"],
  tableDropdown: ["pin", "favorite", "copy-link", "quick-view", "quick-edit"],
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
    label: "Ouvrir",
    tooltip: "Ouvrir dans une page complète",
    icon: "fa-solid fa-up-right-from-square",
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
      // Sur la page de l'entité, on n'affiche pas "view" (on est déjà sur la page)
      if (context?.inPage) return false;
      // En minimal, l'ouverture page se fait via actions dédiées seulement (pas d'ouverture implicite).
      if (context?.inMinimal) return false;
      if (context?.inLine || context?.viewMode === "line" || context?.viewMode === "minimal") return false;
      return true;
    },
  },
  "quick-view": {
    key: "quick-view",
    label: "Ouvrir",
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
    tooltip: "Modifier dans une page complète",
    icon: "fa-solid fa-pen-to-square",
    permission: "canUpdate",
    requiresEntity: true,
    group: "edition",
    getLabel: (context) => {
      // Si on est dans un modal de modification, le label change
      if (context?.inModal && context?.modalMode === "edit") return "Agrandir";
      return "Éditer";
    },
    getTooltip: (context) => {
      if (context?.inModal && context?.modalMode === "edit") return "Modifier dans une page complète";
      return "Modifier dans une page complète";
    },
    visibleIf: (context) => {
      // En vue minimal, on garde l'édition en modal rapide.
      if (context?.inMinimal) return false;
      if (context?.inLine || context?.viewMode === "line" || context?.viewMode === "minimal") return false;
      // Visible en modal et en page (ouverture/édition mode page depuis ces contextes).
      if (context?.inModal || context?.inPage) return true;
      // Hors modal/page, on privilégie l'édition rapide.
      return false;
    },
  },
  "quick-edit": {
    key: "quick-edit",
    label: "Éditer",
    tooltip: "Modifier dans une modal rapide",
    icon: "fa-solid fa-pen-to-square",
    permission: "canUpdate",
    requiresEntity: true,
    group: "edition",
    getLabel: (context) => {
      // Si on est dans une page, on peut vouloir "Modifier" en modal
      if (context?.inPage) return "Éditer";
      return "Éditer";
    },
    getTooltip: (context) => {
      if (context?.inPage) return "Modifier dans une modal rapide";
      return "Modifier dans une modal rapide";
    },
    visibleIf: (context) => {
      // En page complète, on privilégie l'édition page. En modal, on garde l'édition modale.
      if (context?.inPage) return false;
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

