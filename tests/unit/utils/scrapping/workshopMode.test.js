import { describe, expect, it } from "vitest";
import {
    IA_GENERATION_PAGE_PATH,
    isWorkshopMode,
    propertyKeysFromConfig,
    propertyPayloadFromSelection,
    workshopModeFromUrl,
    workshopPageHref,
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

    it("lit le mode depuis ?mode= et ignore une valeur inconnue", () => {
        expect(workshopModeFromUrl("/admin/content/dofusdb?mode=complete")).toBe("complete");
        expect(workshopModeFromUrl("/admin/content/dofusdb?mode=update")).toBe("update");
        expect(workshopModeFromUrl("/admin/content/dofusdb?mode=nope")).toBe("retrieve");
        expect(workshopModeFromUrl("/admin/content/dofusdb")).toBe("retrieve");
        expect(workshopPageHref("update")).toBe("/admin/content/dofusdb?mode=update");
        expect(workshopPageHref("nope")).toBe("/admin/content/dofusdb?mode=retrieve");
        expect(IA_GENERATION_PAGE_PATH).toBe("/admin/content/ia-generation");
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
