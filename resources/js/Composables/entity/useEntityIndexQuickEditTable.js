/**
 * useEntityIndexQuickEditTable
 *
 * @description
 * État partagé : toggle quick edit persisté (via tableau). La sélection des lignes
 * est gérée par {@link TanStackTable} (`clickToSelect`) ; le panneau
 * {@link EntityQuickEditPanel} réagit à `selectedIds` + `tableQuickEditEnabled`.
 * Le simple clic ne fait que sélectionner ; Alt+clic et l’action Éditer ouvrent la page Modifier.
 *
 * @param {object} Model - Classe modèle (ex: Spell) avec .fromArray
 * @returns {{
 *   tableQuickEditEnabled: import('vue').Ref<boolean>,
 *   onUpdateTableQuickEdit: (v: boolean) => void,
 * }}
 */
import { ref } from "vue";

export function useEntityIndexQuickEditTable(_Model) {
    const tableQuickEditEnabled = ref(false);

    const onUpdateTableQuickEdit = (v) => {
        tableQuickEditEnabled.value = Boolean(v);
    };

    return {
        tableQuickEditEnabled,
        onUpdateTableQuickEdit,
    };
}
