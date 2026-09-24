/**
 * Ouverture programmatique de la recherche globale (dock mobile, raccourcis, etc.).
 *
 * `SearchInput` écoute `openNonce` et appelle son `openSearch`.
 *
 * @example
 * import { requestOpenGlobalSearch } from '@/Composables/layout/useGlobalSearchUi';
 * requestOpenGlobalSearch();
 */
import { ref, readonly } from 'vue';

const openNonce = ref(0);

/**
 * Demande l’ouverture de l’overlay de recherche globale.
 */
export function requestOpenGlobalSearch() {
    openNonce.value += 1;
}

/**
 * @returns {{ openNonce: import('vue').Readonly<import('vue').Ref<number>>, requestOpenGlobalSearch: () => void }}
 */
export function useGlobalSearchUi() {
    return {
        openNonce: readonly(openNonce),
        requestOpenGlobalSearch,
    };
}
