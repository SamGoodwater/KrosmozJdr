import { describe, expect, it } from "vitest";
import { classifyRowPointerModifiers } from "@/Composables/table/useEntityTableRowPointer";

describe("classifyRowPointerModifiers (Q8)", () => {
    it("maps Ctrl/Meta to page navigation", () => {
        expect(
            classifyRowPointerModifiers({ ctrlKey: true, metaKey: false, altKey: false }),
        ).toBe("page");
        expect(
            classifyRowPointerModifiers({ ctrlKey: false, metaKey: true, altKey: false }),
        ).toBe("page");
    });

    it("maps Alt to edit", () => {
        expect(
            classifyRowPointerModifiers({ ctrlKey: false, metaKey: false, altKey: true }),
        ).toBe("edit");
    });

    it("defaults to selection", () => {
        expect(
            classifyRowPointerModifiers({ ctrlKey: false, metaKey: false, altKey: false }),
        ).toBe("default");
    });
});
