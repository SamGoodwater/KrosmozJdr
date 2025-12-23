/**
 * entityRouteRegistry
 *
 * @description
 * Centralise les routes Ziggy par type d'entité afin d'éviter que les composants génériques
 * (ex: `EntityTableRow`) ne devinent les noms de routes et les formats de paramètres.
 *
 * Objectif:
 * - scalabilité: une nouvelle entité n'implique pas de modifier des composants génériques
 * - robustesse: gérer les exceptions de nommage (kebab-case, param keys, etc.)
 *
 * @example
 * const cfg = getEntityRouteConfig('resource-types');
 * // cfg.show.name === 'entities.resource-types.show'
 */

/**
 * @typedef {"scalar"|"object"} RouteParamMode
 *
 * @typedef {Object} RouteDefinition
 * @property {string} name - Nom Ziggy (ex: 'entities.resources.show')
 * @property {RouteParamMode} [paramsMode="scalar"] - 'scalar' => route(name, id) ; 'object' => route(name, {paramKey: id})
 * @property {string} [paramKey] - requis si paramsMode = 'object'
 *
 * @typedef {Object} EntityRouteConfig
 * @property {RouteDefinition} [show]
 * @property {RouteDefinition} [edit]
 * @property {RouteDefinition} [delete]
 */

/** @type {Record<string, EntityRouteConfig>} */
export const ENTITY_ROUTE_CONFIG = {
  // Exemples explicités (les autres tombent sur le fallback)
  resources: {
    show: { name: "entities.resources.show", paramsMode: "scalar" },
    edit: { name: "entities.resources.edit", paramsMode: "object", paramKey: "resource" },
    delete: { name: "entities.resources.delete", paramsMode: "object", paramKey: "resource" },
  },
  "resource-types": {
    show: { name: "entities.resource-types.show", paramsMode: "scalar" },
    edit: { name: "entities.resource-types.edit", paramsMode: "object", paramKey: "resourceType" },
    delete: { name: "entities.resource-types.delete", paramsMode: "object", paramKey: "resourceType" },
  },
};

/**
 * Retourne une config de routes pour une entité.
 * Par défaut, on suppose `entities.{entityType}.show` avec param scalaire (le plus robuste côté Ziggy).
 *
 * @param {string} entityType
 * @returns {EntityRouteConfig}
 */
export function getEntityRouteConfig(entityType) {
  const key = String(entityType || "");
  if (ENTITY_ROUTE_CONFIG[key]) return ENTITY_ROUTE_CONFIG[key];
  return {
    show: { name: `entities.${key}.show`, paramsMode: "scalar" },
  };
}

/**
 * Résout une URL Ziggy complète (avec origin) pour une entité et une action.
 * Retourne une string vide si la route n'existe pas / résolution impossible.
 *
 * @param {string} entityType
 * @param {"show"|"edit"|"delete"} action
 * @param {number|string} entityId
 * @param {EntityRouteConfig|null} [overrideConfig]
 * @returns {string}
 */
export function resolveEntityRouteUrl(entityType, action, entityId, overrideConfig = null) {
  try {
    const href = resolveEntityRouteHref(entityType, action, entityId, overrideConfig);
    if (!href) return "";
    return `${window.location.origin}${href}`;
  } catch (e) {
    console.warn(`[entityRouteRegistry] Impossible de résoudre l'URL`, e);
    return "";
  }
}

/**
 * Résout un href Ziggy (relatif) pour une entité et une action.
 * Retourne une string vide si la route n'existe pas / résolution impossible.
 *
 * @param {string} entityType
 * @param {"show"|"edit"|"delete"} action
 * @param {number|string} entityId
 * @param {EntityRouteConfig|null} [overrideConfig]
 * @returns {string}
 */
export function resolveEntityRouteHref(entityType, action, entityId, overrideConfig = null) {
  const cfg = overrideConfig || getEntityRouteConfig(entityType);
  const def = cfg?.[action];
  const routeName = def?.name || `entities.${String(entityType)}.${String(action)}`;

  try {
    const mode = def?.paramsMode || "scalar";
    const params =
      mode === "object"
        ? { [def?.paramKey || String(entityType)]: entityId }
        : entityId;

    return route(routeName, params);
  } catch (e) {
    console.warn(`[entityRouteRegistry] Impossible de résoudre l'href: ${routeName}`, e);
    return "";
  }
}


