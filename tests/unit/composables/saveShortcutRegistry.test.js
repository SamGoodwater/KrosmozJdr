import { describe, it, expect, afterEach } from "vitest";
import { registerSaveShortcut } from "../../resources/js/Composables/utils/saveShortcutRegistry.js";

/**
 * @returns {void}
 */
function dispatchCtrlS() {
    window.dispatchEvent(
        new KeyboardEvent("keydown", {
            key: "s",
            ctrlKey: true,
            bubbles: true,
            cancelable: true,
        }),
    );
}

describe("registerSaveShortcut", () => {
    /** @type {Array<() => void>} */
    const unregisters = [];

    afterEach(() => {
        while (unregisters.length) {
            unregisters.pop()();
        }
    });

    it("n’appelle que le handler au sommet de la pile (LIFO)", () => {
        const log = [];
        unregisters.push(registerSaveShortcut(() => log.push("first")));
        unregisters.push(registerSaveShortcut(() => log.push("second")));
        dispatchCtrlS();
        expect(log).toEqual(["second"]);
        unregisters.pop()();
        dispatchCtrlS();
        expect(log).toEqual(["second", "first"]);
    });
});
