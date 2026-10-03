import { describe, expect, it } from "vitest";
import { ref } from "vue";
import { useOverlayA11y } from "@/Composables/overlay/useOverlayA11y";

describe("useOverlayA11y", () => {
    it("expose un déclencheur focalisable avec role button", () => {
        const openRef = ref(false);
        const { triggerAttrs } = useOverlayA11y({ openRef, interactive: false });

        expect(triggerAttrs.value.tabindex).toBe("0");
        expect(triggerAttrs.value.role).toBe("button");
        expect(triggerAttrs.value["aria-expanded"]).toBe("false");
        expect(triggerAttrs.value["aria-describedby"]).toBeUndefined();
    });

    it("associe aria-describedby au panneau lorsque le tooltip est ouvert", () => {
        const openRef = ref(true);
        const { triggerAttrs, panelAttrs } = useOverlayA11y({ openRef, interactive: false });

        expect(triggerAttrs.value["aria-describedby"]).toBe(panelAttrs.value.id);
    });

    it("Entrée ouvre un overlay hover et Espace le referme", () => {
        const openRef = ref(false);
        const { onTriggerKeydown } = useOverlayA11y({ openRef, interactive: false });
        let opens = 0;
        let closes = 0;
        const handlers = {
            open: () => {
                opens += 1;
                openRef.value = true;
            },
            close: () => {
                closes += 1;
                openRef.value = false;
            },
            toggle: () => {},
            triggerMode: "hover",
        };

        onTriggerKeydown({ key: "Enter", preventDefault: () => {} }, handlers);
        expect(opens).toBe(1);

        onTriggerKeydown({ key: " ", preventDefault: () => {} }, handlers);
        expect(closes).toBe(1);
    });
});
