/**
 * useEntityIndexQuickEditTable
 *
 * @description
 * État partagé : toggle quick edit persisté (via tableau), ouverture modal quick edit au clic ligne.
 *
 * @param {object} Model - Classe modèle (ex: Spell) avec .fromArray
 * @returns {{
 *   tableQuickEditEnabled: import('vue').Ref<boolean>,
 *   quickEditModalOpen: import('vue').Ref<boolean>,
 *   quickEditEntity: import('vue').Ref<object|null>,
 *   onUpdateTableQuickEdit: (v: boolean) => void,
 *   onQuickEditIntent: (row: object) => void,
 * }}
 */
import { ref } from "vue";

export function useEntityIndexQuickEditTable(Model) {
    const tableQuickEditEnabled = ref(true);
    const quickEditModalOpen = ref(false);
    const quickEditEntity = ref(null);

    const onUpdateTableQuickEdit = (v) => {
        tableQuickEditEnabled.value = Boolean(v);
    };

    const onQuickEditIntent = (row) => {
        const raw = row?.rowParams?.entity;
        if (!raw) return;
        const model = raw instanceof Model ? raw : Model.fromArray([raw])?.[0];
        if (!model?.id) return;
        quickEditEntity.value = model;
        quickEditModalOpen.value = true;
    };

    return {
        tableQuickEditEnabled,
        quickEditModalOpen,
        quickEditEntity,
        onUpdateTableQuickEdit,
        onQuickEditIntent,
    };
}
