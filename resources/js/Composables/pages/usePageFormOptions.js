/**
 * usePageFormOptions
 *
 * @description
 * Centralise les options réutilisées dans les formulaires Page (Create/Edit) :
 * - options d'état (state)
 * - options de rôles (read_level / write_level)
 * - options de page parente (exclut la page courante si fournie)
 * - options entity_key (pour box-shadow et icône)
 *
 * @example
 * const { stateOptions, roleOptions, parentPageOptions, entityKeyOptions } =
 *   usePageFormOptions(() => props.pages, computed(() => currentPageId))
 *
 * @param {Function} getPages - Fonction retournant un tableau de pages
 * @param {import('vue').ComputedRef<number|null>} currentPageId - ID de la page courante (optionnel)
 * @returns {{ stateOptions, roleOptions, parentPageOptions, entityKeyOptions }}
 */
import { computed } from 'vue';
import { getEntityStateOptions, getUserRoleOptions } from '@/Utils/Entity/SharedConstants';
import { getEntityConfig, ENTITY_KEYS } from '@/config/entities';
import { Page } from '@/Models';

export function usePageFormOptions(getPages, currentPageId = computed(() => null)) {
  const stateOptions = computed(() => getEntityStateOptions());
  const roleOptions = computed(() => getUserRoleOptions());

  const entityKeyOptions = computed(() => [
    { value: null, label: 'Aucune' },
    ...ENTITY_KEYS.map((key) => {
      const config = getEntityConfig(key);
      return { value: key, label: config.label };
    }),
  ]);

  const parentPageOptions = computed(() => {
    const pages = typeof getPages === 'function' ? (getPages() || []) : [];
    const currentId = currentPageId.value ?? null;

    return [
      { value: null, label: 'Aucune (page racine)' },
      ...pages
        .filter((p) => {
          try {
            const page = new Page(p);
            return currentId === null ? true : page.id !== currentId;
          } catch {
            return true;
          }
        })
        .map((p) => {
          const page = new Page(p);
          return { value: page.id, label: page.title };
        }),
    ];
  });

  return { stateOptions, roleOptions, parentPageOptions, entityKeyOptions };
}


