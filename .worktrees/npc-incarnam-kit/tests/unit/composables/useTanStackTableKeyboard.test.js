import { describe, expect, it } from "vitest";
import {
    isTableTypingTarget,
    isTableInteractiveTarget,
    shouldIgnoreTableShortcut,
    shouldIgnoreTableRowIntent,
    matchTableEnterIntent,
} from "@/Composables/table/useTanStackTableKeyboard.js";

function fakeEl({ tagName = "DIV", closest = () => null, isContentEditable = false } = {}) {
    return { tagName, closest, isContentEditable };
}

describe("raccourcis tableau", () => {
    it("ignore les raccourcis pendant la saisie", () => {
        const input = fakeEl({ tagName: "INPUT" });
        expect(isTableTypingTarget(input)).toBe(true);
        expect(shouldIgnoreTableShortcut({ target: input })).toBe(true);
    });

    it("laisse Entrée native sur un bouton", () => {
        const button = fakeEl({
            tagName: "BUTTON",
            closest: (sel) => (String(sel).includes("button") ? button : null),
        });
        expect(isTableInteractiveTarget(button)).toBe(true);
        expect(shouldIgnoreTableRowIntent({ target: button })).toBe(true);
    });

    it("mappe Entrée / Ctrl+Entrée / Alt+Entrée", () => {
        expect(matchTableEnterIntent({ key: "Enter", altKey: false, ctrlKey: false, metaKey: false })).toBe("open-view");
        expect(matchTableEnterIntent({ key: "Enter", altKey: false, ctrlKey: true, metaKey: false })).toBe("open-show-page");
        expect(matchTableEnterIntent({ key: "Enter", altKey: true, ctrlKey: false, metaKey: false })).toBe("open-edit");
    });
});
