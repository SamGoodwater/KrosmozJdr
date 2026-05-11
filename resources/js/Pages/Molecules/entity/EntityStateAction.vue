<script setup>
/**
 * EntityStateAction — pastille d'état dynamique dans la barre d'actions.
 *
 * @description
 * Affiche l'état courant et, si l'utilisateur peut modifier l'entité, ouvre une
 * liste d'états accessible au clavier pour appliquer le changement.
 *
 * @example
 * <EntityStateAction entity-type="items" :entity="item" :action="stateAction" display="icon-only" />
 */
import { computed, nextTick, ref } from "vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import Dropdown from "@/Pages/Atoms/action/Dropdown.vue";
import Tooltip from "@/Pages/Atoms/feedback/Tooltip.vue";
import { useEntityStateAction } from "@/Composables/entity/useEntityStateAction";

const props = defineProps({
    entityType: { type: String, default: "" },
    entity: { type: Object, default: null },
    action: { type: Object, required: true },
    display: {
        type: String,
        default: "icon-text",
        validator: (v) => ["icon-only", "icon-text"].includes(v),
    },
    size: { type: String, default: "sm" },
    mode: {
        type: String,
        default: "button",
        validator: (v) => ["button", "menu"].includes(v),
    },
});

const emit = defineEmits(["action", "updated"]);

const optionButtonsRef = ref([]);
const {
    canUpdate,
    dotClass,
    label,
    options,
    pending,
    tooltip,
    updateState,
} = useEntityStateAction(
    computed(() => props.entityType),
    computed(() => props.entity),
    computed(() => props.action),
);

const showLabel = computed(() => Boolean(props.action?.showStateLabel) || props.display === "icon-text");
const sizeClass = computed(() => (props.size === "xs" ? "w-2 h-2" : "w-2.5 h-2.5"));
const menuItemClass = computed(() => [
    "entity-actions-menu-item flex items-center gap-2 w-full text-left px-2 py-1.5 rounded-lg transition-[transform,background-color,box-shadow] duration-150 ease-out outline-none hover:bg-base-200/90 focus-visible:bg-primary/12 focus-visible:ring-2 focus-visible:ring-primary/45 focus-visible:scale-[1.02] motion-reduce:transition-none motion-reduce:focus-visible:scale-100",
    !canUpdate.value ? "opacity-75 cursor-default" : "",
]);

function setOptionButtonRef(el, index) {
    if (el) optionButtonsRef.value[index] = el;
}

function focusFirstOption() {
    optionButtonsRef.value = [];
    nextTick(() => optionButtonsRef.value[0]?.focus?.());
}

async function selectState(state) {
    const ok = await updateState(state);
    if (!ok) return;
    emit("updated", state);
    emit("action", "state");
}

function onOptionsKeydown(event) {
    const buttons = optionButtonsRef.value.filter(Boolean);
    if (!buttons.length) return;

    const idx = buttons.indexOf(document.activeElement);
    if (event.key === "ArrowDown") {
        event.preventDefault();
        buttons[idx < 0 ? 0 : Math.min(buttons.length - 1, idx + 1)]?.focus?.();
    } else if (event.key === "ArrowUp") {
        event.preventDefault();
        buttons[idx <= 0 ? buttons.length - 1 : idx - 1]?.focus?.();
    } else if (event.key === "Home") {
        event.preventDefault();
        buttons[0]?.focus?.();
    } else if (event.key === "End") {
        event.preventDefault();
        buttons[buttons.length - 1]?.focus?.();
    }
}
</script>

<template>
    <Dropdown
        v-if="canUpdate"
        placement="bottom-start"
        :close-on-content-click="true"
        :disabled="pending"
    >
        <template #trigger>
            <Tooltip :content="tooltip" placement="top">
                <button
                    v-if="mode === 'menu'"
                    type="button"
                    role="menuitem"
                    :class="menuItemClass"
                    :disabled="pending"
                    :title="tooltip"
                    @pointerdown.stop
                    @mousedown.stop
                    @mouseup.stop
                    @click="focusFirstOption"
                >
                    <span class="rounded-full ring-1 ring-base-300" :class="[sizeClass, dotClass]" aria-hidden="true" />
                    <span class="truncate">État : {{ label }}</span>
                </button>
                <Btn
                    v-else
                    :size="size"
                    variant="ghost"
                    color="neutral"
                    class="gap-2 shrink-0"
                    :class="{ 'btn-square': !showLabel }"
                    :disabled="pending"
                    :title="tooltip"
                    @click="focusFirstOption"
                >
                    <span class="rounded-full ring-1 ring-base-300" :class="[sizeClass, dotClass]" aria-hidden="true" />
                    <span v-if="showLabel" class="truncate">{{ label }}</span>
                    <span v-else class="sr-only">État : {{ label }}</span>
                </Btn>
            </Tooltip>
        </template>

        <template #content>
            <ul
                class="menu bg-base-100 rounded-box z-1 w-48 p-2 shadow-lg border border-base-300"
                role="menu"
                @keydown="onOptionsKeydown"
                @pointerdown.stop
                @mousedown.stop
                @mouseup.stop
                @click.stop
            >
                <li v-for="(option, index) in options" :key="option.value">
                    <button
                        :ref="(el) => setOptionButtonRef(el, index)"
                        type="button"
                        role="menuitemradio"
                        :aria-checked="option.active"
                        class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-left outline-none hover:bg-base-200 focus-visible:bg-primary/12 focus-visible:ring-2 focus-visible:ring-primary/45"
                        :class="{ 'font-semibold text-primary': option.active }"
                        :disabled="pending"
                        @click.stop="selectState(option.value)"
                    >
                        <span class="h-2.5 w-2.5 rounded-full ring-1 ring-base-300" :class="option.dotClass" aria-hidden="true" />
                        <span class="truncate">{{ option.label }}</span>
                    </button>
                </li>
            </ul>
        </template>
    </Dropdown>

    <Tooltip v-else :content="tooltip" placement="top">
        <button
            v-if="mode === 'menu'"
            type="button"
            role="menuitem"
            :class="menuItemClass"
            :title="tooltip"
            @click.stop
        >
            <span class="rounded-full ring-1 ring-base-300" :class="[sizeClass, dotClass]" aria-hidden="true" />
            <span class="truncate">État : {{ label }}</span>
        </button>
        <Btn
            v-else
            :size="size"
            variant="ghost"
            color="neutral"
            class="gap-2 shrink-0"
            :class="{ 'btn-square': !showLabel }"
            :title="tooltip"
        >
            <span class="rounded-full ring-1 ring-base-300" :class="[sizeClass, dotClass]" aria-hidden="true" />
            <span v-if="showLabel" class="truncate">{{ label }}</span>
            <span v-else class="sr-only">État : {{ label }}</span>
        </Btn>
    </Tooltip>
</template>
