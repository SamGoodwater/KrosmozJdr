/**
 * Composable pour gérer les actions d'entité.
 *
 * @description
 * - Filtre les actions selon les permissions (via usePermissions)
 * - Gère le filtrage (whitelist/blacklist)
 * - Retourne les actions disponibles formatées
 * - Gère les groupes d'actions pour les séparateurs dans les menus
 *
 * @example
 * const { availableActions, groupedActions } = useEntityActions('spells', entity, {
 *   whitelist: ['view', 'edit'],
 *   blacklist: ['delete'],
 * });
 */

import { computed } from "vue";
import { getActionsForEntityType, ACTION_GROUPS_ORDER } from "@/Entities/entity-actions-config";
import { usePermissions } from "@/Composables/permissions/usePermissions";

/**
 * @param {string} entityType - Type d'entité (ex: 'spells', 'items')
 * @param {Object|null} entity - Entité (peut être null pour certaines actions)
 * @param {Object} [options] - Options de filtrage
 * @param {string[]} [options.whitelist] - Liste d'actions à inclure uniquement
 * @param {string[]} [options.blacklist] - Liste d'actions à exclure
 * @param {Object} [options.context] - Contexte supplémentaire (ex: { inPanel: true } pour minimize)
 * @returns {{ availableActions: ComputedRef<EntityActionConfig[]>, groupedActions: ComputedRef<Object> }}
 */
export function useEntityActions(entityType, entity = null, options = {}) {
  const { can, canViewAny, canUpdateAny, canDeleteAny, isAdmin } = usePermissions();
  
  const {
    whitelist = null,
    blacklist = null,
    context = {},
  } = options;
  
  // Récupère la config des actions pour ce type d'entité
  const actionsConfig = computed(() => {
    return getActionsForEntityType(entityType);
  });
  
  /**
   * Vérifie si une permission est accordée pour une action.
   *
   * @param {string|null} permission - Nom de la permission (ex: 'canView', 'canUpdate')
   * @returns {boolean}
   */
  const checkPermission = (permission) => {
    if (!permission) return true; // Pas de permission requise
    
    // Mapping des permissions vers les méthodes usePermissions
    const permissionMap = {
      canView: () => {
        // Si on a une entité, on devrait vérifier canView(entity), mais pour l'instant on utilise canViewAny
        // TODO: Implémenter canView(entity) si nécessaire
        return canViewAny(entityType);
      },
      canUpdate: () => {
        // TODO: Implémenter canUpdate(entity) si nécessaire
        return canUpdateAny(entityType);
      },
      canDelete: () => {
        // TODO: Implémenter canDelete(entity) si nécessaire
        return canDeleteAny(entityType);
      },
      canManage: () => {
        // canManage = admin pour l'instant (via manageAny)
        return can(entityType, "manageAny") || isAdmin.value;
      },
    };
    
    const checkFn = permissionMap[permission];
    return checkFn ? checkFn() : false;
  };
  
  // Filtre les actions selon les permissions et les options
  const availableActions = computed(() => {
    const config = actionsConfig.value;
    const actions = Object.values(config);
    
    return actions
      .filter((action) => {
        // Whitelist : n'inclure que les actions listées
        if (whitelist && !whitelist.includes(action.key)) {
          return false;
        }
        
        // Blacklist : exclure les actions listées
        if (blacklist && blacklist.includes(action.key)) {
          return false;
        }
        
        // Vérifier si l'entité est requise
        if (action.requiresEntity && !entity) {
          return false;
        }
        
        // Minimize : seulement disponible dans un panel (context.inPanel)
        if (action.key === "minimize" && !context.inPanel) {
          return false;
        }
        
        // Vérifier les permissions
        if (action.permission && !checkPermission(action.permission)) {
          return false;
        }
        
        // Vérifier visibleIf si défini
        if (typeof action.visibleIf === "function" && !action.visibleIf(context)) {
          return false;
        }
        
        return true;
      })
      .map((action) => {
        // Enrichir l'action avec label et tooltip dynamiques selon le contexte
        const enrichedAction = { ...action };
        
        // Label dynamique
        if (typeof action.getLabel === "function") {
          enrichedAction.label = action.getLabel(context) || action.label;
        }
        
        // Tooltip dynamique
        if (typeof action.getTooltip === "function") {
          enrichedAction.tooltip = action.getTooltip(context) || action.tooltip || action.label;
        } else {
          enrichedAction.tooltip = action.tooltip || action.label;
        }
        
        return enrichedAction;
      });
  });
  
  /**
   * Actions groupées par groupe pour les séparateurs dans les menus.
   */
  const groupedActions = computed(() => {
    const actions = availableActions.value;
    const groups = {};
    
    actions.forEach((action) => {
      const group = action.group || "other";
      if (!groups[group]) {
        groups[group] = [];
      }
      groups[group].push(action);
    });
    
    // Trier les groupes selon l'ordre recommandé
    const orderedGroups = {};
    ACTION_GROUPS_ORDER.forEach((groupKey) => {
      if (groups[groupKey]) {
        orderedGroups[groupKey] = groups[groupKey];
      }
    });
    
    // Ajouter les groupes non listés à la fin
    Object.keys(groups).forEach((groupKey) => {
      if (!ACTION_GROUPS_ORDER.includes(groupKey)) {
        orderedGroups[groupKey] = groups[groupKey];
      }
    });
    
    return orderedGroups;
  });
  
  return {
    availableActions,
    groupedActions,
    actionsConfig,
  };
}

