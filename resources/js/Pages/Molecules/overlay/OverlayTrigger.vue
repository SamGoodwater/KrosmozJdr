<script setup>
import { computed, nextTick, watch } from "vue";
import { useOverlay } from "@/Composables/overlay/useOverlay";
import { OVERLAY_MAX_WIDTH_CLASS, OVERLAY_Z_INDEX } from "@/Composables/overlay/overlayConstants";
import { sanitizeHtml } from "@/Utils/security/sanitizeHtml";

const props = defineProps({
    content: { type: [String, Object, Function], default: "" },
    trigger: { type: String, default: "auto" },
    placement: { type: String, default: "top" },
    maxWidth: { type: String, default: "auto" },
    interactive: { type: Boolean, default: false },
    focusTrap: { type: Boolean, default: false },
    lazy: { type: Boolean, default: true },
    cache: { type: [Boolean, Object], default: true },
    closeOnOutside: { type: Boolean, default: true },
    closeOnEscape: { type: Boolean, default: true },
    panelClass: { type: String, default: "" },
    renderer: { type: [Object, Function], default: null },
    offsetPx: { type: Number, default: 8 },
    allowFlip: { type: Boolean, default: true },
    /**
     * Sans chrome (fond, padding, bordure) : le contenu fournit déjà sa surface
     * (ex. fiche minimale d’entité).
     */
    chromeless: { type: Boolean, default: false },
});

const emit = defineEmits(["open", "close", "error"]);

const { service, position, trigger, a11y } = useOverlay({
    get content() {
        return props.content;
    },
    trigger: props.trigger,
    placement: props.placement,
    maxWidth: props.maxWidth,
    interactive: props.interactive,
    focusTrap: props.focusTrap,
    lazy: props.lazy,
    cache: props.cache,
    closeOnOutside: props.closeOnOutside,
    closeOnEscape: props.closeOnEscape,
    renderer: props.renderer,
    offsetPx: props.offsetPx,
    allowFlip: props.allowFlip,
});
const { triggerRef, overlayRef, floatingStyles, teleportTarget } = position;
const { isOpen, loading, error, resolved, resolvedKind } = service;
const { triggerAttrs, panelAttrs } = a11y;

const panelMaxWidthClass = computed(() => OVERLAY_MAX_WIDTH_CLASS[props.maxWidth] || "");
const panelPointerEventsClass = computed(() =>
    props.interactive ? "pointer-events-auto" : "pointer-events-none",
);
const panelStyle = computed(() => ({
    ...(floatingStyles?.value || {}),
    zIndex: OVERLAY_Z_INDEX.floatingPanel,
}));
const isPositionReady = computed(() => {
    const styles = floatingStyles?.value;
    if (!styles || typeof styles !== "object") return false;
    const hasTop = styles.top != null || styles.bottom != null;
    const hasLeft = styles.left != null || styles.right != null;
    return Boolean(styles.position) && hasTop && hasLeft;
});

const panelContent = computed(() => {
    if (resolvedKind.value === "component") {
        return resolved.value || null;
    }
    return null;
});

const safeHtml = computed(() => (typeof resolved.value === "string" ? sanitizeHtml(resolved.value) : ""));

watch(
    () => isOpen.value,
    async (open) => {
        if (!open) {
            emit("close");
            return;
        }
        emit("open");
        await nextTick();
        a11y.onOpen(overlayRef.value);
    }
);

watch(
    () => error.value,
    (err) => {
        if (!err) return;
        emit("error", err);
    }
);

function handleKeydown(event) {
    a11y.onPanelKeydown(event, overlayRef.value);
}
</script>

<template>
    <span
        ref="triggerRef"
        class="inline-flex min-w-0"
        v-bind="triggerAttrs"
        @mouseenter="trigger.onTriggerEnter"
        @mouseleave="trigger.onTriggerLeave"
        @focusin="trigger.onTriggerFocusIn"
        @focusout="trigger.onTriggerFocusOut"
        @click="trigger.onTriggerClick"
    >
        <slot />
    </span>

    <Teleport v-if="isOpen" :to="teleportTarget">
        <div
            ref="overlayRef"
            :class="[
                chromeless ? 'tooltip-floating-chromeless' : 'tooltip-floating-surface color-neutral',
                panelPointerEventsClass,
                panelMaxWidthClass,
                panelClass,
            ]"
            :style="panelStyle"
            :aria-hidden="!isPositionReady"
            v-show="isPositionReady"
            v-bind="panelAttrs"
            @mouseenter="trigger.onPanelEnter"
            @mouseleave="trigger.onPanelLeave"
            @keydown="handleKeydown"
        >
            <div v-if="loading" class="flex items-center gap-2 p-2 text-xs text-base-content/70">
                <span class="loading loading-spinner loading-xs"></span>
                <span>Chargement...</span>
            </div>

            <component
                :is="props.renderer"
                v-else-if="props.renderer && resolvedKind === 'component'"
                v-bind="resolved?.props || {}"
            />

            <component
                :is="panelContent?.component"
                v-else-if="resolvedKind === 'component' && panelContent?.component"
                v-bind="panelContent?.props || {}"
            />

            <!-- eslint-disable vue/no-v-html -->
            <div v-else-if="resolvedKind === 'html'" class="p-2 text-sm" v-html="safeHtml"></div>
            <!-- eslint-enable vue/no-v-html -->

            <div v-else class="p-2 text-sm">
                {{ resolved }}
            </div>
        </div>
    </Teleport>
</template>
