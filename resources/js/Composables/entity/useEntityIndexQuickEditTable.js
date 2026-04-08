/**
 * useEntityIndexQuickEditTable
 *
 * @description
 * État partagé : toggle quick edit persisté (via tableau). La sélection des lignes
 * est gérée par {@link TanStackTable} (`clickToSelect`) ; le panneau
 * {@link EntityQuickEditPanel} réagit à `selectedIds` + `tableQuickEditEnabled`.
 * Aucune ouverture de modal au simple clic (celle-ci reste réservée au menu d’actions, Alt+clic, etc.).
 *
 * @param {object} Model - Classe modèle (ex: Spell) avec .fromArray
 * @returns {{
 *   tableQuickEditEnabled: import('vue').Ref<boolean>,
 *   quickEditModalOpen: import('vue').Ref<boolean>,
 *   quickEditEntity: import('vue').Ref<object|null>,
 *   onUpdateTableQuickEdit: (v: boolean) => void,
 * }}
 */
import { ref } from "vue";

export function useEntityIndexQuickEditTable(_Model) {
    const tableQuickEditEnabled = ref(true);
    const quickEditModalOpen = ref(false);
    const quickEditEntity = ref(null);

    const onUpdateTableQuickEdit = (v) => {
        tableQuickEditEnabled.value = Boolean(v);
    };

    return {
        tableQuickEditEnabled,
        quickEditModalOpen,
        quickEditEntity,
        onUpdateTableQuickEdit,
    };
}
