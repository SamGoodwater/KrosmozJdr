<script setup>
/**
 * EntityActionsDropdown Molecule
 *
 * @description
 * Menu dropdown d'actions pour une entité + raccourcis inline (écran ≥ sm) :
 * épingler, copier le lien, ouvrir la page, édition rapide (si droits).
 * Le déclencheur « ⋮ » reste toujours visible.
 *
 * @example
 * <EntityActionsDropdown
 *   entity-type="spells"
 *   :actions="availableActions"
 *   :grouped-actions="groupedActions"
 *   display="icon-only"
 *   size="sm"
 *   placement="bottom-end"
 *   @action="handleAction"
 * />
 */
import Dropdown from "@/Pages/Atoms/action/Dropdown.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import { computed } from "vue";
import EntityActionMenuList from "@/Pages/Molecules/entity/EntityActionMenuList.vue";
import EntityStateAction from "@/Pages/Molecules/entity/EntityStateAction.vue";
import { useResolvedEntityActionState } from "@/Composables/entity/useResolvedEntityActionState";

const props = defineProps({
    /**
     * Type d’entité plural (ex. `spells`) — requis pour l’épinglage local.
     */
    entityType: {
        type: String,
        default: "",
    },
    /**
     * Liste des actions disponibles (format plat).
     * @type {Array<Object>}
     */
    actions: {
        type: Array,
        required: true,
        default: () => [],
    },
    /**
     * Actions groupées par groupe (pour séparateurs).
     * @type {Object}
     */
    groupedActions: {
        type: Object,
        default: () => ({}),
    },
    /**
     * Entité (pour afficher le nom en haut du menu).
     * @type {Object|null}
     */
    entity: {
        type: Object,
        default: null,
    },
    /**
     * Mode d'affichage : 'icon-only' ou 'icon-text'
     */
    display: {
        type: String,
        default: "icon-text",
        validator: (v) => ["icon-only", "icon-text"].includes(v),
    },
    /**
     * Taille des boutons (xs, sm, md, lg)
     */
    size: {
        type: String,
        default: "sm",
    },
    /**
     * Couleur des boutons (primary, secondary, etc.)
     */
    color: {
        type: String,
        default: "primary",
    },
    /**
     * Position du dropdown (bottom-end, top-start, etc.)
     */
    placement: {
        type: String,
        default: "bottom-end",
    },
    /**
     * Afficher uniquement l'icône dans le trigger (pour colonne Actions)
     */
    iconOnlyTrigger: {
        type: Boolean,
        default: true,
    },
    /**
     * Variant du trigger (ghost, outline, etc.)
     */
    triggerVariant: {
        type: String,
        default: "ghost",
    },
    /**
     * Ordre des raccourcis inline (clés d’action).
     */
    inlineActionKeys: {
        type: Array,
        default: () => ["state", "pin", "quick-view", "quick-edit", "view-dofusdb", "favorite", "copy-link", "view", "edit"],
    },
    showInlineShortcuts: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(["action"]);

const sourceActions = computed(() => props.actions);
const { resolvedActions, runLocalAction } = useResolvedEntityActionState(
    computed(() => props.entityType),
    computed(() => props.entity),
    sourceActions,
);

const promotedActions = computed(() => {
    if (!props.showInlineShortcuts) return [];
    const keys = Array.isArray(props.inlineActionKeys) ? props.inlineActionKeys : [];
    const source = resolvedActions.value;
    const out = [];
    for (const key of keys) {
        const action = source.find((a) => a?.key === key);
        if (action) out.push(action);
    }
    return out;
});

/**
 * @param {string} actionKey
 */
async function handleShortcutClick(actionKey) {
    if (await runLocalAction(actionKey)) {
        emit("action", actionKey);
        return;
    }
    emit("action", actionKey);
}

/**
 * @param {string} actionKey
 */
function handleMenuAction(actionKey) {
    emit("action", actionKey);
}

/**
 * Récupère le nom de l'entité en gérant les modèles et objets bruts.
 */
const getEntityName = () => {
    if (!props.entity) return null;

    if (props.entity && typeof props.entity._data !== "undefined") {
        return props.entity.name || props.entity.title || null;
    }
    return props.entity?.name || props.entity?.title || null;
};

const entityName = computed(() => getEntityName());
</script>

<template>
    <div class="flex min-w-0 items-center justify-end gap-0.5">
        <!-- Raccourcis : visibles à partir du breakpoint `sm` (place viewport) -->
        <div
            v-if="promotedActions.length"
            class="hidden min-[640px]:flex min-[640px]:items-center min-[640px]:gap-0.5"
        >
            <template v-for="action in promotedActions" :key="action.key">
                <EntityStateAction
                    v-if="action.key === 'state'"
                    :entity-type="entityType"
                    :entity="entity"
                    :action="action"
                    display="icon-only"
                    :size="size"
                    @action="handleShortcutClick"
                />
                <Btn
                    v-else
                    :size="size"
                    variant="ghost"
                    :color="color"
                    class="btn-square shrink-0"
                    :class="{
                        'pin-active text-primary!': action.active,
                    }"
                    :title="action.tooltip || action.label"
                    @click.stop="handleShortcutClick(action.key)"
                >
                    <Icon
                        :source="action.icon"
                        :size="size"
                        :class="{
                            'pin-active-icon': action.active,
                        }"
                    />
                </Btn>
            </template>
        </div>
        <Dropdown :placement="placement" :close-on-content-click="true">
            <template #trigger>
                <Btn
                    :size="size"
                    :variant="triggerVariant"
                    :color="color"
                    :class="iconOnlyTrigger ? 'btn-square shrink-0' : ''"
                    :title="iconOnlyTrigger ? 'Autres actions' : null"
                >
                    <Icon source="fa-solid fa-ellipsis-vertical" :size="size" />
                    <span v-if="!iconOnlyTrigger" class="ml-2">Actions</span>
                </Btn>
            </template>
            <template #content>
                <EntityActionMenuList
                    :entity-type="entityType"
                    :entity="entity"
                    :actions="actions"
                    :grouped-actions="groupedActions"
                    :display="display"
                    :size="size"
                    :entity-name="entityName || ''"
                    menu-class="menu bg-base-100 rounded-box z-1 w-56 p-2 shadow-lg border border-base-300"
                    @action="handleMenuAction"
                />
            </template>
        </Dropdown>
    </div>
</template>

<style scoped>
.pin-active {
    text-shadow: 0 0 8px color-mix(in srgb, var(--color-primary, #60a5fa) 70%, transparent);
}

.pin-active-icon {
    transform: rotate(-38deg);
    transition: transform 180ms ease, filter 180ms ease;
    filter: drop-shadow(0 0 6px color-mix(in srgb, var(--color-primary, #60a5fa) 78%, transparent));
}
</style>
