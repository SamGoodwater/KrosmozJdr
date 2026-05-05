import { useOverlayService } from "@/Composables/overlay/useOverlayService";
import { useOverlayPosition } from "@/Composables/overlay/useOverlayPosition";
import { useOverlayTrigger } from "@/Composables/overlay/useOverlayTrigger";
import { useOverlayDismiss } from "@/Composables/overlay/useOverlayDismiss";
import { useOverlayA11y } from "@/Composables/overlay/useOverlayA11y";

/**
 * Facade unique pour brancher un overlay dans un composant trigger.
 *
 * @param {Parameters<typeof useOverlayService>[0]} options
 */
export function useOverlay(options) {
    const service = useOverlayService(options);
    const position = useOverlayPosition({
        openRef: service.isOpen,
        placement: options.placement || "top",
        offsetPx: options.offsetPx ?? 8,
    });
    const a11y = useOverlayA11y({
        openRef: service.isOpen,
        interactive: Boolean(options.interactive),
        focusTrap: Boolean(options.focusTrap),
    });
    const trigger = useOverlayTrigger({
        trigger: options.trigger || "auto",
        contentKind: service.inferredKind,
        onOpen: () => service.open(),
        onClose: () => service.close("trigger"),
        onToggle: () => service.toggle(),
    });

    useOverlayDismiss({
        openRef: service.isOpen,
        triggerRef: position.triggerRef,
        panelRef: position.overlayRef,
        closeOnOutside: options.closeOnOutside,
        closeOnEscape: options.closeOnEscape,
        onClose: (reason) => service.close(reason),
    });

    return {
        service,
        position,
        trigger,
        a11y,
    };
}
