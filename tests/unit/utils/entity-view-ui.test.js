/**
 * getEntityFieldTooltip / formatCharacteristicLimitLine
 */

import { describe, expect, it } from "vitest";
import {
    formatCharacteristicLimitLine,
    getEntityFieldTooltip,
} from "@/Utils/Entity/entity-view-ui";

describe("getEntityFieldTooltip", () => {
    it("lit helper plat (items / consommables)", () => {
        expect(
            getEntityFieldTooltip({ helper: "Emplacement (anneau, cape, arme…)" }),
        ).toBe("Emplacement (anneau, cape, arme…)");
    });

    it("lit general.tooltip (ressources)", () => {
        expect(
            getEntityFieldTooltip({
                general: { tooltip: "Catégorie métier de la ressource (bois, minerai, plante…)." },
            }),
        ).toBe("Catégorie métier de la ressource (bois, minerai, plante…).");
    });

    it("ignore le help d’édition si un helper d’affichage existe", () => {
        expect(
            getEntityFieldTooltip({
                helper: "Rareté, de Commun (0) à Unique (5).",
                edit: { form: { help: "Rareté stockée en base comme entier (0..5)." } },
            }),
        ).toBe("Rareté, de Commun (0) à Unique (5).");
    });
});

describe("formatCharacteristicLimitLine", () => {
    it("formate min et max", () => {
        expect(formatCharacteristicLimitLine({ limit_min: "0", limit_max: "12" })).toBe(
            "Limites : 0 à 12.",
        );
    });

    it("formate un seul bornage", () => {
        expect(formatCharacteristicLimitLine({ limit_min: "1" })).toBe("Minimum : 1.");
        expect(formatCharacteristicLimitLine({ max: "20" })).toBe("Maximum : 20.");
    });

    it("ignore les définitions vides", () => {
        expect(formatCharacteristicLimitLine(null)).toBe("");
        expect(formatCharacteristicLimitLine({})).toBe("");
    });
});
