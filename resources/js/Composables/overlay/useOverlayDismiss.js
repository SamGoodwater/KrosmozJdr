import { onMounted, onUnmounted } from "vue";

/**
 * @param {{
 * openRef: import('vue').Ref<boolean>,
 * triggerRef: import('vue').Ref<HTMLElement|null>,
 * panelRef: import('vue').Ref<HTMLElement|null>,
 * onClose: (reason: string)=>void,
 * closeOnOutside?: boolean,
 * closeOnEscape?: boolean
 * }} options
 */
export function useOverlayDismiss(options) {
    const closeOnOutside = options.closeOnOutside ?? true;
    const closeOnEscape = options.closeOnEscape ?? true;

    function containsNode(node) {
        const t = options.triggerRef.value;
        const p = options.panelRef.value;
        return (t instanceof HTMLElement && t.contains(node)) || (p instanceof HTMLElement && p.contains(node));
    }

    function onDocPointer(event) {
        if (!options.openRef.value || !closeOnOutside) return;
        const target = event.target;
        if (!(target instanceof Node)) return;
        if (containsNode(target)) return;
        options.onClose("outside");
    }

    function onDocKey(event) {
        if (!options.openRef.value || !closeOnEscape) return;
        if (event.key !== "Escape") return;
        event.preventDefault();
        options.onClose("escape");
    }

    onMounted(() => {
        document.addEventListener("mousedown", onDocPointer, true);
        document.addEventListener("touchstart", onDocPointer, true);
        document.addEventListener("keydown", onDocKey, true);
    });

    onUnmounted(() => {
        document.removeEventListener("mousedown", onDocPointer, true);
        document.removeEventListener("touchstart", onDocPointer, true);
        document.removeEventListener("keydown", onDocKey, true);
    });
}
