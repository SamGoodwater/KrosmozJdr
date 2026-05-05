import { computed, ref } from "vue";
import { autoPlacement, autoUpdate, flip, offset, shift, useFloating } from "@floating-ui/vue";
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
    const rawPlacementRef = computed(() =>
        typeof options.placement === "object" && options.placement?.value != null
            ? options.placement.value
            : options.placement || "auto"
    );
    const placementRef = computed(() =>
        String(rawPlacementRef.value || "auto")
            .toLowerCase()
            .replace(/^start$/, "left")
            .replace(/^end$/, "right")
    );
    const allowFlipRef = computed(() => options.allowFlip !== false);
    const isAutoPlacementRef = computed(
        () => placementRef.value === "auto" || placementRef.value === "auto-start" || placementRef.value === "auto-end"
    );
    const floatingPlacementRef = computed(() => (isAutoPlacementRef.value ? "top" : placementRef.value));
    const middlewareRef = computed(() => {
        const spacing = 8;
        if (isAutoPlacementRef.value) {
            const alignment = placementRef.value === "auto-start" ? "start" : placementRef.value === "auto-end" ? "end" : undefined;
            return [
                offset(options.offsetPx ?? 8),
                autoPlacement({
                    padding: spacing,
                    alignment,
                    autoAlignment: true,
                }),
                shift({ padding: spacing, mainAxis: true, crossAxis: true }),
            ];
        }

        const placement = String(placementRef.value || "top");
        const [side, align] = placement.split("-");
        const isHorizontalSide = side === "left" || side === "right";
        const oppositeSide = side === "left" ? "right" : side === "right" ? "left" : side === "top" ? "bottom" : "top";
        const oppositePlacement = align ? `${oppositeSide}-${align}` : oppositeSide;

        const middleware = [
            offset(options.offsetPx ?? 8),
            shift({
                padding: spacing,
                // Evite de recouvrir le trigger sur left/right:
                // on privilegie le flip horizontal et on ne shift que sur l'axe croise (Y).
                mainAxis: !isHorizontalSide,
                crossAxis: isHorizontalSide,
            }),
        ];

        if (allowFlipRef.value) {
            middleware.splice(
                1,
                0,
                flip({
                    padding: spacing,
                    // Force un vrai changement de côté quand il n'y a plus de place
                    // (ex: left -> right) au lieu de rester collé au trigger.
                    fallbackPlacements: [oppositePlacement, oppositeSide],
                })
            );
        }

        return middleware;
    });

    const { floatingStyles } = useFloating(triggerRef, overlayRef, {
        open: options.openRef,
        strategy: "fixed",
        placement: floatingPlacementRef,
        middleware: middlewareRef,
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
