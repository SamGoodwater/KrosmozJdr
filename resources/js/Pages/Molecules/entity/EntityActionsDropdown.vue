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
import {
    isEntityPinned,
    toggleEntityPin,
    usePinnedEntityVersion,
} from "@/Composables/entity/usePinnedEntityIds";
import { useUxFeedback } from "@/Composables/utils/useUxFeedback";

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
        default: () => ["pin", "copy-link", "view", "edit", "quick-view", "quick-edit"],
    },
    showInlineShortcuts: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(["action"]);

const pinVersion = usePinnedEntityVersion();
const { notifySuccess } = useUxFeedback();

const showIcon = computed(() => props.display === "icon-only" || props.display === "icon-text");

const entityIdStr = computed(() => {
    const e = props.entity;
    const id = e?.id ?? e?._data?.id;
    if (id == null || id === "") return "";
    return String(id);
});

const pinned = computed(() => {
    pinVersion.value;
    const et = String(props.entityType || "").trim();
    if (!et || !entityIdStr.value) return false;
    return isEntityPinned(et, entityIdStr.value);
});

/**
 * Actions avec libellé/icône dynamiques pour `pin`.
 */
const resolvedActions = computed(() => {
    const list = Array.isArray(props.actions) ? props.actions : [];
    return list.map((a) => {
        if (a?.key !== "pin") return a;
        return {
            ...a,
            label: pinned.value ? "Désépingler" : "Épingler",
            tooltip: pinned.value
                ? "Retirer des fiches épinglées (local)"
                : a.tooltip || "Épingler cette fiche (local)",
            icon: "fa-solid fa-thumbtack",
        };
    });
});

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

function runPinToggle() {
    const et = String(props.entityType || "").trim();
    if (!et || !entityIdStr.value) return;
    const now = toggleEntityPin(et, entityIdStr.value);
    notifySuccess(now ? "Fiche épinglée (local)" : "Épinglage retiré");
    emit("action", "pin");
}

/**
 * @param {string} actionKey
 */
function handleShortcutClick(actionKey) {
    if (actionKey === "pin") {
        runPinToggle();
        return;
    }
    emit("action", actionKey);
}

/**
 * @param {string} actionKey
 */
function handleMenuAction(actionKey) {
    if (actionKey === "pin") {
        runPinToggle();
        return;
    }
    emit("action", actionKey);
}

/**
 * Retourne les groupes d'actions dans l'ordre, avec les actions non groupées à la fin.
 */
const orderedGroups = computed(() => {
    const groups = props.groupedActions;
    const groupKeys = Object.keys(groups);

    if (groupKeys.length > 0) {
        return groupKeys;
    }

    return ["all"];
});

/**
 * Retourne les actions d'un groupe (menu complet, y compris les raccourcis déjà visibles).
 */
const getGroupActions = (groupKey) => {
    if (groupKey === "all") {
        return resolvedActions.value;
    }
    return (props.groupedActions[groupKey] || []).map(
        (a) => resolvedActions.value.find((r) => r?.key === a?.key) || a,
    );
};

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
const showEntityName = computed(() => Boolean(entityName.value));
</script>

<template>
    <div class="flex min-w-0 items-center justify-end gap-0.5">
        <!-- Raccourcis : visibles à partir du breakpoint `sm` (place viewport) -->
        <div
            v-if="promotedActions.length"
            class="hidden min-[640px]:flex min-[640px]:items-center min-[640px]:gap-0.5"
        >
            <Btn
                v-for="action in promotedActions"
                :key="action.key"
                :size="size"
                variant="ghost"
                :color="color"
                class="btn-square shrink-0"
                :class="{
                    'pin-active text-primary!': action.key === 'pin' && pinned,
                }"
                :title="action.tooltip || action.label"
                @click.stop="handleShortcutClick(action.key)"
            >
                <Icon
                    :source="action.icon"
                    :size="size"
                    :class="{
                        'pin-active-icon': action.key === 'pin' && pinned,
                    }"
                />
            </Btn>
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
                <ul class="menu bg-base-100 rounded-box z-1 w-56 p-2 shadow-lg border border-base-300">
                    <li v-if="showEntityName" class="px-3 py-2 mb-1 border-b border-base-300">
                        <div class="text-xs text-base-content/60 font-medium truncate" :title="entityName">
                            {{ entityName }}
                        </div>
                    </li>

                    <template v-for="groupKey in orderedGroups" :key="groupKey">
                        <li
                            v-for="action in getGroupActions(groupKey)"
                            :key="action.key"
                            :class="{
                                'text-error': action.variant === 'error',
                            }"
                        >
                            <button
                                type="button"
                                class="flex items-center gap-2 w-full"
                                :class="{
                                    'text-error': action.variant === 'error',
                                }"
                                :title="action.tooltip || action.label"
                                @click="handleMenuAction(action.key)"
                            >
                                <Icon v-if="showIcon" :source="action.icon" :alt="action.label" :size="size" />
                                <span class="truncate">{{ action.label }}</span>
                                <span v-if="action.badge" class="badge badge-sm badge-primary ml-auto">{{
                                    action.badge
                                }}</span>
                            </button>
                        </li>

                        <li
                            v-if="
                                groupKey !== orderedGroups[orderedGroups.length - 1] &&
                                getGroupActions(orderedGroups[orderedGroups.indexOf(groupKey) + 1]).length > 0
                            "
                        >
                            <hr class="my-1" />
                        </li>
                    </template>
                </ul>
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
