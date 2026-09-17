import { describe, it, expect } from "vitest";
import { isOverlayOutsideClickIgnored } from "@/Composables/overlay/useOverlayDismiss";

describe("isOverlayOutsideClickIgnored", () => {
    it("ignore un clic dans un menu dropdown téléporté", () => {
        const menu = document.createElement("div");
        menu.setAttribute("data-dropdown-id", "dd-1");
        const item = document.createElement("button");
        menu.appendChild(item);
        document.body.appendChild(menu);

        expect(isOverlayOutsideClickIgnored(item)).toBe(true);
        expect(isOverlayOutsideClickIgnored(document.body)).toBe(false);

        menu.remove();
    });
});
