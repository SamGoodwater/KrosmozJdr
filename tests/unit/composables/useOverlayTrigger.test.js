import { describe, expect, it, vi, beforeEach, afterEach } from "vitest";
import { useOverlayTrigger } from "@/Composables/overlay/useOverlayTrigger";
import { DEFAULT_OVERLAY_OPTIONS } from "@/Composables/overlay/overlayConstants";

describe("useOverlayTrigger hover keep-open", () => {
    beforeEach(() => {
        vi.useFakeTimers();
        vi.stubGlobal(
            "matchMedia",
            vi.fn(() => ({
                matches: true,
                media: "",
                addEventListener: vi.fn(),
                removeEventListener: vi.fn(),
            })),
        );
    });

    afterEach(() => {
        vi.useRealTimers();
        vi.unstubAllGlobals();
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

describe("useOverlayTrigger tactile (sans hover fin)", () => {
    beforeEach(() => {
        vi.stubGlobal(
            "matchMedia",
            vi.fn(() => ({
                matches: false,
                media: "",
                addEventListener: vi.fn(),
                removeEventListener: vi.fn(),
            })),
        );
    });

    afterEach(() => {
        vi.unstubAllGlobals();
    });

    it("convertit hover forcé en clic quand le pointeur n’a pas de vrai hover", () => {
        const onToggle = vi.fn();
        const trigger = useOverlayTrigger({
            trigger: "hover",
            onOpen: vi.fn(),
            onClose: vi.fn(),
            onToggle,
        });

        expect(trigger.computedTrigger.value).toBe("click");
        trigger.onTriggerEnter();
        expect(onToggle).not.toHaveBeenCalled();

        const event = { preventDefault: vi.fn() };
        trigger.onTriggerClick(event);
        expect(event.preventDefault).toHaveBeenCalled();
        expect(onToggle).toHaveBeenCalledTimes(1);
    });

    it("auto text/html → clic sans hover fin", () => {
        const trigger = useOverlayTrigger({
            trigger: "auto",
            contentKind: "text",
            onOpen: vi.fn(),
            onClose: vi.fn(),
            onToggle: vi.fn(),
        });
        expect(trigger.computedTrigger.value).toBe("click");
    });
});
