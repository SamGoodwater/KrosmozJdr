/**
 * useEntityPermissions Composable (legacy compat)
 *
 * @description
 * Ancien composable utilisé par de nombreuses pages. Il est conservé pour compatibilité,
 * mais s'appuie désormais sur le registry de permissions backend (via `usePermissions`).
 *
 * @example
 * const { canCreateEntity } = useEntityPermissions();
 * const canCreate = canCreateEntity('item'); // accepte singulier historique
 */
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import { User } from "@/Models";
import { usePermissions } from "@/Composables/permissions/usePermissions";

export function useEntityPermissions() {
  const page = usePage();
  const { canCreate } = usePermissions();

  /**
   * Récupère l'utilisateur connecté
   */
  const user = computed(() => {
    const authUser = page.props.auth?.user;
    if (!authUser) return null;
    return authUser instanceof User ? authUser : new User(authUser);
  });

  /**
   * Compat: certains appels historiques utilisent le singulier (item, monster...)
   * Le registry principal utilise le plural (items, monsters...) => fallback intelligent.
   */
  const pluralMap = {
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
    classe: "classes",
    resource: "resources",
    resourceType: "resource-types",
    specialization: "specializations",
    capability: "capabilities",
  };

  /**
   * Vérifie si l'utilisateur peut créer une entité (create).
   * Source of truth: backend permissions.
   */
  const canCreateEntity = (entityType) => {
    if (!user.value) return false;
    const key = pluralMap[String(entityType)] || String(entityType);
    if (canCreate(key)) return true;
    // fallback legacy (si entité absente du registry)
    return user.value.isAdmin;
  };

  const isAuthenticated = computed(() => !!user.value);
  const isAdmin = computed(() => user.value?.isAdmin || false);
  const isSuperAdmin = computed(() => user.value?.isSuperAdmin || false);

  return {
    user,
    canCreateEntity,
    isAuthenticated,
    isAdmin,
    isSuperAdmin,
  };
}

export default useEntityPermissions;


