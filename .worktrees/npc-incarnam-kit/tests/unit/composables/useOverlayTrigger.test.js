import { describe, expect, it, vi, beforeEach, afterEach } from "vitest";
import { useOverlayTrigger } from "@/Composables/overlay/useOverlayTrigger";
import { DEFAULT_OVERLAY_OPTIONS } from "@/Composables/overlay/overlayConstants";

describe("useOverlayTrigger hover keep-open", () => {
    beforeEach(() => {
        vi.useFakeTimers();
    });

    afterEach(() => {
        vi.useRealTimers();
    });

    it("annule la fermeture si le pointeur entre dans le panneau", () => {
        const onOpen = vi.fn();
        const onClose = vi.fn();
        const trigger = useOverlayTrigger({
            trigger: "hover",
            onOpen,
            onClose,
            onToggle: vi.fn(),
        });

        trigger.onTriggerEnter();
        vi.advanceTimersByTime(DEFAULT_OVERLAY_OPTIONS.hoverOpenDelayMs);
        expect(onOpen).toHaveBeenCalledTimes(1);

        trigger.onTriggerLeave();
        trigger.onPanelEnter();
        vi.advanceTimersByTime(DEFAULT_OVERLAY_OPTIONS.hoverCloseDelayMs + 50);
        expect(onClose).not.toHaveBeenCalled();
    });

    it("ferme après le délai si le pointeur ne rejoint pas le panneau", () => {
        const onClose = vi.fn();
        const trigger = useOverlayTrigger({
            trigger: "hover",
            onOpen: vi.fn(),
            onClose,
            onToggle: vi.fn(),
        });

        trigger.onTriggerLeave();
        vi.advanceTimersByTime(DEFAULT_OVERLAY_OPTIONS.hoverCloseDelayMs - 1);
        expect(onClose).not.toHaveBeenCalled();
        vi.advanceTimersByTime(1);
        expect(onClose).toHaveBeenCalledTimes(1);
    });
});
