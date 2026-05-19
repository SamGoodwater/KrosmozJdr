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
import {
  ACTION_GROUPS_ORDER,
  ENTITY_ACTION_CONTEXT_PRESETS,
  getActionsForEntityType,
  isScrappableEntityType,
  normalizeActionEntityType,
} from "@/Entities/entity-actions-config";
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
  const { can, canViewAny, canUpdateAny, canDeleteAny, isAdmin, authUser } = usePermissions();
  
  const {
    whitelist = null,
    blacklist = null,
    context = {},
  } = options;

  const normalizedEntityType = computed(() => normalizeActionEntityType(entityType));

  const resolvedContext = computed(() => {
    const ctx = {
      ...context,
      entityType: normalizeActionEntityType(context?.entityType || entityType),
    };

    if (ctx.viewMode === "minimal" || ctx.viewMode === "line" || ctx.inMinimal || ctx.inLine) {
      return { ...ctx, preset: ctx.preset || "minimalLine", inMinimal: true };
    }
    if (ctx.surface === "modal" || ctx.inModal) {
      return { ...ctx, preset: ctx.preset || "modalDetail", inModal: true };
    }
    if (ctx.surface === "page" || ctx.inPage) {
      return { ...ctx, preset: ctx.preset || "pageDetail", inPage: true };
    }
    return { ...ctx, preset: ctx.preset || "tableDropdown" };
  });
  
  // Récupère la config des actions pour ce type d'entité
  const actionsConfig = computed(() => {
    return getActionsForEntityType(normalizedEntityType.value);
  });
  
  /**
   * Vérifie si une permission est accordée pour une action.
   *
   * @param {string|null} permission - Nom de la permission (ex: 'canView', 'canUpdate')
   * @returns {boolean}
   */
  const getEntityOwnerId = (target) => {
    if (!target) return null;
    const raw = target?._data ?? target;
    const fromCreatedByObj = raw?.createdBy?.id ?? raw?.created_by?.id ?? null;
    const fromCreatedByScalar = raw?.created_by ?? null;
    const candidate = fromCreatedByObj ?? fromCreatedByScalar;
    const id = Number(candidate);
    return Number.isFinite(id) ? id : null;
  };

  const isEntityOwner = computed(() => {
    const currentUserId = Number(authUser.value?.id ?? 0);
    const ownerId = getEntityOwnerId(entity);
    if (!currentUserId || !ownerId) return false;
    return currentUserId === ownerId;
  });

  const checkPermission = (permission) => {
    if (!permission) return true; // Pas de permission requise
    
    // Mapping des permissions vers les méthodes usePermissions
    const permissionMap = {
      canView: () => {
        // Si on a une entité, on devrait vérifier canView(entity), mais pour l'instant on utilise canViewAny
        // TODO: Implémenter canView(entity) si nécessaire
        return canViewAny(normalizedEntityType.value);
      },
      canUpdate: () => {
        return canUpdateAny(normalizedEntityType.value) || isEntityOwner.value;
      },
      canDelete: () => {
        return canDeleteAny(normalizedEntityType.value) || isEntityOwner.value;
      },
      canManage: () => {
        return can(normalizedEntityType.value, "manageAny") || isAdmin.value;
      },
    };
    
    const checkFn = permissionMap[permission];
    return checkFn ? checkFn() : false;
  };
  
  // Filtre les actions selon les permissions et les options
  const availableActions = computed(() => {
    const config = actionsConfig.value;
    const actions = Object.values(config);
    const ctx = resolvedContext.value;
    const presetKeys = Array.isArray(ENTITY_ACTION_CONTEXT_PRESETS[ctx.preset])
      ? ENTITY_ACTION_CONTEXT_PRESETS[ctx.preset]
      : null;
    
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

        if (!whitelist && presetKeys && !presetKeys.includes(action.key)) {
          return false;
        }
        
        // Vérifier si l'entité est requise
        if (action.requiresEntity && !entity) {
          return false;
        }
        
        // Minimize : seulement disponible dans un panel (context.inPanel)
        if (action.key === "minimize" && !ctx.inPanel) {
          return false;
        }

        if (action.key === "refresh" && !isScrappableEntityType(normalizedEntityType.value)) {
          return false;
        }
        
        // Vérifier les permissions
        if (action.permission && !checkPermission(action.permission)) {
          return false;
        }
        
        // Vérifier visibleIf si défini
        if (typeof action.visibleIf === "function" && !action.visibleIf(ctx, entity)) {
          return false;
        }
        
        return true;
      })
      .map((action) => {
        // Enrichir l'action avec label et tooltip dynamiques selon le contexte
        const enrichedAction = { ...action };
        
        // Label dynamique
        if (typeof action.getLabel === "function") {
          enrichedAction.label = action.getLabel(ctx) || action.label;
        }
        
        // Tooltip dynamique
        if (typeof action.getTooltip === "function") {
          enrichedAction.tooltip = action.getTooltip(ctx) || action.tooltip || action.label;
        } else {
          enrichedAction.tooltip = action.tooltip || action.label;
        }

        if (typeof action.getIcon === "function") {
          enrichedAction.icon = action.getIcon(ctx) || action.icon;
        }

        enrichedAction.intent = resolveActionIntent(action.key, ctx);

        if (action.key === "state") {
          enrichedAction.canUpdateState = checkPermission("canUpdate");
          enrichedAction.stateValue = (entity?._data ?? entity)?.state ?? null;
          enrichedAction.showStateLabel = shouldShowStateLabel(ctx);
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
    context: resolvedContext,
  };
}

function resolveActionIntent(actionKey, context) {
  const modal = Boolean(context?.inModal);
  const page = Boolean(context?.inPage);
  const minimalLine = Boolean(context?.inMinimal || context?.inLine || context?.viewMode === "minimal" || context?.viewMode === "line");

  if (actionKey === "view") return modal ? "open-page" : "open-page";
  if (actionKey === "quick-view") return page ? "open-modal" : "open-modal";
  if (actionKey === "edit") return modal || page ? "edit-page" : "edit-page";
  if (actionKey === "quick-edit") return minimalLine || !page ? "edit-modal" : "edit-modal";
  if (actionKey === "copy-link") return "copy-link";
  if (actionKey === "refresh") return "refresh";
  if (actionKey === "delete") return "delete";
  if (actionKey === "pin") return "pin";
  if (actionKey === "favorite") return "favorite";
  if (actionKey === "state") return "state";
  return actionKey;
}

function shouldShowStateLabel(context) {
  const viewMode = String(context?.viewMode || "").trim();
  if (context?.inMinimal || context?.inLine || viewMode === "minimal" || viewMode === "line") return false;
  if (viewMode === "full") return true;
  if (context?.inModal || context?.inPage) return true;
  return false;
}

