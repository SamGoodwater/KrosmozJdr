<script setup>
/**
 * EntityActionMenuList — liste commune pour dropdown et menu clic droit.
 *
 * @description
 * Rend les mêmes items d'action partout, avec navigation clavier et états locaux.
 *
 * @example
 * <EntityActionMenuList :actions="actions" :grouped-actions="groups" @action="run" />
 */
import { computed, nextTick, onMounted, ref, watch } from "vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import EntityStateAction from "@/Pages/Molecules/entity/EntityStateAction.vue";
import { useResolvedEntityActionState } from "@/Composables/entity/useResolvedEntityActionState";

const props = defineProps({
    entityType: { type: String, default: "" },
    entity: { type: Object, default: null },
    actions: { type: Array, default: () => [] },
    groupedActions: { type: Object, default: () => ({}) },
    display: {
        type: String,
        default: "icon-text",
        validator: (v) => ["icon-only", "icon-text"].includes(v),
    },
    size: { type: String, default: "sm" },
    entityName: { type: String, default: "" },
    focusOnMount: { type: Boolean, default: false },
    menuClass: {
        type: String,
        default: "menu bg-base-100 rounded-box w-56 p-2 shadow-lg border border-base-300",
    },
});

const emit = defineEmits(["action", "close"]);

const listRef = ref(null);
const showIcon = computed(() => props.display === "icon-only" || props.display === "icon-text");
const showEntityName = computed(() => Boolean(props.entityName));
const sourceActions = computed(() => props.actions);
const { resolvedActions, runLocalAction } = useResolvedEntityActionState(
    computed(() => props.entityType),
    computed(() => props.entity),
    sourceActions,
);

const orderedGroups = computed(() => {
    const groupKeys = Object.keys(props.groupedActions || {});
    return groupKeys.length ? groupKeys : ["all"];
});

const getGroupActions = (groupKey) => {
    if (groupKey === "all") return resolvedActions.value;
    return (props.groupedActions[groupKey] || []).map(
        (a) => resolvedActions.value.find((r) => r?.key === a?.key) || a,
    );
};

function getButtons() {
    const root = listRef.value;
    if (!root) return [];
    return [...root.querySelectorAll("li button")];
}

function focusFirstItem() {
    nextTick(() => getButtons()[0]?.focus?.());
}

onMounted(() => {
    if (props.focusOnMount) focusFirstItem();
});

watch(
    () => props.focusOnMount,
    (on) => {
        if (on) focusFirstItem();
    },
    { immediate: true },
);

function handleAction(actionKey) {
    if (runLocalAction(actionKey)) {
        emit("action", actionKey);
        return;
    }
    emit("action", actionKey);
}

function onKeydown(e) {
    const buttons = getButtons();
    if (!buttons.length) return;

    const key = e.key;
    const idx = buttons.indexOf(document.activeElement);

    if (key === "Escape") {
        e.preventDefault();
        e.stopPropagation();
        emit("close");
        return;
    }
    if (key === "ArrowDown") {
        e.preventDefault();
        buttons[idx < 0 ? 0 : Math.min(buttons.length - 1, idx + 1)]?.focus?.();
        return;
    }
    if (key === "ArrowUp") {
        e.preventDefault();
        buttons[idx <= 0 ? buttons.length - 1 : idx - 1]?.focus?.();
        return;
    }
    if (key === "Home") {
        e.preventDefault();
        buttons[0]?.focus?.();
        return;
    }
    if (key === "End") {
        e.preventDefault();
        buttons[buttons.length - 1]?.focus?.();
    }
}
</script>

<template>
    <ul
        ref="listRef"
        tabindex="-1"
        role="menu"
        :class="menuClass"
        @keydown="onKeydown"
        @pointerdown.stop
        @mousedown.stop
        @mouseup.stop
        @click.stop
        @contextmenu.prevent.stop
    >
        <li v-if="showEntityName" class="px-3 py-2 mb-1 border-b border-base-300" aria-hidden="true">
            <div class="text-xs text-base-content/60 font-medium truncate" :title="entityName">
                {{ entityName }}
            </div>
        </li>

        <template v-for="groupKey in orderedGroups" :key="groupKey">
            <li
                v-for="action in getGroupActions(groupKey)"
                :key="action.key"
                :class="{ 'text-error': action.variant === 'error' }"
            >
                <EntityStateAction
                    v-if="action.key === 'state'"
                    :entity-type="entityType"
                    :entity="entity"
                    :action="action"
                    display="icon-text"
                    :size="size"
                    mode="menu"
                    @action="handleAction"
                />
                <button
                    v-else
                    type="button"
                    role="menuitem"
                    class="entity-actions-menu-item flex items-center gap-2 w-full text-left px-2 py-1.5 rounded-lg transition-[transform,background-color,box-shadow] duration-150 ease-out outline-none hover:bg-base-200/90 focus-visible:bg-primary/12 focus-visible:ring-2 focus-visible:ring-primary/45 focus-visible:scale-[1.02] motion-reduce:transition-none motion-reduce:focus-visible:scale-100"
                    :class="{
                        'text-error': action.variant === 'error',
                        'entity-action-local-active text-primary!': action.active && action.variant !== 'error',
                    }"
                    :title="action.tooltip || action.label"
                    @pointerdown.stop
                    @mousedown.stop
                    @mouseup.stop
                    @click.stop="handleAction(action.key)"
                >
                    <Icon
                        v-if="showIcon"
                        :source="action.icon"
                        :alt="action.label"
                        :size="size"
                        :class="{ 'entity-action-local-active-icon': action.active }"
                    />
                    <span class="truncate">{{ action.label }}</span>
                    <span v-if="action.badge" class="badge badge-sm badge-primary ml-auto">{{ action.badge }}</span>
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

<style scoped>
.entity-action-local-active {
    text-shadow: 0 0 8px color-mix(in srgb, var(--color-primary, #60a5fa) 70%, transparent);
}

.entity-action-local-active-icon {
    filter: drop-shadow(0 0 6px color-mix(in srgb, var(--color-primary, #60a5fa) 78%, transparent));
}
</style>
