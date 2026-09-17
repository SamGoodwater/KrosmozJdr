import { describe, it, expect, beforeEach } from "vitest";
import { useTanStackTablePreferences } from "@/Composables/table/useTanStackTablePreferences.js";

const KEY = "tanstack_table_prefs_consumables.index";

describe("useTanStackTablePreferences", () => {
    beforeEach(() => {
        localStorage.clear();
    });

    it("ouvre en vue minimale sans préférences enregistrées", () => {
        const prefs = useTanStackTablePreferences("consumables.index", {
            displayMode: "minimal",
            pageSize: 25,
        });
        expect(prefs.displayMode.value).toBe("minimal");
    });

    it("migre les prefs v3 en mode ligne vers la vue minimale", () => {
        localStorage.setItem(
            KEY,
            JSON.stringify({
                version: 3,
                visibleColumns: { name: true },
                touchedColumns: ["name"],
                pageSize: 50,
                displayMode: "line",
                quickEditEnabled: false,
                sorting: [],
            }),
        );

        const prefs = useTanStackTablePreferences("consumables.index", {
            displayMode: "minimal",
            pageSize: 25,
        });

        expect(prefs.displayMode.value).toBe("minimal");
        expect(prefs.pageSize.value).toBe(50);
        expect(prefs.visibleColumns.value).toEqual({ name: true });

        const stored = JSON.parse(localStorage.getItem(KEY));
        expect(stored.version).toBe(4);
        expect(stored.displayMode).toBe("minimal");
    });

    it("conserve le mode colonnes déjà choisi", () => {
        localStorage.setItem(
            KEY,
            JSON.stringify({
                version: 3,
                visibleColumns: {},
                touchedColumns: [],
                pageSize: 25,
                displayMode: "table",
                quickEditEnabled: false,
                sorting: [],
            }),
        );

        const prefs = useTanStackTablePreferences("consumables.index", {
            displayMode: "minimal",
        });
        expect(prefs.displayMode.value).toBe("table");
    });
});
