import { describe, expect, it } from "vitest";
import {
    isWorkshopMode,
    propertyKeysFromConfig,
    propertyPayloadFromSelection,
    workshopUpdateMode,
} from "@/utils/scrapping/workshopMode";

describe("workshopMode", () => {
    it("mappe Compléter vers update_mode ignore", () => {
        expect(workshopUpdateMode({ mode: "complete" })).toBe("ignore");
        expect(isWorkshopMode("complete")).toBe(true);
    });

    it("mappe Mettre à jour vers auto_update par défaut et force si auto_update est ignoré", () => {
        expect(workshopUpdateMode({ mode: "update" })).toBe("auto_update");
        expect(workshopUpdateMode({ mode: "update", respectAutoUpdate: true })).toBe("auto_update");
        expect(workshopUpdateMode({ mode: "update", respectAutoUpdate: false })).toBe("force");
    });

    it("mappe Récupérer vers draft_raw_auto_update", () => {
        expect(workshopUpdateMode({ mode: "retrieve" })).toBe("draft_raw_auto_update");
        expect(workshopUpdateMode({})).toBe("draft_raw_auto_update");
    });

    it("ajoute image aux clés de propriétés", () => {
        expect(propertyKeysFromConfig({ monster: { comparisonKeys: ["name", "level"] } }, "monster")).toEqual([
            "name",
            "level",
            "image",
        ]);
    });

    it("envoie une whitelist vide si tout est coché, y compris image", () => {
        const all = ["name", "image"];
        expect(propertyPayloadFromSelection(all, all)).toEqual({
            property_whitelist: [],
            with_images: true,
        });
        expect(propertyPayloadFromSelection(all, ["name"])).toEqual({
            property_whitelist: ["name"],
            with_images: false,
        });
    });
});
