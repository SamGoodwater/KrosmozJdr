import { computed, ref } from "vue";
import { autoUpdate, flip, offset, shift, useFloating } from "@floating-ui/vue";
import { resolveTooltipTeleportTarget } from "@/Composables/ui/resolveTooltipTeleportTarget";

/**
 * @param {{
 * openRef: import('vue').Ref<boolean>,
 * placement?: import('vue').Ref<string>|string,
 * offsetPx?: number
 * }} options
 */
export function useOverlayPosition(options) {
    const triggerRef = ref(null);
    const overlayRef = ref(null);
    const placementRef = computed(() =>
        typeof options.placement === "object" && options.placement?.value != null
            ? options.placement.value
            : options.placement || "top"
    );
    const { floatingStyles } = useFloating(triggerRef, overlayRef, {
        open: options.openRef,
        strategy: "fixed",
        placement: placementRef,
        middleware: [offset(options.offsetPx ?? 8), flip(), shift({ padding: 8 })],
        whileElementsMounted: autoUpdate,
    });

    const teleportTarget = computed(() => {
        if (!options.openRef.value) {
            return typeof document !== "undefined" ? document.body : "body";
        }
        return resolveTooltipTeleportTarget(triggerRef.value);
    });

    return {
        triggerRef,
        overlayRef,
        floatingStyles,
        teleportTarget,
    };
}
