/**
 * useEntityIndexTableIntents — handlers keyboard-intent communs aux Index entités (Q8)
 *
 * @description
 * Centralise open-show-page (Ctrl/Cmd), open-view (modal full), open-edit (Alt) avec garde permissions.
 *
 * @example
 * const { handleKeyboardIntent } = useEntityIndexTableIntents({
 *   entityType: 'spells',
 *   ModelClass: Spell,
 *   routeShowName: 'entities.spells.show',
 *   routeShowParam: 'spell',
 *   canModify: () => canModify.value,
 *   openFullModal: (model) => { selectedEntity.value = model; modalOpen.value = true; },
 *   openEdit: (model) => openSpellEditModal(model.id),
 * });
 */
import { router } from "@inertiajs/vue3";
import { useNotificationStore } from "@/Composables/store/useNotificationStore";

/**
 * @param {object} options
 * @param {string} options.entityType - pluriel registre (ex. spells)
 * @param {new (raw: object) => object} options.ModelClass - classe modèle (Spell, Item, …)
 * @param {string} options.routeShowName - nom route Ziggy show
 * @param {string} options.routeShowParam - clé param route (ex. spell)
 * @param {() => boolean} [options.canModify] - droit update
 * @param {(model: object) => void} options.openFullModal - ouvre EntityModal en full
 * @param {(model: object) => void} [options.openEdit] - édition (modal ou page)
 * @param {string} [options.noEditMessage] - message si Alt+clic sans droit
 */
export function useEntityIndexTableIntents(options) {
    const notificationStore = useNotificationStore();
    const {
        ModelClass,
        routeShowName,
        routeShowParam,
        canModify = () => true,
        openFullModal,
        openEdit,
        noEditMessage = "Vous n'avez pas les droits pour modifier cette entité.",
    } = options;

    function resolveModel(row) {
        const raw = row?.rowParams?.entity;
        if (!raw) return null;
        if (raw instanceof ModelClass) return raw;
        const list = ModelClass.fromArray?.([raw]);
        return list?.[0] ?? null;
    }

    /**
     * @param {{ type?: string, row?: object }} payload
     */
    function handleKeyboardIntent(payload) {
        const { type, row } = payload || {};
        const model = resolveModel(row);
        if (!model?.id) return;

        switch (type) {
            case "open-show-page":
                router.visit(route(routeShowName, { [routeShowParam]: model.id }));
                break;
            case "open-view":
                openFullModal(model);
                break;
            case "open-edit":
                if (!canModify()) {
                    notificationStore.warning(noEditMessage, { duration: 4000, placement: "top-right" });
                    return;
                }
                if (typeof openEdit === "function") {
                    openEdit(model);
                }
                break;
            default:
                break;
        }
    }

    return { handleKeyboardIntent, resolveModel };
}
