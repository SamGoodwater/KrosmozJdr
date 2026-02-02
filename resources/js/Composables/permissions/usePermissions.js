/**
 * usePermissions
 *
 * @description
 * Service/composable unique pour lire les permissions exposées par Laravel (Inertia props)
 * et fournir une API DRY: can(entityType, ability).
 *
 * - Source of truth: backend Policies (via `page.props.permissions`)
 * - Cache: mémoire tampon par user (évite de retraiter/recopier)
 *
 * @example
 * const { can, canReadAny, canUpdateAny } = usePermissions();
 * if (can('resources', 'updateAny')) { ... }
 */

import { computed, shallowRef } from "vue";
import { usePage } from "@inertiajs/vue3";

// Cache module-level (mémoire tampon) : persiste tant que l'onglet est ouvert.
const _cache = {
  userId: null,
  permissions: shallowRef({ entities: {} }),
};

const normalizeAbility = (ability) => {
  const a = String(ability || "").trim();
  // Alias métier -> abilities policy
  if (a === "read" || a === "view") return "view";
  if (a === "readAny" || a === "viewAny" || a === "list") return "viewAny";
  if (a === "add" || a === "create") return "create";
  if (a === "addAny" || a === "createAny") return "createAny";
  if (a === "update" || a === "edit") return "update";
  if (a === "updateAny" || a === "editAny") return "updateAny";
  if (a === "delete" || a === "remove") return "delete";
  if (a === "deleteAny" || a === "removeAny") return "deleteAny";
  if (a === "manage" || a === "admin") return "manageAny";
  if (a === "manageAny" || a === "adminAny") return "manageAny";
  return a;
};

const normalizeEntityType = (entityType) => {
  const t = String(entityType || "").trim();
  if (!t) return "";
  // Si déjà au pluriel / kebab, on le garde.
  if (t.includes("-") || t.endsWith("s")) return t;

  // Mapping singulier -> entityType registry (pluriel / kebab-case)
  const map = {
    item: "items",
    monster: "monsters",
    spell: "spells",
    creature: "creatures",
    scenario: "scenarios",
    shop: "shops",
    npc: "npcs",
    consumable: "consumables",
    campaign: "campaigns",
    panoply: "panoplies",
    attribute: "attributes",
    breed: "breeds",
    classe: "breeds", // alias pour rétrocompat
    resource: "resources",
    resourceType: "resource-types",
    specialization: "specializations",
    capability: "capabilities",
    page: "pages",
    user: "users",
  };

  return map[t] || t;
};

export function usePermissions() {
  const page = usePage();

  // Métadonnées d'auth (centralisées)
  const authUser = computed(() => page.props.auth?.user ?? null);
  const isAuthenticated = computed(() => Boolean(authUser.value?.id));
  const isAdmin = computed(() => Boolean(authUser.value?.is_admin ?? authUser.value?.isAdmin ?? false));
  const isSuperAdmin = computed(() => Boolean(authUser.value?.is_super_admin ?? authUser.value?.isSuperAdmin ?? false));

  const userId = computed(() => page.props.auth?.user?.id ?? null);
  const rawPermissions = computed(() => page.props.permissions || { entities: {} });

  // Met à jour la mémoire tampon si l'utilisateur change.
  const permissions = computed(() => {
    if (_cache.userId !== userId.value) {
      _cache.userId = userId.value;
      _cache.permissions.value = rawPermissions.value || { entities: {} };
    } else {
      // même user : on garde la ref (mais on peut quand même actualiser si Inertia change la structure)
      // pour éviter des valeurs périmées après navigation
      _cache.permissions.value = rawPermissions.value || { entities: {} };
    }
    return _cache.permissions.value;
  });

  const can = (entityType, ability) => {
    const e = normalizeEntityType(entityType);
    const a = normalizeAbility(ability);
    const entry = permissions.value?.entities?.[e];

    // viewAny est souvent "public", mais si rien n'est exposé, on retourne false par défaut.
    if (!entry) return false;

    // Certains abilities (view/update/delete) sont par instance => non gérés ici.
    // Ce registry expose surtout les abilities "Any" + create.
    return Boolean(entry?.[a]);
  };

  /**
   * Permissions d'accès UI (ex: menu administration/scrapping).
   *
   * @param {string} key
   * @returns {boolean}
   *
   * @example
   * if (canAccess('adminPanel')) { ... }
   */
  const canAccess = (key) => {
    const k = String(key || "").trim();
    if (!k) return false;
    return Boolean(permissions.value?.access?.[k]);
  };

  // Helpers courants
  const canViewAny = (entityType) => can(entityType, "viewAny");
  // Backward compatible (ancien naming)
  const canReadAny = canViewAny;
  const canCreate = (entityType) => can(entityType, "create");
  const canCreateAny = (entityType) => can(entityType, "createAny");
  const canUpdateAny = (entityType) => can(entityType, "updateAny");
  const canDeleteAny = (entityType) => can(entityType, "deleteAny");
  const canManageAny = (entityType) => can(entityType, "manageAny");

  return {
    permissions,
    can,
    canAccess,
    canViewAny,
    canReadAny,
    canCreate,
    canCreateAny,
    canUpdateAny,
    canDeleteAny,
    canManageAny,
    // auth
    authUser,
    isAuthenticated,
    isAdmin,
    isSuperAdmin,
  };
}

export default usePermissions;


