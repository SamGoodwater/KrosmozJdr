/**
 * usePageFormOptions
 *
 * @description
 * Centralise les options réutilisées dans les formulaires Page (Create/Edit) :
 * - options d'état (state)
 * - options de rôles (read_level / write_level)
 * - options de page parente (exclut la page courante si fournie)
 *
 * @example
 * const { stateOptions, roleOptions, parentPageOptions } =
 *   usePageFormOptions(() => props.pages, computed(() => currentPageId))
 *
 * @param {Function} getPages - Fonction retournant un tableau de pages
 * @param {import('vue').ComputedRef<number|null>} currentPageId - ID de la page courante (optionnel)
 * @returns {{ stateOptions: import('vue').ComputedRef<Array>, roleOptions: import('vue').ComputedRef<Array>, parentPageOptions: import('vue').ComputedRef<Array> }}
 */
import { computed } from 'vue';
import { getEntityStateOptions, getUserRoleOptions } from '@/Utils/Entity/SharedConstants';
import { Page } from '@/Models';

export function usePageFormOptions(getPages, currentPageId = computed(() => null)) {
  const stateOptions = computed(() => getEntityStateOptions());
  const roleOptions = computed(() => getUserRoleOptions());

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

  return { stateOptions, roleOptions, parentPageOptions };
}


