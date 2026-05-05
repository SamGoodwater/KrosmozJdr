import { computed, onUnmounted, ref } from "vue";
import { DEFAULT_OVERLAY_OPTIONS, OVERLAY_TRIGGER } from "@/Composables/overlay/overlayConstants";

/**
 * @param {{
 * trigger?: string,
 * contentKind?: import('vue').Ref<string>|string,
 * onOpen: ()=>void,
 * onClose: ()=>void,
 * onToggle: ()=>void,
 * }} options
 */
export function useOverlayTrigger(options) {
    const closeTimer = ref(null);
    const openTimer = ref(null);

    const computedTrigger = computed(() => {
        const requested = options.trigger || OVERLAY_TRIGGER.AUTO;
        if (requested !== OVERLAY_TRIGGER.AUTO) return requested;
        const kind =
            typeof options.contentKind === "object" && options.contentKind?.value != null
                ? options.contentKind.value
                : String(options.contentKind || "");
        return kind === "text" || kind === "html" ? OVERLAY_TRIGGER.HOVER : OVERLAY_TRIGGER.CLICK;
    });

    function clearTimers() {
        if (openTimer.value) {
            clearTimeout(openTimer.value);
            openTimer.value = null;
        }
        if (closeTimer.value) {
            clearTimeout(closeTimer.value);
            closeTimer.value = null;
        }
    }

    function onTriggerEnter() {
        if (computedTrigger.value !== OVERLAY_TRIGGER.HOVER) return;
        clearTimers();
        openTimer.value = setTimeout(options.onOpen, DEFAULT_OVERLAY_OPTIONS.hoverOpenDelayMs);
    }

    function onTriggerLeave() {
        if (computedTrigger.value !== OVERLAY_TRIGGER.HOVER) return;
        clearTimers();
        closeTimer.value = setTimeout(options.onClose, DEFAULT_OVERLAY_OPTIONS.hoverCloseDelayMs);
    }

    function onTriggerClick(event) {
        if (computedTrigger.value !== OVERLAY_TRIGGER.CLICK) return;
        event.preventDefault();
        options.onToggle();
    }

    function onTriggerFocusIn() {
        // Keyboard focus should open hover-style overlays (tooltip behavior),
        // but must not auto-open click/auto(click) overlays on mount/focus.
        if (computedTrigger.value !== OVERLAY_TRIGGER.HOVER) return;
        options.onOpen();
    }

    function onTriggerFocusOut() {
        if (computedTrigger.value !== OVERLAY_TRIGGER.HOVER) return;
        onTriggerLeave();
    }

    function onPanelEnter() {
        if (computedTrigger.value !== OVERLAY_TRIGGER.HOVER) return;
        clearTimers();
    }

    function onPanelLeave() {
        onTriggerLeave();
    }

    onUnmounted(clearTimers);

    return {
        computedTrigger,
        onTriggerEnter,
        onTriggerLeave,
        onTriggerClick,
        onTriggerFocusIn,
        onTriggerFocusOut,
        onPanelEnter,
        onPanelLeave,
        clearTimers,
    };
}
