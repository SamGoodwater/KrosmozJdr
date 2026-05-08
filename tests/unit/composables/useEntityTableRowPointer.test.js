import { describe, expect, it } from "vitest";
import {
    classifyRowPointerModifiers,
    isRowInteractiveTarget,
} from "@/Composables/table/useEntityTableRowPointer";

describe("useEntityTableRowPointer", () => {
    it("classe les modificateurs de clic selon le contrat minimal/line", () => {
        expect(classifyRowPointerModifiers(null)).toBe("default");
        expect(classifyRowPointerModifiers({ ctrlKey: true })).toBe("view");
        expect(classifyRowPointerModifiers({ metaKey: true })).toBe("view");
        expect(classifyRowPointerModifiers({ altKey: true })).toBe("edit");
        expect(classifyRowPointerModifiers({})).toBe("default");
    });

    it("ignore les cibles interactives", () => {
        const root = document.createElement("div");
        const button = document.createElement("button");
        const text = document.createElement("span");
        root.append(button, text);

        expect(isRowInteractiveTarget({ target: button })).toBe(true);
        expect(isRowInteractiveTarget({ target: text })).toBe(false);
    });
});
